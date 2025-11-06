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
    $verifyLink = $appUrl . '/backend/verificar_email.php?token=' . urlencode($token);

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

        $displayName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');

        $htmlBody = '<!doctype html>
        <html lang="es">
        <head><meta charset="utf-8"></head>
        <body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;margin:40px auto;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <tr>
                  <td style="padding:20px 24px;background:#0d6efd;color:#ffffff;text-align:center;">
                    <h1 style="margin:0;font-size:20px;">Verifica tu correo</h1>
                  </td>
                </tr>
                <tr>
                  <td style="padding:26px 28px;color:#333;">
                    <p style="font-size:16px;margin:0 0 12px;">Hola '.$displayName.',</p>
                    <p style="margin:0 0 18px;color:#555;">Gracias por registrarte. Para activar tu cuenta pulsa el botón de abajo. El enlace expira en 24 horas.</p>
                    <p style="text-align:center;margin:22px 0;">
                      <a href="'.$verifyLink.'" target="_blank" style="display:inline-block;padding:12px 22px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;">Verificar mi correo</a>
                    </p>
                    <p style="font-size:13px;color:#777;margin:0;">Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:<br><a href="'.$verifyLink.'" style="color:#0d6efd;word-break:break-all;">'.$verifyLink.'</a></p>
                  </td>
                </tr>
                <tr>
                  <td style="padding:16px 20px;background:#f7f9fb;color:#888;text-align:center;font-size:13px;">
                    Si no solicitaste esto, puedes ignorar este correo.
                  </td>
                </tr>
              </table>
            </td></tr>
          </table>
        </body>
        </html>';

        $mail->Body = $htmlBody;
        $mail->AltBody = "Hola {$toName}\n\nGracias por registrarte. Visita el enlace para verificar tu correo (expira en 24h):\n{$verifyLink}\n\nSi no solicitaste esto, ignora este mensaje.";

        return $mail->send();
    } catch (Exception $e) {
        // opcional: error_log($e->getMessage());
        return false;
    }
}
?>
