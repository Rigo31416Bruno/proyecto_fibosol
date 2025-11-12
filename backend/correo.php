<?php
// Carga autoload buscando en rutas habituales
require('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail(string $toEmail, string $toName, string $code): bool {
    $mail = new PHPMailer(true);
    try {
        // SMTP config (ajusta credenciales o lee desde env)
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'reestablecercontrasena99@gmail.com';
        $mail->Password = 'eazoyravaqeacxcs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('reestablecercontrasena99@gmail.com', 'Soporte');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = 'Código recuperación de contraseña';
        $mail->Body = "
          <div style='font-family:Arial,Helvetica,sans-serif;color:#111;'>
            <h3>Hola " . htmlspecialchars($toName) . "</h3>
            <p>Tu código para reestablecer la contraseña es:</p>
            <div style='font-size:1.4rem;font-weight:700;padding:10px;background:#f3f4f6;display:inline-block;border-radius:6px;letter-spacing:3px;'>" . htmlspecialchars($code) . "</div>
            <p>Expira en 1 hora. Si no solicitaste esto, ignora este correo.</p>
          </div>";
        $mail->AltBody = "Tu código de recuperación: $code";

        return (bool) $mail->send();
    } catch (Exception $e) {
        error_log('sendVerificationEmail error: ' . $e->getMessage());
        return false;
    }
}
?>