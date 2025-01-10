<?php

// Iniciar la sesión
session_start();

// Incluir la conexión a la base de datos
require './models/conex.php';

// Router básico para cargar las páginas
$page = $_GET['page'] ?? 'login';
$items = $_GET['items'] ?? null; // Parámetro de paginación específico para 'user'

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user']) && $page !== 'login') {
    // Redirigir al login si no hay sesión activa y no está en la página de login
    header('Location: ?page=login');
    exit;
}

// Verificar si la ruta pertenece a /models/*
if (preg_match('/^models\/(.+)$/', $page, $matches)) {
    $modelFile = './models/' . $matches[1] . '.php';
    if (file_exists($modelFile)) {
        include $modelFile;
        exit; // Finaliza la ejecución después de incluir el archivo
    } else {
        $pageContent = 'views/404.php'; // Archivo no encontrado
    }
} else {
    // Establecemos la ruta de contenido de la página que será incluida en `main.php`
    switch ($page) {
        case 'logout':
            // Destruir la sesión y redirigir al login
            session_start();
            session_unset();
            session_destroy();
            header('Location: ?page=login');
            exit;
            break;
        case 'dashboard':
            $pageContent = 'views/dashboard.php';
            break;
        case 'vendor':
            $pageContent = 'views/vendor.php';
            break;
        case 'transaction':
            $pageContent = 'views/transaction.php';
            break;
        case 'report':
            $pageContent = 'views/report.php';
            break;
        case 'vendor_client':
            $pageContent = 'views/vendor_client.php';
            break;
        case 'user':
            $pageContent = 'views/user.php';
            break;
        default:
            $pageContent = 'views/login.php'; // Por defecto, carga login.php
            break;
    }
}

// Pasar `$items` solo cuando `page` sea `user`
if ($page === 'user' && $items !== null) {
    $_GET['items'] = (int)$items; // Asegura que el valor de `items` sea un número entero
}

// Incluir la plantilla principal solo si el usuario está autenticado
if (isset($_SESSION['user']) && $page !== 'login') {
    include 'views/layout/main.php';
} else {
    include $pageContent; // Carga login.php o el archivo especificado
}
?>
