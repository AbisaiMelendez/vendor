<?php
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);


try {
    $mail = new PHPMailer(true);

    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com'; // Cambiar según tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'vendor-noreply@surgepays.sv';
    $mail->Password = 'D.460087689989az';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configuración del correo
    $mail->setFrom('vendor-noreply@surgepays.sv', 'TEST vendor');
    $mail->addAddress('it@surgepays.sv');

    // Agregar destinatarios como copia (CC)
    $mail->addCC('etrejo@surgepays.com');
    $mail->addCC('jsegovia@surgepays.sv');

    // Agregar destinatarios como copia oculta (BCC)
    // $mail->addBCC('auditoria@surgepays.sv');


    $mail->Subject = 'Prueba de PHPMailer';
    $mail->Body = 'Este es un mensaje de prueba enviado con PHPMailer.';

    // Enviar correo
    $mail->send();
    echo 'Correo enviado correctamente.';
} catch (Exception $e) {
    echo "Error al enviar correo: {$mail->ErrorInfo}";
}


?>