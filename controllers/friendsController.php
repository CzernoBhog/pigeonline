<?php

namespace controllers;

require_once('utils/autoload.php');

class friendsController
{
    /**
     * Visualizza la pagina di gestione amici e ricarica il menu delgi amici se richiesto con ajax
     */
    public function viewFriendsPage()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        //recupero gli amici dell'utente
        $detailsFriends = \models\DAOFriends::getFriendsDetails($user->getUserId(), 1); //lista amici effettivi
        $userPendingRequests = \models\DAOFriends::getFriendsDetails($user->getUserId(), 0); //richieste inviate dall'utente non ancora accettate
        $friendPendingrequests = \models\DAOFriends::getApplicantsUsers($user->getUserId()); //richieste ricevute dall'utente non ancora accettate

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            include('./views/menuFriends.php');
        } else {
            include('views/friendsPage.php');
        }
    }

    /**
     * Restituisce una lista di utenti (solo id e username per sicurezza) filtrati con la clausola LIKE in base a username = $_POST['filter'];
     */
    public function searchUser()
    {
        if ($_POST['filter'] === "") {
            echo 'null';
            return;
        }

        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        $users = \models\DAOUser::getUser(array('username' =>  $_POST['filter'] . '%', 'activated' => '1'), false, true, 'username', 'userId, username', true);
        $alreadyFriends = \models\DAOFriends::getFriendsDetails($user->getUserId());

        if (!is_null($users)) {
            if (($index = array_search($user->getUserId(), array_column($users, 'userId'))) !== false) {
                unset($users[$index]);
            }

            if (!is_null($alreadyFriends)) {
                foreach ($alreadyFriends as $friend) {
                    if (($index = array_search($friend['userId'], array_column($users, 'userId'))) !== false) {
                        unset($users[$index]);
                    }
                }
            }
        }

        echo json_encode($users);
    }

    /**
     * Invia una richiesta di amicizia all'utente selezionato
     */
    public function friendRequest()
    {
        $newFriend = \models\DAOUser::getUser(array('userId' => $_POST['friendId']));
        if ($newFriend === null) {
            echo 'error';
        } else {
            $request = new \models\DOFriends($_SESSION['id'], $newFriend->getUserId(), 0);
            try {
                \models\DAOFriends::insertFriend($request);
                echo 'success';
            } catch (\Exception $e) {
                echo 'error';
            }
        }
    }

    /**
     * Accetta una richiesta di amicizia all'utente selezionato
     */
    public function acceptDeclineRequest()
    {
        try {
            $newFriend = \models\DAOUser::getUser(array('userId' => $_POST['friendId']));

            if ($newFriend === null) {
                echo 'error';
            } else {
                if ($_POST['accepted']) {
                    \utils\Transaction::beginTransaction();
                    $request = new \models\DOFriends($newFriend->getUserId(), $_SESSION['id'], 1);
                    // aggiorna la richiesta dell'utente
                    \models\DAOFriends::updateFriend($request);

                    // aggiunge l'amicizia anche all'altro utente
                    $request = new \models\DOFriends($_SESSION['id'], $newFriend->getUserId(), 1);
                    \models\DAOFriends::insertFriend($request);
                    \utils\Transaction::commitTransaction();
                } else {
                    \models\DAOFriends::deleteFriend($request);
                }
                echo 'success';
            }
        } catch (\Exception $e) {
            \utils\Transaction::rollBackTransaction();
            echo 'error';
        }
    }
}
