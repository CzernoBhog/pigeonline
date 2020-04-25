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
            //nuove richieste inviate all'utente
            $friendPendingrequests = \models\DAOFriends::getApplicantsUsers($user->getUserId());
            include('./views/menu.php');
        }
    }
