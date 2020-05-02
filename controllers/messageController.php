<?php

namespace controllers;

require_once('utils/autoload.php');

class messageController
{
    /**
     * Invia un messaggio inserendolo nel database
     */
    public function sendMessage()
    {
        $chatId = $_SESSION["chatId"];
        $text = $_POST['messageText'];
        try {
            $message = new \models\DOMessage(NULL, NULL, NULL, NULL, $text, NULL, 1, FALSE, FALSE, $_SESSION['id'], NULL, $chatId, FALSE);
            \models\DAOMessage::insertMessage($message);
            $messageId = \models\DAOMessage::getLastInsertId();
            //non serve, lo fa già carica messaggi
            //$seenBy = new \models\DOSeenBy($_SESSION['id'], $messageId);
            //\models\DAOSeenBy::insertSeenBy($seenBy);
            echo '1';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Carica la sezione messaggi della pagina chatPage per far apparire i nuovi messaggi
     */
    public function caricaMessaggi()
    {
        //var_dump($_SESSION);
        $chat = \models\DAOChat::getChat(array("chatId" => $_SESSION['chatId']))[0];
        $chatMembers = \models\DAOChatMembers::getChatMembers(array('chatId' => $chat->getChatId()));

        //reperire i messaggi non ancora visualizzati dall'utente per la chat passata e includere
        $messages = \models\DAOMessage::getOldMessages($chat->getChatId(), $_SESSION['id'], '1');

        try {
            \utils\Transaction::beginTransaction();

            if (is_null($messages)) {
                //TODO sistemare limit 100 o forse no
                $messages = \models\DAOMessage::getMessage(
                    array('chatId' => $_SESSION['chatId']),
                    false,
                    false,
                    'timeStamp',
                    '*',
                    TRUE,
                    array('user' => 'userId'),
                    'sentBy'
                );
            } else {
                $messages = \models\DAOMessage::getNewMessages($_SESSION['chatId'], $_SESSION['id']);   // recupero messaggi della chat con dati dell'utente che l'ha inviato 
            }

            if (!is_null($messages)) {
                foreach ($messages as $message) {
                    $seenBy = new \models\DOSeenBy($_SESSION['id'], $message['messageId']);
                    \models\DAOSeenBy::insertSeenBy($seenBy);
                    $views = \models\DAOSeenBy::getSeenBy(array('messageId' => $message['messageId']));
                    if(count($views) == 2 && ($chat->getChatType() == 1 || $chat->getChatType() == 4)){
                        \models\DAOMessage::updateSeenToTrue($message['messageId']);
                        \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                    }else if(count($views) == count($chatMembers) && ($chat->getChatType() == 2 || $chat->getChatType() == 3)){
                        \models\DAOMessage::updateSeenToTrue($message['messageId']);
                        \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                    }
                }
            }
            \utils\Transaction::commitTransaction();
        } catch (\Exception $e) {
            \utils\Transaction::rollBackTransaction();
            echo $e->getMessage();
            $messages = null;
        }

        include('./views/newMessages.php');
    }
}
