
<?php
// Configuración de la conexión a la base de datos
$host = '172.24.13.20';
$db = 'hr_surge';
$user = 'hrsurge';
$password = '01cNSZZEwK1t';

try {
    // Crear una nueva conexión PDO
    $conn5 = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

    // Establecer el modo de error de PDO a excepción
    $conn5->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar y ejecutar la consulta
    $query6 = "
            SELECT 
            i.idbatch AS installment_id, 
            i.installment_number, 
            i.amount, 
            i.status, 
            i.due_date, 
            i.paid, 
            vt.product, 
            i.payment_date, 
            vt.badge, 
            CONCAT(vt.name) AS fullname, 
            vt.job AS job, 
            vt.idVendor, 
            vt.nameVendor, 
            u.fullname AS fullnameVendor, 
            u.number_account, 
            u.comments,
            vt.created_at AS payment_created_at
        FROM 
            installments i
        JOIN 
            vendor_transaccion vt 
            ON i.idbatch = vt.idbatch
        LEFT JOIN 
            users u 
            ON vt.idVendor = u.userId
        ORDER BY 
            i.idbatch, vt.created_at;
    ";

    $stmt7 = $conn5->prepare($query6);
    $stmt7->execute();

    // Obtener todos los resultados
    $results7 = $stmt7->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejar errores
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Cerrar la conexión
$conn5 = null;
?>
