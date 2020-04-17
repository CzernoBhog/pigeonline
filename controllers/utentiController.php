<?php

namespace controllers;

require_once('utils/autoload.php');

class utentiController
{

    /**
     * Esegue la login dell'utente
     * 
     * @return Void
     */
    public function login()
    {
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
                        // Generare un ID di sessione randomico, rigenerare un nuovo ID di sessione (vedere quando farlo), controllare se la sessione riguarda in certo IP (magari avvisare se è lui o no)
                        //provo a vedere se va il login
                        $_SESSION['id'] = $user->getUserId(); //TODO pensarci bene
                        $_SESSION['sessionTTL'] = time();
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
                            $message = "<p style='font-size:25px;'>È stato eseguito un nuovo accesso al tuo account da:</p><br>
                            <ul><li>Stato: $country</li><li>Regione: $region</li><li>Città: $city</li></ul><br>
                            <p>Se non sei stato tu ad effettuare l'accesso ti consigliamo di cambiare password al più presto.<br>Cordiali saluti, lo staff</p>";

                            $mail->invioMail(trim($_POST['email']), $user->getUsername(), $message, "Nuovo accesso a pigeOnLine");
                        }

                        return;
                    }
                } catch (\Exception $e) {        // Eccezzione lanciata dal metodo verifyCSRF()
                    //a, non so perchèdesso no guarda, entro su verify
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

    public function viewHomePage()
    {
        //TODO controllo se utente è salavto nelle sessioni
        include('views/homePage.php');
    }

    public function viewFirstPage()
    {  //default action
        if (session_status() == 2) {
            session_destroy();
        }

        include('views/firstPage.php');
    }

    public function viewLogin($username = null)
    {
        include('views/loginPage.php');
    }

    public function viewRegistration()
    {
        include('views/registerPage.php');
    }

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

    public function controlloUsername()
    {    //richiamato da ajax controlla che in fase di registrazione l'username non sia già stato utilizzato
        echo \models\DAOUser::getUser(array('username' => $_POST['username'])) != null ? 'Username già in uso' : 'true';
    }

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

            $linkAttivazione = "http://localhost:8080/esercizi/pigeonline/index.php?controller=utentiController&action=confermaRegistrazione&id=$id&token=$token";

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

    //pagina default per visualizzare messaggi di errore o di conferma
    public function viewMessagePage($message)
    {
        include('./views/messagePage.php');
    }

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
}
