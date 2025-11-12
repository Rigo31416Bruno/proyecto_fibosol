<?php
require('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail 
{
    private $host = 'smtp.gmail.com';
    private $user = 'reestablecercontrasena99@gmail.com';
    private $pass = 'eazoyravaqeacxcs';
    private $port = 587;
    private $security = 'tls';
    private $mailFrom = 'reestablecercontrasena99@gmail.com';
    private $mailFromName = '';
    private $mail = null;

    public function __construct(int $debug = 0)
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->SMTPDebug = $debug;
        $this->mail->SMTPAuth = true;

        $this->mail->Host = $this->host;
        $this->mail->Username = $this->user;
        $this->mail->Password = $this->pass;
        $this->mail->Port = $this->port;

        if ($this->security === 'ssl') {
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($this->security === 'tls') {
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $this->mail->setFrom($this->mailFrom, $this->mailFromName);
        $this->mail->isHTML(true);
    }

    public function getMail()
    {
        return $this->mail;
    }
}