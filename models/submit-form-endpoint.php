



<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include './saveUser.php';


    if (!isset($conn)) {
        die("Error: La conexión a la base de datos no se inicializó correctamente.");
    }

    $userLevel = $_POST['userLevel'] ?? null;
    $badge = $_POST['badge'] ?? null;
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    $fullname = $_POST['fullname'] ?? null;
    $vendor_name = $_POST['vendor_name'] ?? null;
    $number_account = $_POST['number_account'] ?? null;
    $comments = $_POST['comments'] ?? null;
    $firstname = $_POST['firstname'] ?? null;
    $firstlastname = $_POST['firstlastname'] ?? null;
    $status = $_POST['status'] ?? 1;
    $typeAccount = $_POST['typeCuenta'] ?? 1;

    try {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

        $query = "
            INSERT INTO users (
                userLevel, badge, username, password, name_vendor, firstName,
                firstLastName, fullname, number_account, status, typeAccount ,  comments
            ) VALUES (
                :userLevel, :badge, :username, :password, :vendor_name, :firstname,
                :firstlastname, :fullname,  :number_account, :status, :typeAccount,  :comments
            )
        ";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            ':userLevel' => $userLevel,
            ':badge' => $badge,
            ':username' => $username,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':vendor_name' => $vendor_name,
            ':firstname' => $firstname,
            ':firstlastname' => $firstlastname,
            ':fullname' => $fullname,
            ':number_account' => $number_account,
            ':status' => $status,
            ':typeAccount' => $typeAccount,
            ':comments' => $comments,
        ]);


        header("Location: /hr-surge.com/vendor/index.php?page=user&alert=success");

        // header("Location: /hr-surge.com/vendor/index.php?page=user");

        echo "Usuario insertado exitosamente.";

        exit;
    } catch (PDOException $e) {

        // Mostrar mensaje de error
        echo "
         <script>
             Swal.fire({
                 icon: 'error',
                 title: 'Error al guardar',
                 text: 'Error al insertar usuario: " . addslashes($e->getMessage()) . "',
             }).then(() => {
                window.location.href = '/hr-surge.com/vendor/index.php?page=user'; // Redirige después del éxito
                
             });
         </script>
     ";

        echo "Error al insertar usuario: " . $e->getMessage();
        header("Location: /hr-surge.com/vendor/index.php?page=user&alert=error&message=" . urlencode($e->getMessage()));

    }
} else {
    echo "No se enviaron datos por POST.";
    exit;
}
?>
