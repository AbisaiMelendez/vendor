<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include './saveUser.php';

    if (!isset($conn)) {
        die("Error: La conexión a la base de datos no se inicializó correctamente.");
    }

    $dataUser = json_decode($_POST['dataUser'], true);

    print_r($_POST);

    // Campos enviados por el formulario
    $userId = $_POST['userIdField'] ?? null;
    $userLevel = $_POST['userLevel'] ?: $dataUser['userLevel'];
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
        'userLevel' => $userLevel,
        'username' => $username,
        'fullname' => $fullname,
        'password' => $password,
        'vendorName' => $vendorName,
        'numberAccount' => $numberAccount,
        'comments' => $comments,
        'firstname' => $firstname,
        'status' => $status,
    ];

    print_r($processedData);

    try {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        // Consulta de actualización
        $query = "
            UPDATE users
            SET 
                userLevel = :userLevel,
                username = :username,
                fullname = :fullname,
                password = :password,
                name_vendor = :vendor_name,
                firstName = :firstname,
                number_account = :number_account,
                status = :status,
                comments = :comments
            WHERE userId = :userId
        ";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            ':userLevel' => $userLevel,
            ':username' => $username,
            ':fullname' => $fullname,
            ':password' => $password, // La contraseña ya está cifrada previamente si es nueva
            ':vendor_name' => $vendorName,
            ':firstname' => $firstname,
            ':number_account' => $numberAccount,
            ':status' => $status,
            ':comments' => $comments,
            ':userId' => $userId, // Usamos el userId como referencia
        ]);

        // Redirección después de éxito
        header("Location: /vendor/index.php?page=user&alert=update");
        // header("Location: /hr-surge.com/vendor/index.php?page=user&alert=update");
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
                window.location.href = '/vendor/index.php?page=user'; // Redirige después del error
             });
         </script>
     ";

        header("Location: /vendor/index.php?page=user&alert=error&message=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    echo "No se enviaron datos por POST.";
    exit;
}
