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

//configuracion de envio de correo
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);


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

    $installmentDetails = ""; // Inicializar detalles de cuotas para el correo


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


        // Agregar detalles de cada cuota al texto del correo
        $installmentDetails .= "Cuota " . ($i + 1) . ": $" . number_format($installmentAmount, 2) . " con vencimiento el " . $formattedDate . "\n";
    }

    echo "Cuotas guardadas correctamente.";

      // Enviar correo con los detalles de las cuotas
      sendInstallmentsEmail($idbatch, $total, $installmentsCount, $installmentDetails);
}


function sendInstallmentsEmail($idbatch, $total, $installmentsCount, $installmentDetails)
{
    try {
        $mail = new PHPMailer(true);

        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // Cambiar según tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'vendor-noreply@surgepays.sv';
        $mail->Password = 'D.460087689989az';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('vendor-noreply@surgepays.sv', 'TEST vendor');
        $mail->addAddress('it@surgepays.sv');

        // Agregar destinatarios como copia (CC)
        $mail->addCC('etrejo@surgepays.com');
        $mail->addCC('jsegovia@surgepays.sv');

        // Asunto y cuerpo del correo
        $mail->Subject = 'Detalles de las cuotas de pago';
        $mail->Body = "ID del batch: $idbatch\n"
                    . "Total: $" . number_format($total, 2) . "\n"
                    . "Número de cuotas: $installmentsCount\n\n"
                    . "Detalles de las cuotas:\n"
                    . $installmentDetails;

        // Enviar correo
        $mail->send();
        echo 'Correo con las cuotas enviado correctamente.';
    } catch (Exception $e) {
        echo "Error al enviar correo: {$mail->ErrorInfo}";
    }
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


function calculatePaymentDates($installmentsCount, $purchaseDate)
{
    $dates = [];

    // Generar fechas dinámicamente según los viernes específicos del calendario
    function generatePredefinedPaymentDates($year)
    {
        $predefinedPaymentDates = [
            '2025-01' => ['2025-01-10', '2025-01-24'],
            '2025-02' => ['2025-02-07', '2025-02-21'],
            '2025-03' => ['2025-03-07', '2025-03-21'],
            '2025-04' => ['2025-04-04', '2025-04-18'],
            '2025-05' => ['2025-05-02', '2025-05-16', '2025-05-30'],
            '2025-06' => ['2025-06-13', '2025-06-27'],
            '2025-07' => ['2025-07-11', '2025-07-25'],
            '2025-08' => ['2025-08-08', '2025-08-22'],
            '2025-09' => ['2025-09-05', '2025-09-19'],
            '2025-10' => ['2025-10-03', '2025-10-17', '2025-10-31'],
            '2025-11' => ['2025-11-14', '2025-11-28'],
            '2025-12' => ['2025-12-12', '2025-12-26'],
        ];

        return $predefinedPaymentDates;
    }

    $year = $purchaseDate->format('Y');
    $predefinedPaymentDates = generatePredefinedPaymentDates($year);

    // Encontrar la primera fecha de corte válida
    $foundStart = false;
    foreach ($predefinedPaymentDates as $month => $datesInMonth) {
        foreach ($datesInMonth as $validDate) {
            $validDateTime = new DateTime($validDate);
            if ($validDateTime >= $purchaseDate) {
                $dates[] = $validDateTime;
                $foundStart = true;
                break 2; // Salir cuando encontremos la primera fecha válida
            }
        }
    }

    if (!$foundStart) {
        return $dates; // No hay fechas válidas después de la fecha de compra
    }

    // Generar las siguientes cuotas hasta completar el número requerido
    while (count($dates) < $installmentsCount) {
        $lastDate = end($dates);
        $currentMonth = $lastDate->format('Y-m');

        if (isset($predefinedPaymentDates[$currentMonth])) {
            foreach ($predefinedPaymentDates[$currentMonth] as $validDate) {
                $validDateTime = new DateTime($validDate);
                if ($validDateTime > $lastDate) { // Fechas posteriores a la última seleccionada
                    $dates[] = $validDateTime;
                    if (count($dates) >= $installmentsCount) {
                        break 2; // Salir si ya alcanzamos el número requerido
                    }
                }
            }
        }

        // Si no encontramos más fechas en el mes actual, avanzar al siguiente mes
        if (count($dates) < $installmentsCount) {
            $nextMonth = (new DateTime($lastDate->format('Y-m-01')))->modify('+1 month');
            $nextMonthKey = $nextMonth->format('Y-m');

            if (isset($predefinedPaymentDates[$nextMonthKey])) {
                foreach ($predefinedPaymentDates[$nextMonthKey] as $validDate) {
                    $dates[] = new DateTime($validDate);
                    if (count($dates) >= $installmentsCount) {
                        break 2; // Salir si ya alcanzamos el número requerido
                    }
                }
            }
        }
    }

    return $dates;
}

// Datos de ejemplo
$data = $_POST;

saveTransaction($data);


try {
    $mail = new PHPMailer(true);

    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com'; // Cambiar según tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'vendor-noreply@surgepays.sv';
    $mail->Password = 'D.460087689989az';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configuración del correo
    $mail->setFrom('vendor-noreply@surgepays.sv', 'TEST vendor');
    $mail->addAddress('it@surgepays.sv');

    // Agregar destinatarios como copia (CC)
    $mail->addCC('etrejo@surgepays.com');
    $mail->addCC('jsegovia@surgepays.sv');

    // Agregar destinatarios como copia oculta (BCC)
    // $mail->addBCC('auditoria@surgepays.sv');


    $mail->Subject = 'Prueba de PHPMailer';
    $mail->Body = 'Este es un mensaje de prueba enviado con PHPMailer.' . $data['nameVendor'] . 'dates' . $dates;

    // Enviar correo
    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar correo: {$mail->ErrorInfo}";
}

