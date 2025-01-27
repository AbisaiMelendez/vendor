<?php
// Configuración de la conexión a la base de datos

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = '172.24.13.20';
$db = 'hr_surge';
$user = 'hrsurge';
$password = '01cNSZZEwK1t';

$mysqli = new mysqli($host, $user, $password, $db);
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

function saveTransaction($data)
{
    global $mysqli;

    $badge = trim($data['badge']);
    if (empty($badge)) {
        // Redirigir si 'badge' está vacío
        header('Location: ?page=vendor_client');
        exit;
    }
    $total = $data['total'];
    $currentCredit = 0;

    // Extraer el número de cuotas desde payment_option
    $installmentsCount = (int) substr(trim($data['payment_option']), 0, 1);

    // Verificar si el badge existe en la tabla credits
    $stmt = $mysqli->prepare("SELECT current_credit FROM credits WHERE badge = ?");
    $stmt->bind_param("s", $badge);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizar current_credit si el badge existe
        $row = $result->fetch_assoc();
        $currentCredit = $row['current_credit'] - $total;

        if ($currentCredit < 0) {
            die("Error: El total excede el crédito actual.");
        }

        $updateStmt = $mysqli->prepare("UPDATE credits SET current_credit = ? WHERE badge = ?");
        $updateStmt->bind_param("ds", $currentCredit, $badge);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Insertar un nuevo registro si el badge no existe
        $initialCredit = 100;
        $currentCredit = $initialCredit - $total;

        if ($currentCredit < 0) {
            die("Error: El total excede el crédito inicial.");
        }

        $insertStmt = $mysqli->prepare("INSERT INTO credits (badge, initial_credit, current_credit) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sdd", $badge, $initialCredit, $currentCredit);
        $insertStmt->execute();
        $insertStmt->close();
    }

    $stmt->close();

    // Insertar los datos en la tabla vendor_transaccion
    $idbatch = uniqid(); // Generar un identificador único para el batch
    $insertTransactionStmt = $mysqli->prepare(
        "INSERT INTO vendor_transaccion (idbatch, idVendor, nameVendor, badge, name, job, company, quantity, price, total, product, payment_option) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    $insertTransactionStmt->bind_param(
        "sissssiidsss",
        $idbatch,
        $data['idVendor'],
        $data['nameVendor'],
        $badge,
        $data['name'],
        $data['job'],
        $data['company'],
        $data['quantity'],
        $data['price'],
        $data['total'],
        $data['product'],
        $data['payment_option']
    );
    
    $insertTransactionStmt->execute();
    $insertTransactionStmt->close();

    // Guardar cuotas en la tabla installments
    saveInstallments($idbatch, $total, $installmentsCount);

    echo "Transacción guardada correctamente. Crédito actual: $currentCredit";

    header("Location: /vendor/?page=vendor_client&success=ok");
    //header("Location: /hr-surge.com/vendor/?page=vendor_client&success=ok");
}

function saveInstallments($idbatch, $total, $installmentsCount)
{
    global $mysqli;

    $installmentAmount = $total / $installmentsCount;
    $today = new DateTime();

    $dates = calculatePaymentDates($installmentsCount, $today);

    foreach ($dates as $i => $dueDate) {
        $stmt = $mysqli->prepare(
            "INSERT INTO installments (idbatch, installment_number, amount, due_date, paid, payment_date) 
            VALUES (?, ?, ?, ?, ?, NULL)"
        );
        $paid = 0; // Inicialmente no pagado
        $formattedDate = $dueDate->format('Y-m-d'); // Asignar a una variable antes de bind_param
        $installmentNumber = $i + 1; // Crear variable para evitar errores de referencia

        $stmt->bind_param("sidsi", $idbatch, $installmentNumber, $installmentAmount, $formattedDate, $paid);
        $stmt->execute();
        $stmt->close();
    }

    echo "Cuotas guardadas correctamente.";
}

// // estas fechas son segun quincenan antepenultimo dia del mes
// function calculatePaymentDates($installmentsCount, $startDate)
// {
//     $dates = [];
//     $currentDate = clone $startDate;

//     for ($i = 0; $i < $installmentsCount; $i++) {
//         if ((int)$currentDate->format('d') > 14) {
//             // Si estamos después del 14, primera cuota al penúltimo día del mes
//             $currentDate->modify('last day of this month');
//             $currentDate->modify('-2 day');
//         } else {
//             // Si estamos antes o en el 14, primera cuota al día 14
//             $currentDate = new DateTime($currentDate->format('Y-m-14'));
//         }

//         $dates[] = clone $currentDate;

//         // Alternar entre el 14 y el penúltimo día del siguiente mes
//         if ((int)$currentDate->format('d') === 14) {
//             $currentDate->modify('last day of this month');
//             $currentDate->modify('-2 day');
//         } else {
//             $currentDate->modify('first day of next month');
//             $currentDate = new DateTime($currentDate->format('Y-m-14'));
//         }
//     }

//     return $dates;
// }

// estos son un viernes si y un viernes no.

function calculatePaymentDates($installmentsCount, $startDate)
{
    $dates = [];
    $currentDate = clone $startDate;

    // Generar dinámicamente las fechas válidas de pago
    function generateValidPaymentDates($year)
    {
        $validPaymentDates = [];

        // Generar fechas para cada mes del año
        for ($month = 1; $month <= 12; $month++) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT); // Asegurar formato MM

            // Encontrar todos los viernes del mes
            $firstDayOfMonth = new DateTime("$year-$monthStr-01");
            $lastDayOfMonth = (clone $firstDayOfMonth)->modify('last day of this month');

            $currentDay = clone $firstDayOfMonth;
            while ($currentDay <= $lastDayOfMonth) {
                if ($currentDay->format('N') == 5) { // N=5 es viernes
                    $validPaymentDates["$year-$monthStr"][] = $currentDay->format('Y-m-d');
                }
                $currentDay->modify('+1 day');
            }
        }

        return $validPaymentDates;
    }

    $year = $startDate->format('Y');
    $validPaymentDates = generateValidPaymentDates($year);

    for ($i = 0; $i < $installmentsCount; $i++) {
        $currentMonth = $currentDate->format('Y-m');

        if (isset($validPaymentDates[$currentMonth])) {
            foreach ($validPaymentDates[$currentMonth] as $validDate) {
                $validDateTime = new DateTime($validDate);
                if ($validDateTime >= $currentDate) {
                    $dates[] = $validDateTime;
                    $currentDate = clone $validDateTime;
                    $currentDate->modify('+1 day');
                    break;
                }
            }
        }

        // Si no hay más fechas válidas en el mes, avanzar al siguiente mes
        if (count($dates) < $i + 1) {
            $currentDate->modify('first day of next month');
            while (!isset($validPaymentDates[$currentDate->format('Y-m')])) {
                $currentDate->modify('first day of next month');
            }
            $currentDate = new DateTime($validPaymentDates[$currentDate->format('Y-m')][0]);
        }
    }

    return $dates;
}



// Datos de ejemplo
$data = $_POST;

saveTransaction($data);
