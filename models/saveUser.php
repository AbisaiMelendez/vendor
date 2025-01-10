<?php
// Configuración de la conexión a la base de datos
$host = '172.24.13.20';
$db = 'hr_surge';
$user = 'hrsurge';
$password = '01cNSZZEwK1t';

try {
    // Crear una nueva conexión PDO
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Manejar el error en la conexión
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
