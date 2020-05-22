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
                $sharedGroups = \models\DAOChat::getSharedGroups($mainUser['userId'], $otherUser['userId']);
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

                    if (!is_null($isBlocked) || !is_null($imBlocked) || $users[$i]['userId'] === $_SESSION['id']) {
                        $users[$i] += ['cantBeRequested' => true];
                    } else if (!is_null($isFriend)) {
                        if ($isFriend->getAuthorizated() === '0') {
                            $users[$i] += ['cantBeRequested' => 'pending'];
                        } else {
                            $users[$i] += ['cantBeRequested' => true];
                        }
                    } else {
                        $users[$i] += ['cantBeRequested' => false];
                    }
                }
                break;
        }

        include('./views/menuDetailsChat.php');
    }

    /**
     * Controlla se ci sono aggiornamenti per il menu delle chat
     */
    public function chatMenuUpdates()
    {
        $chat = \models\DAOChat::getChat(array('chatId' => $_SESSION['chatId']))[0];
        $updates = array();
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

        if ($chat->getChatType() == 2 || $chat->getChatType() == 3 || $chat->getChatType() == 5) {
            if ($chat->getTitle() != $_POST['title']) {
                $updates['chat']['title'] = $chat->getTitle();
            }
            if ($chat->getDescription() != $_POST['description'] && $chat->getDescription() != null) {
                $updates['chat']['description'] = $chat->getDescription();
            }
            if ($chat->getPathToChatPhoto() != $_POST['photo']) {
                $updates['chat']['photo'] = $chat->getPathToChatPhoto();
            }
        } else {
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
            $otherUser = ($users[0]['userId'] !== $mainUser['userId']) ? $users[0] : $users[1];
            if ($otherUser['username'] != $_POST['title']) {
                $updates['chat']['title'] = $otherUser['username'];
            }
            if ($otherUser['mood'] != $_POST['description'] && $otherUser['mood'] != '') {
                $updates['chat']['description'] = $otherUser['mood'];
            }
            if ($otherUser['pathProfilePicture'] != $_POST['photo']) {
                $updates['chat']['photo'] = $otherUser['pathProfilePicture'];
            }
        }

        if ($chat->getChatType() == 2 || ($chat->getChatType() == 3 && $mainUser['userType'] == '3')) {
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

            for ($i = 0; $i < count($users); $i++) {      //ciclo per vedere quali utenti sono nelle amicizie e quali sono bloccati, dell'utente loggato
                $isFriend = \models\DAOFriends::getFriends(array("userId" => $_SESSION['id'], "friendId" => $users[$i]['userId']));
                $isBlocked = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $_SESSION['id'], 'userBlocked' => $users[$i]['userId']));
                $imBlocked = \models\DAOUsersBlocked::getUsersBlocked(array('blockedBy' => $users[$i]['userId'], 'userBlocked' => $_SESSION['id']));

                if (!is_null($isBlocked) || !is_null($imBlocked) || $users[$i]['userId'] === $_SESSION['id']) {
                    $users[$i] += ['cantBeRequested' => true];
                } else if (!is_null($isFriend)) {
                    if ($isFriend->getAuthorizated() === '0') {
                        $users[$i] += ['cantBeRequested' => 'pending'];
                    } else {
                        $users[$i] += ['cantBeRequested' => true];
                    }
                } else {
                    $users[$i] += ['cantBeRequested' => false];
                }
            }

            $members = '';
            foreach ($users as $user) {
                $src = $user['pathProfilePicture'];
                $members .= '<li style="display: flex;">
                    <a style="padding-top: 0; width: 80%;">
                        <img class="chat-img fa-pull-left" src="' . $src . '" alt="Avatar">
                        <span class="usernameMember" style="padding-left: 10px; font-size: normal; color: white">' . $user['username'] . '</span>';
                if ($user['privacyLevel'] !== '3') {
                    $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
                    $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
                    $members .= '<br><span style="padding-left: 10px; font-size: smaller">';
                    $members .= ($user['lastActivity'] > $current_timestamp) ? 'Online' : 'Offline';
                    $members .= '</span></a>';
                }

                if ($mainUser['userType'] === '3') {
                    if ($mainUser['userId'] !== $user['userId']) {
                        $members .= '<a class="removeUser" style="width: min-content; padding: 0;" title="Remove user" userId="' . $user['userId'] . '" id="removeUser' . $user['userId'] . '" href="#"><i style="color: #db4949" class="fas fa-user-minus"></i></a>';
                    }
                    if ($user['userType'] !== '3') {
                        $members .= '<a class="addRemoveAdmin" style="width: min-content; padding: 0;" title="Make Admin" userId="' . $user['userId'] . '" id="addRemoveAdmin' . $user['userId'] . '" href="#"><i class="far fa-star"></i></a>';
                    } else {
                        $members .= '<a class="addRemoveAdmin" style="width: min-content; padding: 0;" title="Remove Admin" userId="' . $user['userId'] . '" id="addRemoveAdmin' . $user['userId'] . '" href="#"><i class="fas fa-star"></i></a>';
                    }
                }

                if (!$user['cantBeRequested']) {
                    $members .= '<a class="friendRequest" style="width: min-content; padding: 0;" title="Add friend" userId="' . $user['userId'] . '" id="friendRequest' . $user['userId'] . '"><i style="color: green" class="fas fa-user-plus"></i></a>';
                } else if ($user['cantBeRequested'] === 'pending') {
                    $members .= '<a style="width: min-content; padding: 0;" title="Request sent"><i style="color: yellow" class="fas fa-user-clock"></i></a>';
                }

                $members .= '</li>';
            }

            $updates['members'] = $members;
        }

        echo json_encode($updates);
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

    /*  public function checkUpdates()
    {
        $timestamp = $_POST['timestamp'];
        $chats = \models\DAOChat::getChat(array('chatMemebers.userId' => $_SESSION['id']), FALSE, FALSE, NULL, 'chat.*', TRUE, array('chatMemebers' => 'chatId'), 'chatId');

        $updates = array();
        foreach ($chats as $chat) {
            $messages = \models\DAOMessage::getOldMessages($chat['chatId'], $_SESSION['id'], '1');
            $newMessages = \models\DAOMessage::getNewMessages($chat['chatId'], $_SESSION['id'], '1');
            if (!is_null($newMessages) || is_null($messages)) {
                $chat[] = ['newMessages' => 'true'];
            }
            switch ($chat['chatType']) {
                case '1':
                case '4':
                    $chatMembers = \models\DAOChatMembers::getChatMembers(array('chatId' => $chat['chatId']));
                    $friendId = ($chatMembers[0]->getUserId() !== $_SESSION['id']) ? $chatMembers[0]->getUserId() : $chatMembers[1]->getUserId();
                    $otherUser = \models\DAOUser::getUser(array('userId' => $friendId), FALSE, FALSE, NULL, 'user.*, userDetails.lastActivity', TRUE, array('userDetails' => 'userId'), 'userId');
                    if ($otherUser['lastModify'] > $timestamp) {
                        $chat[] = $otherUser;
                    }
                    if($otherUser['lastModify'] > $timestamp || isset($chat['newMessages'])){
                        $updates[] = $chat;
                    }
                    break;
                case '2':
                case '3':
                    if ($chat['lastModify'] > $timestamp || isset($chat['newMessages'])) {
                        $updates[] = $chat;
                    }
                    break;

                default:
                    break;
            }
        }
    } */
}
