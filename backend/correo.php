<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cargar autoload de Composer (vendor está en la raíz del proyecto)
require __DIR__ . '/../vendor/autoload.php';

// .env está en la raíz del proyecto (un nivel arriba de backend)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// DEBUG: asegurar que se cargaron las variables (no mostrar la contraseña)
if (!getenv('SMTP_USER') || !getenv('SMTP_PASSWORD')) {
    die("Faltan SMTP_USER o SMTP_PASSWORD. Verifica .env o variables de entorno\n");
}

$mail = new PHPMailer(true);
// Ver más detalle durante pruebas SMTP (moverlo después de instanciar)
$mail->SMTPDebug = SMTP::DEBUG_OFF;
$mail->Debugoutput = 'echo';

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                                
    // Trim para evitar saltos de línea
    $mail->Username   = trim(getenv('SMTP_USER'));                 
    $mail->Password   = trim(getenv('SMTP_PASSWORD'));             
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     
    $mail->Port       = 587;                                 

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom(getenv('SMTP_USER'), 'Mi App');
    $mail->addAddress('r.castro23@info.uas.edu.mx', 'RIGOBERTO CASTRO PACHECO');

    $mail->isHTML(false);                                   
    $mail->Subject = 'Rigoberto';
    $mail->Body    = "El codigo es: ";

    $mail->send();
    echo 'Mensaje enviado';
} catch (Exception $e) {
    echo "No se pudo enviar. Error: {$mail->ErrorInfo} - {$e->getMessage()}";
}
