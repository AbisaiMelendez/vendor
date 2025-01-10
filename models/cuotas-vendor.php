<?php

include 'conex.php';

try {
    // Crear una nueva conexión PDO
    $conn3 = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $conn3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar y ejecutar la consulta
    $queryCuotas = "SELECT * FROM installments;";
    $stmtCuotas = $conn3->prepare($queryCuotas);
    $stmtCuotas->execute();

    // Obtener todos los resultados como un array asociativo
    $resultCuotas = $stmtCuotas->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los resultados en formato JSON
    if ($resultCuotas) {

        $dataCode = json_encode($resultCuotas); // Devolver resultados como JSON

    } else {
        echo json_encode(['message' => 'No data found']); // En caso de que no haya resultados
    }

} catch (PDOException $e) {
    // Devolver el error en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
}
