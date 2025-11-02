<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// cargar .env desde la carpeta padre
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

function env(string $key, $default = null) {
    if (array_key_exists($key, $_ENV) && $_ENV[$key] !== '') return trim((string) $_ENV[$key]);
    if (array_key_exists($key, $_SERVER) && $_SERVER[$key] !== '') return trim((string) $_SERVER[$key]);
    $v = getenv($key);
    return $v === false ? $default : trim((string) $v);
}

// leer configuración usando env()
$smtpHost   = env('SMTP_HOST', 'smtp.gmail.com');
$smtpUser   = env('SMTP_USER');
$smtpPass   = env('SMTP_PASS', env('SMTP_PASSWORD'));
$smtpPort   = (int) env('SMTP_PORT', 587);
$smtpSecure = strtolower(env('SMTP_SECURE', 'tls'));

$mailFrom     = env('MAIL_FROM', $smtpUser);
$mailFromName = env('MAIL_FROM_NAME', $mailFrom);

// validaciones
if (!filter_var($smtpUser, FILTER_VALIDATE_EMAIL)) {
    echo 'Error: SMTP_USER no valido o no definido en .env';
    exit;
}
if (empty($smtpPass)) {
    echo 'Error: SMTP_PASS no definido en .env (usa App Password si es Gmail con 2FA)';
    exit;
}
if (!filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
    $mailFrom = $smtpUser;
    $mailFromName = $mailFromName ?: $mailFrom;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->SMTPDebug = 0; // 0
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->Port       = $smtpPort;

    if ($smtpSecure === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($smtpSecure === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->setFrom($mailFrom, $mailFromName);
    $mail->addAddress('r.castro23@info.uas.edu.mx'); // destinatario

    $mail->isHTML(true);
    $mail->Subject = 'Prueba';
    $mail->Body    = 'Correo de prueba';
    $mail->AltBody = 'Correo de prueba (texto)';

    $mail->send();
    echo 'Correo enviado';
} catch (Exception $e) {
    echo 'Error: ' . $mail->ErrorInfo . ' — ' . $e->getMessage();
}
// ...existing code...