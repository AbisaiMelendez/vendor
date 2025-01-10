<?php
// Iniciar la sesión
session_start();

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al login
header('Location: ?page=login');
exit;
?>
