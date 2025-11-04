<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// cargar .env desde la carpeta padre (si no se cargó ya)
if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
}

function env(string $key, $default = null) {
    if (array_key_exists($key, $_ENV) && $_ENV[$key] !== '') return trim((string) $_ENV[$key]);
    if (array_key_exists($key, $_SERVER) && $_SERVER[$key] !== '') return trim((string) $_SERVER[$key]);
    $v = getenv($key);
    return $v === false ? $default : trim((string) $v);
}

function sendVerificationEmail(string $toEmail, string $toName, string $token): bool {
    $smtpHost   = env('SMTP_HOST', 'smtp.gmail.com');
    $smtpUser   = env('SMTP_USER');
    $smtpPass   = env('SMTP_PASS', env('SMTP_PASSWORD'));
    $smtpPort   = (int) env('SMTP_PORT', 587);
    $smtpSecure = strtolower(env('SMTP_SECURE', 'tls'));
    $mailFrom   = env('MAIL_FROM', $smtpUser);
    $mailFromName = env('MAIL_FROM_NAME', 'Equipo');

    $appUrl = rtrim(env('APP_URL', 'http://localhost/proyecto_fibosol-main'), '/');
    $verifyLink = $appUrl . '/backend/verify_email.php?token=' . urlencode($token);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->Port = $smtpPort;
        if ($smtpSecure === 'ssl') $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        elseif ($smtpSecure === 'tls') $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom($mailFrom, $mailFromName);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Verifica tu correo';
        $mail->Body = "<p>Hola " . htmlentities($toName) . ",</p>"
                    . "<p>Gracias por registrarte. Haz clic en el siguiente enlace para verificar tu correo:</p>"
                    . "<p><a href=\"" . $verifyLink . "\">Verificar mi correo</a></p>"
                    . "<p>Si no solicitaste esto, ignora este mensaje.</p>";
        $mail->AltBody = "Hola {$toName}\n\nVisita el enlace para verificar: {$verifyLink}";

        return $mail->send();
    } catch (Exception $e) {
        // opcional: error_log($e->getMessage());
        return false;
    }
}
?>