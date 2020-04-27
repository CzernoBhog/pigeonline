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
            $chatsMember = \models\DAOChatMembers::getChatMembers( array('chatMembers.userId' => $_SESSION['id']), FALSE, FALSE, 'chatId', '*', FALSE, array('user' => 'userId'), 'userId' );
            $chats = null;
            if(!is_null($chatsMember)){
                $chats = \models\DAOChat::getChat( array('chatId' => $chatsMember->getChatId()), FALSE, FALSE, 'title', '*', FALSE, array('chat' => 'chatId'), 'chatId' );
            }

            include('./views/menu.php');
        }
    }
