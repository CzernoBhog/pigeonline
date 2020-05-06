<?php

namespace controllers;

require_once('utils/autoload.php');

class menuController
{

    /**
     * carica il menu dei dettagli delle chat
     */
    public function caricaMenuChat()
    {
        $chat = \models\DAOChat::getChat(array('chatId' => $_SESSION['chatId']))[0];
        $mainUser = \models\DAOUser::getUser(
            array('user.userId' => $_SESSION['id'], 'chatId' => $chat->getChatId()),
            false,
            false,
            null,
            '*',
            true,
            array('chatMembers' => 'userId'),
            'userId'
        )[0];
        $otherUser = null;

        $users = \models\DAOChatMembers::getChatMembers(
            array('chatId' => $_SESSION['chatId']),
            FALSE,
            FALSE,
            'username',
            'chatMembers.*, user.username, user.pathProfilePicture, user.mood, userDetails.lastActivity, userDetails.privacyLevel',
            TRUE,
            array('user' => 'userId', 'userDetails' => 'userId'),
            'userId'
        );

        switch ($chat->getChatType()) {
            case '1':   //chat normali
            case '4':   // chat segrete
                $otherUser = ($users[0]['userId'] !== $mainUser['userId']) ? $users[0] : $users[1];
                break;

            case '5':   //chat cloud
                $otherUser = $users[0];
                break;

            case '2':   //gruppi
            case '3':   //canali
                for ($i = 0; $i < count($users); $i++) {      //ciclo per vedere quali utenti sono nelle amicizie e quali sono bloccati, dell'utente loggato
                    $isFriend = \models\DAOFriends::getFriends(array("userId" => $_SESSION['id'], "friendId" => $users[$i]['userId']));
                    $isBlocked = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $_SESSION['id'], 'userBlocked' => $users[$i]['userId']));
                    $imBlocked = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $users[$i]['userId'], 'userBlocked' => $_SESSION['id']));
                    
                    if(!is_null($isBlocked) || !is_null($imBlocked) || $users[$i]['userId'] === $_SESSION['id']){
                        $users[$i] += ['cantBeRequested' => true];
                    }else if(!is_null($isFriend)){             
                        if($isFriend->getAuthorizated() === '0'){
                            $users[$i] += ['cantBeRequested' => 'pending'];
                        }else{
                            $users[$i] += ['cantBeRequested' => true];
                        }
                    }else{
                        $users[$i] += ['cantBeRequested' => false];
                    }
                }
                break;
        }

        include('./views/menuDetailsChat.php');
    }

    /**
     * Carica il menu relativo all'utente
     */
    public function caricaMenu()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        //nuove richieste inviate all'utente
        $friendPendingrequests = \models\DAOFriends::getApplicantsUsers($user->getUserId());
        //ottiene le chat di cui è membro l'utente
        $chatsMember = \models\DAOChatMembers::getChatMembers(array('chatMembers.userId' => $_SESSION['id']), FALSE, FALSE, 'chatId', '*', FALSE, array('user' => 'userId'), 'userId');
        $chats = array();
        if (!is_null($chatsMember)) {
            foreach ($chatsMember as $chat) {
                array_push($chats, \models\DAOChat::getChat(array('chat.chatId' => $chat->getChatId()), FALSE, FALSE, NULL, '*', TRUE)[0]);
            }
        }

        //controlla se è una chat privata e imposta il titolo della chat con l'username dell'amico
        for ($i = 0; $i < count($chats); $i++) {
            $chatType = $chats[$i]['chatType'];
            if ($chatType === "1" || $chatType === "4") {
                $chatMembers = \models\DAOChatMembers::getChatMembers(array('chatId' => $chats[$i]['chatId']));
                $friendId = ($chatMembers[0]->getUserId() !== $_SESSION['id']) ? $chatMembers[0]->getUserId() : $chatMembers[1]->getUserId();
                //nasconde le chat private degli utenti bloccati
                if (is_null(\models\DAOFriends::getFriends(array('userId' => $_SESSION['id'], 'friendId' => $friendId, 'authorizated' => 1)))) {
                    unset($chats[$i]);
                    continue;
                }
                $chats[$i] = \models\DAOChatMembers::getChatMembers(
                    array('chatMembers.chatId' => $chats[$i]['chatId'], 'user.userId' => $friendId),
                    FALSE,
                    FALSE,
                    NULL,
                    '*',
                    TRUE,
                    array('user' => 'userId', 'userDetails' => 'userId'),
                    'userId'
                )[0];
                $chats[$i] += ['chatType' => $chatType];
            }
            $messages = \models\DAOMessage::getOldMessages($chats[$i]['chatId'], $_SESSION['id'], '1');
            $newMessages = \models\DAOMessage::getNewMessages($chats[$i]['chatId'], $_SESSION['id'], '1');
            if (!is_null($newMessages) || is_null($messages)) {
                $chats[$i] += ['newMessages' => 'true'];
            }
        }

        include('./views/menuContent.php');
    }
}
