<?php

namespace controllers;

use models\DAOUser;

require_once('utils/autoload.php');

class utentiController {

    public function login() {}      // TODO

    public function viewFirstPage(){  //default action
        if (session_status() == 2) {
            session_destroy();
        }
        include('views/firstPage.php');
    }

    public function viewLogin(){
        include('views/loginPage.php');
    }

    public function viewRegistration(){
        include('views/registerPage.php');
    }

    public function controlloEmail(){
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){ 
            //se l'email è valida controllo che non sia già utilizzata
            if(DAOUser::getUser(array('email' => $_POST['email'])) != null){
                echo 'Email già in uso';
            }else{
                echo 'true';
            }
        }else{
            echo 'Email non valida';
        }
    }

    public function controlloUsername(){
        echo DAOUser::getUser(array('username' => $_POST['username'])) != null ? 'Username già in uso' : 'true';
    }
}

?>