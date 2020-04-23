<?php

namespace controllers;

require_once('utils/autoload.php');

class menuController
{

    /**
     * Carica il menu relativo all'utente
     */
    public function caricaMenu()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        include('./views/menu.php');
    }
}
