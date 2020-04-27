<?php

namespace controllers;

require_once('utils/autoload.php');

class chatController
{

    /**
     * Recupera tutte le chat relative all'utente loggato
     */
    public function getChats()
    {

    }

    /**
     * Elimina la chat selezionata
     */
    public function deleteChat()
    {

    }

    /**
     * Crea una chat con un utente selezionato (o più)
     */
    public function createChat()
    {

    }

    public function mostraModaleAddChat()
    {
        $detailsFriends = \models\DAOFriends::getFriendsDetails($_SESSION['id'], 1); //lista amici effettivi
        include('./views/modali.php');
    }

}

?>