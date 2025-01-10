<?php


include 'conex.php';

//print_r($data);


try {
    // Crear una nueva conexión PDO
    $conn2 = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar y ejecutar la consulta
    $queryCredit = "SELECT SUM(total) AS total_sum FROM vendor_transaccion;";
    $stmtCredit = $conn2->prepare($queryCredit);
    $stmtCredit->execute();

    // Obtener el resultado
    $result = $stmtCredit->fetch(PDO::FETCH_ASSOC);

    // Imprimir solo el valor de la suma
    if ($result && isset($result['total_sum'])) {
       // echo $result['total_sum']; // Esto imprimirá solo el valor, por ejemplo, "905.00"
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
