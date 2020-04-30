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
        //ottiene le chat di cui è membro l'utente
        $chatsMember = \models\DAOChatMembers::getChatMembers(array('chatMembers.userId' => $_SESSION['id']), FALSE, FALSE, 'chatId', '*', FALSE, array('user' => 'userId'), 'userId');
        $chats = array();
        if (!is_null($chatsMember)) {
            foreach ($chatsMember as $chat) {
                array_push($chats, \models\DAOChat::getChat(array('chat.chatId' => $chat->getChatId()))[0]);
            }
        }

        //controlla se è una chat privata e imposta il titolo della chat con l'username dell'amico
        for ($i=0; $i < count($chats); $i++) { 
            $chatType = $chats[$i]->getChatType();
            if ($chatType === "1" || $chatType === "4") {
                $chatMembers = \models\DAOChatMembers::getChatMembers(array('chatId' => $chats[$i]->getChatId()));
                $friendId = ($chatMembers[0]->getUserId() !== $_SESSION['id']) ? $chatMembers[0]->getUserId() : $chatMembers[1]->getUserId();
                $chats[$i] = \models\DAOChatMembers::getChatMembers(
                    array('chatMembers.chatId' => $chats[$i]->getChatId(), 'user.userId' => $friendId), 
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
        }

        include('./views/menu.php');
    }
}
