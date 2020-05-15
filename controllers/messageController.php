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
        $text = (isset($_POST['messageText'])) ? $_POST['messageText'] : NULL;
        $file = (isset($_FILES["file"])) ? $_FILES : NULL;
        $filePath = NULL;
        try {
            if (!is_null($file)) {           // File allegato senza testo
                if (filesize($file["file"]["tmp_name"]) <= 32 * 1024 * 1024) {      // controlla se maggiore di 32MB
                    $msgType = 2;
                    if (!file_exists($chatDirPath = "./utils/filesChats/" . $_SESSION['chatId'])) {
                        if (!mkdir($chatDirPath, 0766, true)) {
                            throw new \Exception('Failed to create folders...');
                        }
                    }
                    $filePath = $chatDirPath . '/' . basename($file["file"]["name"]);
                    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $filePath)) {
                        throw new \Exception("Sorry, there was an error uploading your file.");
                    }
                    if (!is_null($text)) {       // Testo con file allegato
                        $msgType = 3;
                    }
                } else {
                    throw new \Exception("File di dimensioni superiori a 32MB");
                }
            } else {                        // Solo testo
                $msgType = 1;
            }
            $message = new \models\DOMessage(NULL, NULL, NULL, $filePath, $text, NULL, $msgType, FALSE, FALSE, $_SESSION['id'], NULL, $chatId, FALSE);
            \models\DAOMessage::insertMessage($message);
            $messageId = \models\DAOMessage::getLastInsertId();
            echo 'success';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Carica la sezione messaggi della pagina chatPage per far apparire i nuovi messaggi
     */
    public function caricaMessaggi()
    {
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
                    if (count($views) == 2 && ($chat->getChatType() == 1 || $chat->getChatType() == 4)) {
                        \models\DAOMessage::updateSeenToTrue($message['messageId']);
                        \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                    } else if (count($views) == count($chatMembers) && ($chat->getChatType() == 2 || $chat->getChatType() == 3)) {
                        \models\DAOMessage::updateSeenToTrue($message['messageId']);
                        \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                    } else if (count($views) == 1 && $chat->getChatType() == 5) {
                        \models\DAOMessage::updateSeenToTrue($message['messageId']);
                        \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                    }
                }
   
                include('./views/newMessages.php');
            }
            \utils\Transaction::commitTransaction();
        } catch (\Exception $e) {
            \utils\Transaction::rollBackTransaction();
            echo $e->getMessage();
            $messages = null;
        }
    }

    /**
     * @todo
     */
    public function deleteMessage()
    {
        $message = \models\DAOMessage::getMessage(array("messageId" => $_POST['messageId'], "chatId" => $_SESSION['chatId']));
        \models\DAOMessage::deleteMessage($message);
    }
}
