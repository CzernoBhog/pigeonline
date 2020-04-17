<?php

namespace utils;

use PHPMailer\PHPMailer\PHPMailer;

require_once './assets/PHPMailer-master/src/PHPMailer.php';
require_once './assets/PHPMailer-master/src/POP3.php';
require_once './assets/PHPMailer-master/src/SMTP.php';
require_once './assets/PHPMailer-master/src/Exception.php';

class Mail extends PHPMailer
{

    public function __construct()
    {
        $this->isSMTP();
        $this->SMTPAuth = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->Host = 'smtp.gmail.com';
        $this->Port = 465;
        $this->isHTML(true);
        $this->Username = 'pigeonline.project@gmail.com';
        $this->Password = 'Chatta_Con_Piccioni';
        $this->setFrom('pigeonline.project@gmail.com', 'PigeOnLine');
        //$this->SMTPDebug = 4;
    }

    public function invioMail($mailTo, $username, $message, $subject)
    {
        $this->addAddress($mailTo);
        $this->Subject = $subject;
        ob_start();
        include("./views/mailBody.php");     // il contenuto della view viene salvata nel buffer
        $this->Body = ob_get_contents();
        ob_end_clean();
        if (!$this->send()) {
            throw new \Exception($this->ErrorInfo);
        }
        return true;
    }
}
