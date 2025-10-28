<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require('../libs/phpmailer/vendor/autoload.php');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Desactivar debug en producción
    $mail->isSMTP();                                         //Enviar usando SMTP
    $mail->Host       = 'smtp.gmail.com';                    //SMTP de Gmail
    $mail->SMTPAuth   = true;                                //Autenticación SMTP
    $mail->Username   = 'rigotrainer@gmail.com';            //Tu cuenta Gmail
    $mail->Password   = 'ueae hdpb hrcv lidw';              //App Password de Google
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     //STARTTLS
    $mail->Port       = 587;                                 //Puerto TLS

    //Recipients
    $mail->setFrom('rigotrainer@gmail.com', 'RigoTrainer');
    $mail->addAddress('rigotrainer@gmail.com', 'RigoTrainer'); //Cambia al destinatario real

    //Content - texto plano
    $mail->isHTML(false);                                   //Enviar solo texto
    $mail->Subject = 'Asunto del correo';
    $mail->Body    = "Este es el cuerpo del correo en texto plano.\nPuedes usar saltos de línea.";

    $mail->send();
    echo 'Mensaje enviado';
} catch (Exception $e) {
    echo "No se pudo enviar. Error: {$mail->ErrorInfo}";
}