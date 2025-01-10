<?php


include 'conex.php';

//print_r($data);


try {
    // Crear una nueva conexión PDO
    $conn2 = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar y ejecutar la consulta
    $queryPending = "SELECT SUM(amount) AS amount FROM hr_surge.installments WHERE status = 'Pending';";
    $stmtPending = $conn2->prepare($queryPending);
    $stmtPending->execute();

    // Obtener el resultado
    $resultPending = $stmtPending->fetch(PDO::FETCH_ASSOC);

    // Imprimir solo el valor de la suma
    if ($resultPending && isset($resultPending['amount'])) {
       // echo $resultPaid['amount']; // Imprime el valor total de 'amount', por ejemplo, "905.00"
    } else {
       // echo "0"; // En caso de que no haya resultados
    }

} catch (PDOException $e) {
    // Devolver el error en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Cerrar la conexión
    $conn2 = null;
}
