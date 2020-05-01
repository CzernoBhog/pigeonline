<?php

namespace controllers;

use utils\Transaction;

require_once('utils/autoload.php');

class chatController
{

    /**
     * Elimina la chat selezionata
     */
    public function deleteChat()
    {
        // TODO
    }

    /**
     * Crea una chat con un utente selezionato (o più)
     */
    public function createChat()
    {

        if (!isset($_POST['users'])) {
            die("Nessun utente selezionato");
        }

        $userIds = $_POST['users'];     // un solo ID se private, altrimenti array di IDs

        if ($_POST['chatType'] === '1' && count($userIds) > 1) {
            die("Errore creazione chat");
        }

        $creatorUser = \models\DAOUser::getUser(array("userId" => $_SESSION['id']));

        if ($_POST['chatType'] === '1') {
            $chatType = ($_POST['isSecret'] === "true") ? 4 : $_POST['chatType'];
            $chats = \models\DAOChat::getChat(
                array('chatType' => $chatType, 'userId' => $_POST['users']),
                FALSE,
                FALSE,
                NULL,
                '*',
                TRUE,
                array('chatMembers' => 'chatId'),
                'chatId'
            );
            if (!is_null($chats)) {
                foreach ($chats as $chat) {
                    $creatorUserSameChat = \models\DAOChat::getChat(
                        array('chat.chatId' => $chat['chatId'], 'userId' => $creatorUser->getUserId()),
                        FALSE,
                        FALSE,
                        NULL,
                        '*',
                        TRUE,
                        array('chatMembers' => 'chatId'),
                        'chatId'
                    );
                    if (count($creatorUserSameChat) > 0) {
                        die('Chat già avviata con l\'utente selezionato');
                    }
                }
            }
        }

        $chatTitle = trim($_POST['name']) == '' ? null : $_POST['name'];
        $chatDescription = trim($_POST['description']) == '' ? null : $_POST['description'];

        $targetFilePath = null;
        if (!empty($_FILES["photo"]["name"])) {
            // File upload path
            $targetDir = "./utils/imgs/groupsPhoto/";
            $fileName = basename($_FILES["photo"]["name"]);
            $targetFilePath = $targetDir . rand(0, 100) . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            // Allow certain file formats
            $allowTypes = array('jpg', 'png', 'jpeg');
            if (in_array($fileType, $allowTypes)) {
                // Upload file to server
                if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                    die("Sorry, there was an error uploading your file.");
                }
            } else {
                die('Sorry, only JPG, JPEG & PNG files are allowed to upload.');
            }
        }

        try {
            Transaction::beginTransaction();
            switch ($_POST['chatType']) {
                case '1':
                    $chatType = ($_POST['isSecret'] === "true") ? 4 : $_POST['chatType'];
                    $chat = new \models\DOChat(NULL, $chatType);
                    \models\DAOChat::insertChat($chat);
                    $chatId = \models\DAOChat::getLastInsertId();
                    //inserisce l'amico come membro della chat
                    $chatMember = new \models\DOChatMembers($_POST['users'], $chatId, null, 2);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    //inserisce l'utente che crea la chat come membro
                    $chatMember = new \models\DOChatMembers($creatorUser->getUserId(), $chatId, null, 2);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    break;

                case '2':
                    $targetFilePath = is_null($targetFilePath) ? './utils/imgs/groupDefault.png' : $targetFilePath;
                    $chat = new \models\DOChat(NULL, $_POST['chatType'], $chatTitle, $chatDescription, $targetFilePath);
                    \models\DAOChat::insertChat($chat);
                    $chatId = \models\DAOChat::getLastInsertId();
                    foreach ($userIds as $id) {
                        $chatMember = new \models\DOChatMembers($id, $chatId, null, 2);
                        \models\DAOChatMembers::insertChatMember($chatMember);
                    }
                    $chatMember = new \models\DOChatMembers($creatorUser->getUserId(), $chatId, null, 3);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    break;

                case '3':
                    $targetFilePath = is_null($targetFilePath) ? './utils/imgs/groupDefault.png' : $targetFilePath;
                    $chat = new \models\DOChat(NULL, $_POST['chatType'], $chatTitle, $chatDescription, $targetFilePath);
                    \models\DAOChat::insertChat($chat);
                    $chatId = \models\DAOChat::getLastInsertId();
                    foreach ($userIds as $id) {
                        $chatMember = new \models\DOChatMembers($id, $chatId, null, 1);
                        \models\DAOChatMembers::insertChatMember($chatMember);
                    }
                    $chatMember = new \models\DOChatMembers($creatorUser->getUserId(), $chatId, null, 3);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    break;

                default:
                    die("Tipo di chat non supportato :/");
                    break;
            }
            Transaction::commitTransaction();
            echo 'true';
        } catch (\Exception $e) {
            Transaction::rollBackTransaction();
            // die($e->getMessage());
            die("Errore imprevisto");
        }
    }

    /**
     * genera div modale per avvio di una nuova chat
     */
    public function mostraModaleAddChat()
    {
        $detailsFriends = \models\DAOFriends::getFriendsDetails($_SESSION['id'], 1); //lista amici effettivi
        include('./views/modalAddChat.php');
    }

    /**
     * Visualizza la chat selezionata con i membri e i messaggi
     */
    public function viewChatPage()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        $chat = \models\DAOChat::getChat(array("chatId" => $_GET['chatId']))[0];
        //recupera il ChatMember dell'utente per verificare che sia all'interno della chat
        $chatMember = \models\DAOChatMembers::getChatMembers(array("chatId" => $_GET['chatId'], "userId" => $_SESSION['id']));
        

        if (is_null($chatMember) || is_null($chat)) {
            (new \utils\WAF)->showBlockPage('98', 'access to other people\'s chats');
        }

        //salva l'id della chat nella sessione per il refresh dei messaggi
        $_SESSION['chatId'] = $chat->getChatId();

        //recupero dati dei membri della chat
        $chatMembers = \models\DAOChatMembers::getChatMembers(array("chatId" => $chat->getChatId()), FALSE, FALSE, 'username', '*', TRUE, array('user' => 'userId', 'userDetails' => 'userId'), 'userId');
        $messages = \models\DAOMessage::getMessage(
            array("chatId" => $chat->getChatId(), 'seenBy.userId' => $user->getUserId()), 
            FALSE, 
            FALSE, 
            'timeStamp',
            '*',
            TRUE,
            array('user' => 'userId', 'seenBy' => 'messageId'),
            array('sentBy', 'messageId')
        );   // recupero messaggi della chat con dati dell'utente che l'ha inviato 
        include('views/chatPage.php');
    }
}