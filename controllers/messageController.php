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
        $chat = \models\DAOChat::getChat(array("chatId" => $_SESSION['chatId']))[0];
        $text = (isset($_POST['messageText'])) ? $_POST['messageText'] : NULL;
        $file = (isset($_FILES["file"])) ? $_FILES : NULL;
        $filePath = NULL;
        $ttl = NULL;
        try {
            if (!is_null($file)) {           // File allegato senza testo
                if (filesize($file["file"]["tmp_name"]) <= 64 * 1024 * 1024) {      // controlla se maggiore di 64MB
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
                    throw new \Exception("File di dimensioni superiori a 64MB");
                }
            } else {                        // Solo testo
                $msgType = 1;
            }

            if ($chat->getChatType() === '4') {
                $chatMembers = \models\DAOChatMembers::getChatMembers(array('chatId' => $chat->getChatId()));
                $otherMemeberId = ($chatMembers[0]->getUserId() !== $_SESSION['id']) ? $chatMembers[0]->getUserId() : $chatMembers[1]->getUserId();
                //recupero chiave privata del mittente e chiave pubblica del destinatario
                $senderPrivK = openssl_pkey_get_private(file_get_contents("./utils/keys/" . $_SESSION['id'] . "/private.pem"));
                $receiverPubK = openssl_pkey_get_public(file_get_contents("./utils/keys/" . $otherMemeberId . "/public.pem"));

                // $receiverPrivK = openssl_pkey_get_private(file_get_contents("./utils/keys/" . $otherMemeberId . "/private.pem"));
                // $senderPubK = openssl_pkey_get_public(file_get_contents("./utils/keys/" . $_SESSION['id'] . "/public.pem"));

                //$error = openssl_error_string();

                // PrivKey Mitt. -> PubKey Dest.
                // PrivKey Dest. -> PubKey MItt.
                if (!is_null($text)) {
                    openssl_private_encrypt($text, $text, $senderPrivK);
                    openssl_public_encrypt($text, $text, $receiverPubK);
                }

                // openssl_private_decrypt($text, $text, $receiverPrivK);
                // openssl_public_decrypt($text, $text, $senderPubK);

                if (!is_null($filePath)) {
                    openssl_private_encrypt($filePath, $filePath, $senderPrivK);
                    openssl_public_encrypt($filePath, $filePath, $receiverPubK);
                }

                $ttl = 1;
            }

            $message = new \models\DOMessage(NULL, $ttl, NULL, $filePath, $text, NULL, $msgType, FALSE, FALSE, $_SESSION['id'], NULL, $chat->getChatId(), FALSE);
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
                foreach ($messages as &$message) {
                    if ($message['seen'] != '1') {
                        $seenBy = new \models\DOSeenBy($_SESSION['id'], $message['messageId']);
                        \models\DAOSeenBy::insertSeenBy($seenBy);
                        $views = \models\DAOSeenBy::getSeenBy(array('messageId' => $message['messageId']));
                        
                        if($chat->getChatType() == 4) {
                            $otherMemeberId = ($chatMembers[0]->getUserId() !== $message['sentBy']) ? $chatMembers[0]->getUserId() : $chatMembers[1]->getUserId();

                            $receiverPrivK = openssl_pkey_get_private(file_get_contents("./utils/keys/" . $otherMemeberId . "/private.pem"));
                            $senderPubK = openssl_pkey_get_public(file_get_contents("./utils/keys/" . $message['sentBy'] . "/public.pem"));
                            
                            if(!is_null($message['text'])) {
                                openssl_private_decrypt($message['text'], $message['text'], $receiverPrivK);
                                openssl_public_decrypt($message['text'], $message['text'], $senderPubK);
                            }
                            
                            if(!is_null($message['filePath'])){
                                openssl_private_decrypt($message['filePath'], $message['filePath'], $receiverPrivK);
                                openssl_public_decrypt($message['filePath'], $message['filePath'], $senderPubK);
                            }
                        }

                        if (count($views) == 2 && ($chat->getChatType() == 1 || $chat->getChatType() == 4)) {
                            if(!is_null($message['ttl'])) {
                                \models\DAOMessage::deleteMessage($message['messageId']);
                            } else {
                                \models\DAOMessage::updateSeenToTrue($message['messageId']);
                            } 
                            \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                        } else if (count($views) == count($chatMembers) && ($chat->getChatType() == 2 || $chat->getChatType() == 3)) {
                            \models\DAOMessage::updateSeenToTrue($message['messageId']);
                            \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                        } else if (count($views) == 1 && $chat->getChatType() == 5) {
                            \models\DAOMessage::updateSeenToTrue($message['messageId']);
                            \models\DAOSeenBy::deleteSeenBy($message['messageId']);
                        }
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
        \models\DAOMessage::deleteMessage($message->getMessageId());
    }
}
