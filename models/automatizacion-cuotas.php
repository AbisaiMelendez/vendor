<?php
// Configuración de la conexión a la base de datos
$host = '172.24.13.20';
$db = 'hr_surge';
$user = 'hrsurge';
$password = '01cNSZZEwK1t';

try {
    // Crear la conexión con PDO
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la fecha actual
    $currentDate = date('Y-m-d');

    // Actualizar el campo 'status' a 'paid' donde 'due_date' es igual o menor a la fecha actual
    $sqlUpdateInstallments = "UPDATE installments 
                              SET status = 'paid'
                              WHERE status ='pending' AND due_date <= :currentDate" ;

    $stmt = $pdo->prepare($sqlUpdateInstallments);
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->execute();

    // Consultar las filas que se actualizaron en installments
    $sqlSelectInstallments = "SELECT idbatch, amount FROM installments WHERE due_date <= :currentDate";
    $stmt = $pdo->prepare($sqlSelectInstallments);
    $stmt->bindParam(':currentDate', $currentDate);
    $stmt->execute();
    $installments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar cada fila para actualizar las tablas relacionadas
    foreach ($installments as $installment) {
        $idbatch = $installment['idbatch'];
        $amount = $installment['amount'];

        // Buscar el 'badge' en la tabla vendor_transaccion usando el 'idbatch'
        $sqlSelectBadge = "SELECT badge FROM vendor_transaccion WHERE idbatch = :idbatch";
        $stmt = $pdo->prepare($sqlSelectBadge);
        $stmt->bindParam(':idbatch', $idbatch);
        $stmt->execute();
        $vendorTransaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vendorTransaction) {
            $badge = $vendorTransaction['badge'];

            // Actualizar el campo 'current_credit' en la tabla credit sumando el 'amount'
            $sqlUpdateCredit = "UPDATE credits 
                                SET current_credit = current_credit + :amount
                                WHERE badge = :badge";
            $stmt = $pdo->prepare($sqlUpdateCredit);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':badge', $badge);
            $stmt->execute();

            echo 'se actualizaron registros';
        }else{
            echo '0 registros actualizados';
        }
    }

    echo "Actualización completa.";

} catch (PDOException $e) {
    // Manejo de errores
    echo "Error en la conexión o consulta: " . $e->getMessage();
}
?>
