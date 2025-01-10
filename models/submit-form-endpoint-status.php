<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include './saveUser.php';

    if (!isset($conn)) {
        die("Error: La conexión a la base de datos no se inicializó correctamente.");
    }

    $dataUser = json_decode($_POST['dataUser'], true);

    print_r($_POST);

    // Campos enviados por el formulario
    $userId = $_POST['status-id'] ?? null;
    $userLevel = $_POST['userLevelField'] ?: $dataUser['userLevel'];
    $username = $_POST['usernameField'] ?: $dataUser['username'];
    $fullname = $_POST['nameField'] ?: $dataUser['fullname'];
    $password = !empty($_POST['password'])
        ? password_hash($_POST['password'], PASSWORD_BCRYPT) // Codificar nueva contraseña
        : $dataUser['password']; // Usar la contraseña existente
    $vendorName = $_POST['vendor_name'] ?: $dataUser['name_vendor'];
    $numberAccount = $_POST['numberAccountField'] ?: $dataUser['number_account'];
    $comments = $_POST['commentsField'] ?: $dataUser['comments'];
    $firstname = $_POST['firstname'] ?: $dataUser['firstName'];
    $status = isset($_POST['statusField']) && $_POST['statusField'] !== '' ? $_POST['statusField'] : $dataUser['status'];

    // // Imprimir los datos procesados para depuración
    $processedData = [
        'userId' => $userId,
        'status' => $status,
    ];

    print_r($processedData);

    try {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        // Consulta de actualización
        $query = "
            UPDATE users
            SET 
                status = :status
                
            WHERE userId = :userId
        ";

        $stmt = $conn->prepare($query);

        $stmt->execute([
          
            ':status' => $status,
            ':userId' => $userId, // Usamos el userId como referencia
        ]);

        // Redirección después de éxito
        header("Location: /hr-surge.com/vendor/index.php?page=user&alert=update");
        exit;
    } catch (PDOException $e) {
        // Mostrar mensaje de error
        echo "
         <script>
             Swal.fire({
                 icon: 'error',
                 title: 'Error al actualizar',
                 text: 'Error al actualizar usuario: " . addslashes($e->getMessage()) . "',
             }).then(() => {
                window.location.href = '/hr-surge.com/vendor/index.php?page=user'; // Redirige después del error
             });
         </script>
     ";

        header("Location: /hr-surge.com/vendor/index.php?page=user&alert=error&message=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    echo "No se enviaron datos por POST.";
    exit;
}
