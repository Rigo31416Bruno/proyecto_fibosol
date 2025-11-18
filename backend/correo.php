<?php
require('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail(string $toEmail, string $toName, string $code): bool {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    try {
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'reestablecercontrasena99@gmail.com';
        $mail->Password = 'eazoyravaqeacxcs';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('reestablecercontrasena99@gmail.com', 'Soporte Shopware');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = 'Shopware — Recuperación de contraseña';

        $mail->Body = "
        <div style='font-family:Arial,Helvetica,sans-serif;color:#222;margin:0;padding:0;background:#f6f7fb;'>
          <table width='100%' cellpadding='0' cellspacing='0' role='presentation'>
            <tr>
              <td align='center' style='padding:24px 0'>
                <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 6px 18px rgba(0,0,0,.06)'>
                  
                  <tr>
                    <td style='padding:20px 24px;border-bottom:1px solid #eee;background:#111;color:#fff'>
                      <h1 style='margin:0;font-size:20px;font-weight:700'>Shopware</h1>
                      <div style='font-size:12px;opacity:.85'>Tienda de ropa</div>
                    </td>
                  </tr>

                  <tr>
                    <td style='padding:28px 32px'>
                      <p style='margin:0 0 12px;font-size:15px'>Hola " . htmlspecialchars($toName) . ",</p>
                      <p style='margin:0 0 18px;color:#555'>Recibimos una solicitud para reestablecer la contraseña de tu cuenta en Shopware. Usa el código siguiente para continuar:</p>

                      <div style='display:flex;justify-content:center;margin:18px 0'>
                        <div style='background:#f3f4f6;border-radius:8px;padding:14px 22px;font-size:22px;font-weight:700;letter-spacing:6px;color:#111'>
                          " . htmlspecialchars($code) . "
                        </div>
                      </div>

                      <p style='margin:0 0 18px;color:#777'>El código expira en 1 hora. Si no solicitaste este cambio, puedes ignorar este correo o contactar a soporte.</p>

                      <p style='margin:0 0 6px'>
                        <a href='/' style='display:inline-block;padding:10px 16px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;font-weight:600'>
                          Ir a Shopware
                        </a>
                      </p>

                      <hr style='border:none;border-top:1px solid #eee;margin:20px 0' />

                      <p style='margin:0;font-size:12px;color:#999'>
                        Equipo Shopware — Tienda de ropa<br>
                        Si necesitas ayuda responde a este correo.
                      </p>
                    </td>
                  </tr>

                  <tr>
                    <td style='padding:12px 24px;background:#fafafa;font-size:12px;color:#999;text-align:center'>
                      © " . date('Y') . " Shopware. Todos los derechos reservados.
                    </td>
                  </tr>

                </table>
              </td>
            </tr>
          </table>
        </div>
        ";

        $mail->AltBody = "Hola " . htmlspecialchars($toName) . "\n\n"
            . "Tu código para reestablecer la contraseña en Shopware es: " . $code . "\n\n"
            . "Expira en 1 hora. Si no solicitaste este cambio, ignora este mensaje.\n\n"
            . "Visita: /";

        return (bool) $mail->send();
    } catch (Exception $e) {
        error_log('sendVerificationEmail error: ' . $e->getMessage());
        return false;
    }
}
?>