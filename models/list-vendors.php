<?php
// Configuración de la conexión a la base de datos
$host = '172.24.13.20';
$db = 'hr_surge';
$user = 'hrsurge';
$password = '01cNSZZEwK1t';

//header('Content-Type: application/json');

try {
    // Crear una nueva conexión PDO
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar y ejecutar la consulta
    $query = "
    SELECT * 
    FROM users
    WHERE userLevel = 2 
    OR userLevel LIKE '%Vendor%'
    ORDER BY userId DESC;
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Obtener todos los resultados y enviarlos en formato JSON
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  
    //echo json_encode($results);

} catch (PDOException $e) {
    // Devolver el error en formato JSON
    //echo json_encode(['error' => $e->getMessage()]);
}

// Cerrar la conexión
$conn = null;
