<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require('../libs/phpmailer/vendor/autoload.php');

/*$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();*/

$mail = new PHPMailer(true);

try {
<<<<<<< HEAD
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = 'echo';
=======
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Desactivar debug en producción
    $mail->isSMTP();                                         //Enviar usando SMTP
    $mail->Host       = 'smtp.gmail.com';                    //SMTP de Gmail
    $mail->SMTPAuth   = true;                                //Autenticación SMTP
    $mail->Username   = $_ENV['GMAIL_PASSWORD'];           //Tu cuenta Gmail
    $mail->Password   = '';              //App Password de Google
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     //STARTTLS
    $mail->Port       = 587;                                 //Puerto TLS
>>>>>>> 61e6aa1e9da467e9303f2f611cbced8af1082b33

    $mail->isSMTP();                                         
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                                
    $mail->Username   = getenv('SMTP_USER');                 
    $mail->Password   = getenv('SMTP_PASS');                 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     
    $mail->Port       = 587;                                 

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom('reestablecercontrasena99@gmail.com', 'reestablecercontrasena99');
    $mail->addAddress('reestablecercontrasena99@gmail.com', 'reestablecercontrasena99');

    $mail->isHTML(false);                                   
    $mail->Subject = 'Rigoberto';
    $mail->Body    = "Este es el cuerpo del correo en texto plano.\nPuedes usar saltos de línea. Rigoberto";

    $mail->send();
    echo 'Mensaje enviado';
} catch (Exception $e) {
    echo "No se pudo enviar. Error: {$mail->ErrorInfo} - {$e->getMessage()}";
}
