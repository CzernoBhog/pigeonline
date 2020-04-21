<?php

namespace controllers;

require_once('utils/autoload.php');

class menuController
{
    public function caricaMenu()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        include('./views/menu.php');
    }
}
