<?php
// Configuración de la conexión a la base de datos
$host = '172.24.13.20';
$db = 'hr_surgepays';
$user = 'hrsurge';
$password = '01cNSZZEwK1t';

//header('Content-Type: application/json');

try {
    // Crear una nueva conexión PDO
    $connEmployee = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $connEmployee->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar y ejecutar la consulta
    $query = "SELECT * FROM bills";
    $stmt = $connEmployee->prepare($query);
    $stmt->execute();

    // Obtener todos los resultados y enviarlos en formato JSON
    $resultsBills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $dataBill = json_encode($resultsBills);
   // echo $data;
    //echo json_encode($results);

} catch (PDOException $e) {
    // Devolver el error en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
}

// Cerrar la conexión
$conn = null;
?>
