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
        die(var_dump($_POST));
        $userIds = explode(", ", $_POST['users']);
        if($_POST['chatType'] === 'privateChat' && count($userIds) > 1) {
            die("NO");
        }

        switch($_POST['chatType']) {
            case 'privateChat':
                break;

            case 'group':
                foreach ($userIds as $id) {
                    $user = \models\DAOUser::getUser(array("userId" => $id));
                }
                break;

            case 'channel':
                break;

            default:
                die("NO");
                break;
        }
    }

    /**
     * genera div modale per avvio di una nuova chat
     */
    public function mostraModaleAddChat()
    {
        $detailsFriends = \models\DAOFriends::getFriendsDetails($_SESSION['id'], 1); //lista amici effettivi
        include('./views/modalAddChat.php');
    }

    /**
     * Visualizza la chat selezionata
     */
    public function viewChatPage()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        include('views/chatPage.php');
    }

}

?>