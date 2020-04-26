<?php

namespace controllers;

require_once('utils/autoload.php');

class blockedsController
{

    /**
    * Blocca l'utente passato
    */
    public function blockUser()
    {
        try {
            $blockedUser = \models\DAOUser::getUser(array('userId' => $_POST['userId']));
            if ($blockedUser === null) {
                echo 'error';
            } else {
                \utils\Transaction::beginTransaction();
                $block = new \models\DOUsersBlocked($_SESSION['id'], $blockedUser->getUserId());
                //elimino eventuali richieste di amicizia o l'aicizia stessa
                $friend = new \models\DOFriends($_SESSION['id'], $blockedUser->getUserId());
                \models\DAOFriends::deleteFriend($friend);
                $friendRequest = new \models\DOFriends($blockedUser->getUserId(), $_SESSION['id']);
                \models\DAOFriends::deleteFriend($friendRequest);
                \models\DAOUsersBlocked::insertUserBlocked($block);
                \utils\Transaction::commitTransaction();
                echo "success";
            }
        } catch (\Exception $e) {
            \utils\Transaction::rollBackTransaction();
            echo "error";
        }
    }

    /**
    * Sblocca l'utente passato
    */
    public function unblockUser()
    {
        try {
            $blockedUser = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $_SESSION['id'], 'userBlocked' => $_POST['userId']))[0];
            if ($blockedUser === null) {
                echo 'error';
            } else {
                \models\DAOUsersBlocked::deleteUserBlocked($blockedUser);
                echo "success";
            }
        } catch (\Exception $e) {
            echo "error";
        }
    }
}

?>
