<?php


include 'conex.php';

//print_r($data);


try {
    // Crear una nueva conexión PDO
    $conn2 = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar y ejecutar la consulta
    $queryPaid = "SELECT SUM(amount) AS amount FROM hr_surge.installments WHERE status = 'Paid';";
    $stmtPaid = $conn2->prepare($queryPaid);
    $stmtPaid->execute();

    // Obtener el resultado
    $resultPaid = $stmtPaid->fetch(PDO::FETCH_ASSOC);

    // Imprimir solo el valor de la suma
    if ($resultPaid && isset($resultPaid['amount'])) {
       // echo $resultPaid['amount']; // Imprime el valor total de 'amount', por ejemplo, "905.00"
    } else {
        $resultPaid['amount'] = 00.00;
      //  echo "0"; // En caso de que no haya resultados
    }

} catch (PDOException $e) {
    // Devolver el error en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Cerrar la conexión
    $conn2 = null;
}
