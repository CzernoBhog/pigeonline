<?php

namespace controllers;

require_once('utils/autoload.php');

class utentiController
{

    public function login()
    {
    }      // TODO

    public function viewFirstPage()
    {  //default action
        if (session_status() == 2) {
            session_destroy();
        }
        include('views/firstPage.php');
    }

    public function viewLogin($username = null)
    {
        $waf = new \utils\WAF();
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
            $newUser = new \models\DOUser(null, trim($_POST['name']), trim($_POST['surname']), trim($_POST['email']), null, trim($_POST['username']), $passwordHash, null, null, $token);
            \models\DAOUser::insertUser($newUser);
            $id =  \models\DAOUser::getLastInsertId();
            $newUserDetails = new \models\DOUserDetails(null, 0, date('Y-m-d H:i:s'), null, $id);
            \models\DAOUserDetails::insertUserDetails($newUserDetails);
            $linkAttivazione = "http://localhost:8080/esercizi/pigeonline/index.php?controller=utentiController&action=confermaRegistrazione&id=$id&token=$token";
            //invio mail autenticazione
            $mail = new \utils\Mail();
            $mail->invioMailAttivazione($linkAttivazione, trim($_POST['email']), $newUser->getUsername());
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
