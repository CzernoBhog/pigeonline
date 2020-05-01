<?php

namespace controllers;

use utils\Transaction;

require_once('utils/autoload.php');

class utentiController
{

    /**
     * Esegue la login dell'utente
     */
    public function login()
    {
        //se la sessione ha id settato allora l'utente è gia loggato
        if (isset($_SESSION['id'])) {
            $this->viewHomePage();
            return;
        }

        try {
            $user = \models\DAOUser::getUser(array('username' => $_POST['username']));
        } catch (\Exception $e) {
            $message = 'Login fallito, errore imprevisto. Riprovare';
            $this->viewMessagePage($message);
            return;
        }

        if ($user !== null) {       // Controlla se è stato trovato l'utente
            if ($user->getActivated() == 1) {      // Controlla se l'account è attivato
                try {
                    \utils\WAF::verifyCSRF($_POST['token']); // lancia una serie di eccezzioni se: non è settato il token, se è scaduto o se non combacia
                    $hashedPws = $user->getPassword();
                    if (password_verify($_POST['password'], $hashedPws)) {
                        $_SESSION['id'] = $user->getUserId();
                        $_SESSION['sessionTTL'] = time();
                        $newUserDetails = new \models\DOUserDetails($user->getUserId(), 1, date('Y-m-d H:i:s'));
                        \models\DAOUserDetails::updateUserDetails($newUserDetails);
                        $this->viewHomePage();

                        //se l'ip del client che accede è diverso da quello salvato alla regitraione invio una mail per segnalare il nuovo accesso
                        $ip = empty($_SERVER['REMOTE_ADDR']) ? null : $_SERVER['REMOTE_ADDR']; //prendo l'ip del client
                        if ($user->getUserIp() != $ip) {
                            $mail = new \utils\Mail();
                            $json     = file_get_contents("http://ipinfo.io/$ip/geo"); //geolocalizzo il nuovo ip
                            $json     = json_decode($json, true);
                            $country  = $json['country'];
                            $region   = $json['region'];
                            $city     = $json['city'];
                            $message  = "<p style='font-size:25px;'>È stato eseguito un nuovo accesso al tuo account da:</p>
                                <ul style='font-size:20px;'><li>Stato: <b>$country</b></li><li>Regione: <b>$region</b></li><li>Città: <b>$city</b></li></ul><br>
                                <p>Se non sei stato tu ad effettuare l'accesso ti consigliamo di cambiare password al più presto.<br>Cordiali saluti, lo staff :)</p>";

                            //uno è per altervista e usa mail(), l'altro usa PHPMailer, quando copio su altervista inverto i commenti
                            //$success = \utils\MailPHP::sendMail($user->getEmail(), $user->getUsername(), "Nuovo accesso a PigeOnLine", $message);
                            /*if (!$success) {
                                var_dump(error_get_last()['message']);
                            }*/
                            //$mail->invioMail(trim($_POST['email']), $user->getUsername(), $message, "Nuovo accesso a pigeOnLine");
                            $mail->invioMail(trim($_POST['email']), $user->getUsername(), $message, "Nuovo accesso a pigeOnLine");
                        }

                        return;
                    }
                } catch (\Exception $e) {        // Eccezzione lanciata dal metodo verifyCSRF()
                    $message = 'Login fallito, riprova a loggare';
                    $this->viewMessagePage($message);
                    return;
                }
            } else {
                $message = 'Login fallito, devi prima attivare l\'account tramite il link inviatoti alla mail';
                $this->viewMessagePage($message);
                return;
            }
        }
        $message = 'Login fallito, username o password non validi';
        $this->viewMessagePage($message);
    }

    /**
     * Visualizza la Home Page
     */
    public function viewHomePage()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        include('views/homePage.php');
    }

    /**
     * Visualizza la prima pagina e distrugge la sessione se esiste
     */
    public function viewFirstPage()
    {   //default action
        if (session_status() == 2) {
            if (isset($_SESSION['id'])) {
                $newUserDetails = new \models\DOUserDetails($_SESSION['id'], 0, date('Y-m-d H:i:s'));
                \models\DAOUserDetails::updateUserDetails($newUserDetails);
            }
            session_destroy();
        }

        include('views/firstPage.php');
    }

    /**
     * Visualizza la View di Login
     */
    public function viewLogin($username = null)
    {
        include('views/loginPage.php');
    }

    /**
     * Visualizza la View di Registrazione
     */
    public function viewRegistration()
    {
        include('views/registerPage.php');
    }

    /**
     * Controlla che l'email non sia già stata usata o che sia valida
     */
    public function controlloEmail()
    {   //richiamato da un ajax controlla che in fase di registrazione la mail sia valida e non sia stata già utilizzata
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            //se l'email è valida controllo che non sia già utilizzata
            try {
                if (\models\DAOUser::getUser(array('email' => $_POST['email'])) != null) {
                    echo 'Email già in uso';
                } else {
                    echo 'true';
                }
            } catch (\Exception $e) {
                echo 'errore temporaneo';
            }
        } else {
            echo 'Email non valida';
        }
    }

    /**
     * Controlla che l'Username non sia già stato usato
     */
    public function controlloUsername()
    {    //richiamato da ajax controlla che in fase di registrazione l'username non sia già stato utilizzato
        if (isset($_SESSION['id'])) {
            $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
            if ($_POST['username'] == $user->getUsername()) {
                echo 'true';
                return;
            }
        }
        echo \models\DAOUser::getUser(array('username' => $_POST['username'])) != null ? 'Username già in uso' : 'true';
    }

    /**
     * Registra l'utente iniziando un processo di transazione e manda un'email necessaria per l'attivazione dell'account
     *
     * @throws Exception $e Nel caso la registrazione sia fallita, fa anche il rollback della transazione
     */
    public function registraUtente()
    {
        \utils\Transaction::beginTransaction();

        try {
            $passwordHash = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));

            $ip = empty($_SERVER['REMOTE_ADDR']) ? null : $_SERVER['REMOTE_ADDR']; //prendo l'ip del client

            $newUser = new \models\DOUser(null, trim($_POST['name']), trim($_POST['surname']), trim($_POST['email']), null, trim($_POST['username']), $passwordHash, null, null, $token, $ip);
            \models\DAOUser::insertUser($newUser);
            $id =  \models\DAOUser::getLastInsertId();

            $newUserDetails = new \models\DOUserDetails(null, 0, date('Y-m-d H:i:s'), null, $id);
            \models\DAOUserDetails::insertUserDetails($newUserDetails);

            $host  = $_SERVER['HTTP_HOST'];
            $linkAttivazione = "http://$host/esercizi/pigeonline/index.php?controller=utentiController&action=confermaRegistrazione&id=$id&token=$token";

            //invio mail autenticazione
            $mail = new \utils\Mail();
            $message = "<p>Per confermare la mail ed attivare il tuo account premi qui:</p>
            <table role='presentation' border='0' cellpadding='0' cellspacing='0' class='btn btn-primary'>
              <tbody>
                <tr>
                  <td align='center'>
                    <table role='presentation' border='0' cellpadding='0' cellspacing='0'>
                      <tbody>
                        <tr>
                          <td> <a href='$linkAttivazione' target='_blank'>Conferma Email</a> </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
            <br></br>
            <p>oppure puoi incollare il seguente link nel tuo browser:</p>
            <p>$linkAttivazione</p>
            <br>
            <p>Grazie per esserti iscritto alla nostra piattaforma :)</p>";

            //\utils\MailPHP::sendMail(trim($_POST['email']), $newUser->getUsername(), "Attivazione dell'account", $message);
            $mail->invioMail(trim($_POST['email']), $newUser->getUsername(), $message, "Attivazione dell'account");
        } catch (\Exception $e) {
            \utils\Transaction::rollBackTransaction();
            $message = 'Registrazione fallita, riprovare più tardi.<br>' . $e->getMessage();
            $this->viewMessagePage($message);

            return;
        }
        \utils\Transaction::commitTransaction();
        $message = 'Confermare la registrazione tramitte il link inviato via mail ed effettuare l\'accesso';
        $this->viewMessagePage($message);
    }

    /**
     * Pagina default per visualizzare messaggi di errore o di conferma
     */
    public function viewMessagePage($message)
    {
        include('./views/messagePage.php');
    }

    /**
     * Conferma la registrazione dell'utente abilitandolo all'uso del sito
     *
     * @throws Exception $e In caso di errore imprevisto
     */
    public function confermaRegistrazione()
    { //conferma account tramite link sulla mail
        $user = \models\DAOUser::getUser(array('userId' => $_GET['id']));
        if ($user != null) {
            if ($user->getToken() == $_GET['token']) {
                $user->setActivated(1);
                try {
                    \models\DAOUser::updateUtente($user);
                    $this->viewLogin($user->getUsername());
                } catch (\Exception $e) {
                    $message = "Errore, riprovare più tardi";
                    $this->viewMessagePage($message);
                }
                return;
            }
        }
        $message = "link non valido";
        $this->viewMessagePage($message);
    }

    /**
     * Aggiorna l'ultima attività dell'utente
     */
    public function updateActivity()
    {
        $currentTime = date('Y-m-d H:i:s');
        $userDetails = new \models\DOUserDetails($_SESSION['id'], 1, $currentTime);
        \models\DAOUserDetails::updateUserDetails($userDetails);
    }

    /**
     * Genera div modale per le impostazioni del profilo utente
     */
    public function mostraUserSettings()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        $userPrivacy = \models\DAOUserDetails::getUserDetails(array('userId' => $_SESSION['id']));
        include('./views/modalUserSettings.php');
    }

    /**
     * aggiorna le impostazioni del profilo dell'utente
     */
    public function aggiornaProfilo()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        $mood = $_POST['mood'] != '' ? $_POST['mood'] : null;
        $username = $_POST['username'] != '' ? $_POST['username'] : $user->getUsername();
        $passwordHash = $_POST['password'] != '' ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : $user->getPassword();
        $pl = $_POST['pl'] != '0' ? $_POST['pl'] : null;

        $targetFilePath = $user->getPathProfilePicture();

        if (!empty($_FILES["picture"]["name"])) {
            // File upload path
            $targetDir = "./utils/imgs/usersPhoto/";
            $fileName = basename($_FILES["picture"]["name"]);
            $targetFilePath = $targetDir . $user->getUserId() . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            // Allow certain file formats
            $allowTypes = array('jpg', 'png', 'jpeg');
            if (in_array($fileType, $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFilePath)) {
                    if ($user->getPathProfilePicture() != './utils/imgs/img_avatar.png')
                        unlink($user->getPathProfilePicture());
                } else {
                    die("Sorry, there was an error uploading your file.");
                }
            } else {
                die('Sorry, only JPG, JPEG & PNG files are allowed to upload.');
            }
        } 

        try {
            Transaction::beginTransaction();
            if (!is_null($pl)) {
                $newDetails = new \models\DOUserDetails(null, null, null, $pl, $user->getUserId());
                \models\DAOUserDetails::updatePrivacyLevel($newDetails);
            }
            $targetFilePath = is_null($targetFilePath) ? './utils/imgs/img_avatar.png' : $targetFilePath;
            $newUser = new \models\DOUser($user->getUserId(), null, null, null, $mood, $username, $passwordHash, $targetFilePath);
            \models\DAOUser::updateSettings($newUser);
            Transaction::commitTransaction();
            echo '1';
        } catch (\Exception $e) {
            Transaction::rollBackTransaction();
            if (!is_null($targetFilePath))
                unlink($targetFilePath);
            echo 'error';
        }
    }
}
