<?php

namespace utils;

class MailPHP
{

    public static function sendMail($to, $username, $subject, $message)
    {
        // Message
        ob_start();
        include("./views/mailBody.php");     // il contenuto della view viene salvata nel buffer
        $body = ob_get_contents();
        ob_end_clean();

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        // Mail it
        mail($to, $subject, $body, implode("\r\n", $headers));
    }
}
