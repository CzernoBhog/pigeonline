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
        
        $detailsFriends = \models\DAOFriends::getFriendsDetails($user->getUserId(), 1); //lista amici effettivi
        $userPendingRequests = \models\DAOFriends::getFriendsDetails($user->getUserId(), 0); //richieste inviate dall'utente non ancora accettate
        $friendPendingrequests = \models\DAOFriends::getApplicantsUsers($user->getUserId()); //richieste ricevute dall'utente non ancora accettate
        $blockedUsers = \models\DAOUsersBlocked::getBlockedDetails($user->getUserId()); //lista utenti bloccati

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
        $blockedBy = \models\DAOUsersBlocked::getUsersBlocked(array('userBlocked' => $user->getUserId()));
        $userBlocked = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $user->getUserId()));
        $alreadyRequested = \models\DAOFriends::getFriends(array('friendId' => $user->getUserId(), 'authorizated' => 0));
        if(!is_array($alreadyRequested) && !is_null($alreadyRequested)){
            $alreadyRequested = array($alreadyRequested);
        }

        if (!is_null($users)) {
            if (($index = array_search($user->getUserId(), array_column($users, 'userId'))) !== false) {
                unset($users[$index]);
            }

            if (!is_null($alreadyRequested)) {
                foreach ($alreadyRequested as $request) {
                    if (($index = array_search($request->getUserId(), array_column($users, 'userId'))) !== false) {
                        unset($users[$index]);
                    }
                }
            }

            if (!is_null($alreadyFriends)) {
                foreach ($alreadyFriends as $friend) {
                    if (($index = array_search($friend->getUserId(), array_column($users, 'userId'))) !== false) {
                        unset($users[$index]);
                    }
                }
            }

            if (!is_null($blockedBy)) {
                foreach ($blockedBy as $user) {
                    if (($index = array_search($user->getBlockedBy(), array_column($users, 'userId'))) !== false) {
                        unset($users[$index]);
                    }
                }
            }

            if (!is_null($userBlocked)) {
                foreach ($userBlocked as $user) {
                    if (($index = array_search($user->getUserBlocked(), array_column($users, 'userId'))) !== false) {
                        unset($users[$index]);
                    }
                }
            }
        }

        echo json_encode($users);
    }

    /**
     * Invia una richiesta di amicizia all'utente selezionato
     * 
     * @throws Exception $e Exception generale per indicare un errore avvenuto durante l'operazione
     */
    public function friendRequest()
    {
        $isBlockedByYou = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $_SESSION['id'], 'userBlocked' => $_POST['friendId']));     //verifica che l'utente loggato non 
        $isBlockedByHim = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $_POST['friendId'], 'userBlocked' => $_SESSION['id']));     //abbia bloccato l'altro  e viceversa
        if($_POST['friendId'] !== $_SESSION['id'] && (is_null($isBlockedByYou) || is_null($isBlockedByYou))) {    //controlla, inoltre, che non riesca a inviarsi richieste da solo
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
    }

    /**
     * Accetta una richiesta di amicizia all'utente selezionato
     * 
     * @throws Exception $e Exception generale per indicare un errore avvenuto durante l'operazione
     */
    public function acceptDeclineRequest()
    {
        try {
            $newFriend = \models\DAOUser::getUser(array('userId' => $_POST['friendId']));

            if ($newFriend === null) {
                echo 'error';
            } else {
                \utils\Transaction::beginTransaction();
                if ($_POST['accepted'] === "true") {
                    $request = new \models\DOFriends($newFriend->getUserId(), $_SESSION['id'], 1);
                    // aggiorna la richiesta dell'utente
                    \models\DAOFriends::updateFriend($request);

                    // aggiunge l'amicizia anche all'altro utente
                    $request = new \models\DOFriends($_SESSION['id'], $newFriend->getUserId(), 1);
                    \models\DAOFriends::insertFriend($request);
                } else {
                    $request = new \models\DOFriends($newFriend->getUserId(), $_SESSION['id'], 0);
                    \models\DAOFriends::deleteFriend($request);
                }
                \utils\Transaction::commitTransaction();
                echo 'success';
            }
        } catch (\Exception $e) {
            \utils\Transaction::rollBackTransaction();
            echo 'error';
        }
    }

    /**
     * elimina una richiesta di amicizia inviata
     * 
     * @throws Exception $e Exception generale per indicare un errore avvenuto durante l'operazione
     */
    public function cancelRequest()
    {
        try {
            $newFriend = \models\DAOUser::getUser(array('userId' => $_POST['friendId']));
            if ($newFriend === null) {
                echo 'error';
            } else {
                $request = new \models\DOFriends($_SESSION['id'], $newFriend->getUserId(), 0);
                \models\DAOFriends::deleteFriend($request);
                echo 'success';
            }
        } catch (\Exception $e) {
            echo 'error';
        }
    }
}
