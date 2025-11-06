<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(__DIR__ . '/..')->safeLoad();
}

function env(string $key, $default = null){
    if (array_key_exists($key, $_ENV) && $_ENV[$key] !== '') return trim((string) $_ENV[$key]);
    if (array_key_exists($key, $_SERVER) && $_SERVER[$key] !== '') return trim((string) $_SERVER[$key]);
    $v = getenv($key);
    return $v === false ? $default : trim((string) $v);
}

/**
 * Envia un correo con el código de verificación (sin URL).
 * Retorna true si se envió correctamente, false en otro caso.
 */
function sendVerificationEmail(string $toEmail, string $toName, string $code): bool {
    $smtpHost   = env('SMTP_HOST', 'smtp.gmail.com');
    $smtpUser   = env('SMTP_USER');
    $smtpPass   = env('SMTP_PASS', env('SMTP_PASSWORD'));
    $smtpPort   = (int) env('SMTP_PORT', 587);
    $smtpSecure = strtolower(env('SMTP_SECURE', 'tls'));
    $mailFrom   = env('MAIL_FROM', $smtpUser);
    $mailFromName = env('MAIL_FROM_NAME', 'Equipo');

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
        $mail->Subject = 'Codigo de verificacion';
        $mail->Body = "
            <div style='font-family: Arial, Helvetica, sans-serif; color:#111;'>
              <h3>Hola " . htmlspecialchars($toName) . "</h3>
              <p>Tu código de verificación es:</p>
              <div style='font-size:1.6rem; font-weight:700; background:#f3f4f6; padding:10px 14px; display:inline-block; border-radius:6px; letter-spacing:4px;'>
                " . htmlspecialchars($code) . "
              </div>
              <p>Este codigo expira en 1 hora. Si no solicitaste este codigo, ignora este correo.</p>
            </div>
        ";
        $mail->AltBody = "Tu codigo de verificacion: " . $code;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>