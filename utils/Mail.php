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

    public function invioMailAttivazione($link, $mailTo, $username)
    {
        $this->addAddress($mailTo);
        $this->Subject = 'Account activation';
        $message  = "<html><body>";
        $message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";
        $message .= "<tr><td>";
        $message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='max-width:650px; background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";
        $message .= "<thead>
  <tr height='80'>
  <th colspan='4' style='background-color:#f5f5f5; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:34px;' >PigeOnLine - Account activation</th>
  </tr>
             </thead>";
        $message .= "<tbody>
             <tr align='center' height='50' style='font-family:Verdana, Geneva, sans-serif;'>
       <td style='background-color:#00a2d1; text-align:center;'><a href='#' style='color:#fff; text-decoration:none;'>First page</a></td>
       <td style='background-color:#00a2d1; text-align:center;'><a href='#' style='color:#fff; text-decoration:none;'>Features</a></td>
       </tr>
      
       <tr>
       <td colspan='4' style='padding:15px;'>
       <p style='font-size:20px;'>Hi' " . $username . ",</p>
       <hr />
       <p style='font-size:25px;'>Per confermare la mail e attivare il tuo account premi qui:</p>
       <a style='font-size:15px;' href='" . $link . "'>Conferma il tuo indirizzo mail</a>
       <p style='font-size:15px; font-family:Verdana, Geneva, sans-serif;'>O puoi incollare questo link nel tuo browser:<br>" . $link . "</p>
       </td>
       </tr>
      
       <tr height='80'>
       <td colspan='4' align='center' style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:24px; '>
       <label>PigeOnLine On : 
              <a href='#' target='_blank'><img style='vertical-align:middle' src='https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-facebook-m.png' /></a>
       <a href='#' target='_blank'><img style='vertical-align:middle' src='https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-twitter-m.png' /></a>
       <a href='#' target='_blank'><img style='vertical-align:middle' src='https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-googleplus-m.png' /></a>
       <a href='#' target='_blank'><img style='vertical-align:middle' src='https://cdnjs.cloudflare.com/ajax/libs/webicons/2.0.0/webicons/webicon-rss-m.png' /></a>
              </label>
       </td>
       </tr>
      
              </tbody>";
        $message .= "</table>";
        $message .= "</td></tr>";
        $message .= "</table>";
        $message .= "</body></html>";
        $this->Body = $message;
        if (!$this->send()) {
            throw new \Exception($this->ErrorInfo);
        }
        return true;
    }
}
