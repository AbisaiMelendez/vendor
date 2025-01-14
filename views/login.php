<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de la conexión a la base de datos
$host = '172.24.13.20';
$dbname = 'hr_surge';
$user = 'hrsurge';
$password = '01cNSZZEwK1t';

try {
    // Crear la conexión a la base de datos
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}

// Manejar errores
$error = '';

// Procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        try {
            // Verificar si el usuario existe en la base de datos
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Guardar los datos del usuario en la sesión
                $_SESSION['user'] = [
                    'userId' => $user['userId'],
                    'name_vendor' => $user['name_vendor'],
                    'username' => $user['username'],
                    'level' => $user['userLevel'],
                ];

                // Redirigir al dashboard
                if($_SESSION['user']['level'] == '1'){
                    
                    header('Location: ?page=dashboard');
                }else{
                    header('Location: ?page=vendor_client');
                }
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        } catch (Exception $e) {
            $error = 'Error al procesar la solicitud. Inténtalo de nuevo.';
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Iniciar Sesión</title>
</head>

<body class="bg-gray-900 flex items-center justify-center h-screen">
    <div class="bg-gray-800 text-white p-12 rounded-lg shadow-md w-full max-w-md mx-4 sm:mx-auto">
        <div>
            <img src="https://surgesetup.com/img/SurgePays_Logo_.png" alt="SurgePays Logo" class="align-items-center mb-2 w-auto h-auto p-8">
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="?page=login">

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium mb-2">Usuario</label>
                <input type="text" id="username" name="username" class="w-full p-2 rounded bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium mb-2">Contraseña</label>
                <input type="password" id="password" name="password" class="w-full p-2 rounded bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">Ingresar</button>
            </div>
        </form>
    </div>
</body>

</html>
