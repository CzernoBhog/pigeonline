<?php

namespace controllers;

use Exception;
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
                    $isFriend = \models\DAOFriends::getFriends(array('friendId' => $_POST['users']));
                    if (!is_null($isFriend)) {
                        //inserisce l'amico come membro della chat
                        $chatMember = new \models\DOChatMembers($_POST['users'], $chatId, null, 2);
                        \models\DAOChatMembers::insertChatMember($chatMember);
                        //inserisce l'utente che crea la chat come membro
                        $chatMember = new \models\DOChatMembers($creatorUser->getUserId(), $chatId, null, 2);
                        \models\DAOChatMembers::insertChatMember($chatMember);
                    } else {
                        throw new Exception('Errore creazione chat.');
                    }
                    break;

                case '2':
                    if (count($userIds) > 50) {
                        throw new Exception('Troppi utenti selezionati: massimo 50 persone!');
                    }
                    $chatTitle == '' ? 'New group' : $chatTitle;
                    $targetFilePath = is_null($targetFilePath) ? './utils/imgs/groupDefault.png' : $targetFilePath;
                    $chat = new \models\DOChat(NULL, $_POST['chatType'], $chatTitle, $chatDescription, $targetFilePath);
                    \models\DAOChat::insertChat($chat);
                    $chatId = \models\DAOChat::getLastInsertId();
                    foreach ($userIds as $id) {
                        $isFriend = \models\DAOFriends::getFriends(array('friendId' => $id));
                        if (!is_null($isFriend)) {
                            $chatMember = new \models\DOChatMembers($id, $chatId, null, 2);
                            \models\DAOChatMembers::insertChatMember($chatMember);
                        }
                    }
                    $chatMember = new \models\DOChatMembers($creatorUser->getUserId(), $chatId, null, 3);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    break;

                case '3':
                    $targetFilePath = is_null($targetFilePath) ? './utils/imgs/groupDefault.png' : $targetFilePath;
                    $chatTitle == '' ? 'New group' : $chatTitle;
                    $chat = new \models\DOChat(NULL, $_POST['chatType'], $chatTitle, $chatDescription, $targetFilePath);
                    \models\DAOChat::insertChat($chat);
                    $chatId = \models\DAOChat::getLastInsertId();
                    foreach ($userIds as $id) {
                        $isFriend = \models\DAOFriends::getFriends(array('friendId' => $id));
                        if (!is_null($isFriend)) {
                            $chatMember = new \models\DOChatMembers($id, $chatId, null, 1);
                            \models\DAOChatMembers::insertChatMember($chatMember);
                        }
                    }
                    $chatMember = new \models\DOChatMembers($creatorUser->getUserId(), $chatId, null, 3);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    break;

                default:
                    throw new Exception("Tipo di chat non supportato :/");
                    break;
            }
            Transaction::commitTransaction();
            echo 'true';
        } catch (\Exception $e) {
            Transaction::rollBackTransaction();
            die($e->getMessage());
            //die("Errore imprevisto");
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
        $chatMember = \models\DAOChatMembers::getChatMembers(array("chatId" => $_GET['chatId'], "userId" => $_SESSION['id']))[0];
        //recupero dati dei membri della chat
        $chatMembers = \models\DAOChatMembers::getChatMembers(array("chatId" => $chat->getChatId()), FALSE, FALSE, 'username', '*', TRUE, array('user' => 'userId', 'userDetails' => 'userId'), 'userId');

        if (is_null($chatMember) || is_null($chat)) {       //blocca l'utente nel caso cercasse di entrare nelle chat altrui
            (new \utils\WAF)->showBlockPage('98', 'access to other people\'s chats');
        }

        if ($chat->getChatType() == '1' || $chat->getChatType() == '4') {
            $friendId = ($chatMembers[0]['userId'] !== $_SESSION['id']) ? $chatMembers[0]['userId'] : $chatMembers[1]['userId'];
            if (is_null(\models\DAOFriends::getFriends(array('userId' => $_SESSION['id'], 'friendId' => $friendId, 'authorizated' => 1)))) {
                (new \utils\WAF)->showBlockPage('98', 'access to blocked chats');
            }
        }

        //salva l'id della chat nella sessione per il refresh dei messaggi
        $_SESSION['chatId'] = $chat->getChatId();

        //recupero dei messaggi
        $messages = \models\DAOMessage::getOldMessages($chat->getChatId(), $user->getUserId());
        include('views/chatPage.php');
    }

    /**
     * Visualizza la cloud chat dell'utente
     */
    public function viewCloudChat()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        $chat = \models\DAOChat::getChat(
            array("userId" => $_SESSION['id'], 'chatType' => 5),
            FALSE,
            FALSE,
            NULL,
            'chat.*',
            FALSE,
            array('chatMembers' => 'chatId'),
            'chatId'
        )[0];

        //salva l'id della chat nella sessione per il refresh dei messaggi
        $_SESSION['chatId'] = $chat->getChatId();
        //recupera i messaggi
        $messages = \models\DAOMessage::getOldMessages($chat->getChatId(), $user->getUserId());
        include('views/chatPage.php');
    }

    /**
     * Aggiunge o revoca i diritti di admin a un utente selezionato, appartenente alla chat attuale
     * 
     * @throws Exception $e Exception generale per indicare un errore avvenuto durante l'operazione
     */
    public function addRemoveAdmin()
    {
        try {
            $countAdmins = count(\models\DAOChatMembers::getChatMembers(array("chatId" => $_SESSION['chatId'], "userType" => "3")));
            $chat = \models\DAOChat::getChat(array('chatId' => $_SESSION['chatId']))[0];
            $chatMember = \models\DAOChatMembers::getChatMembers(array("userId" => $_POST['userId'], "chatId" => $_SESSION['chatId']))[0];
            if ($countAdmins > 1 && ($chatMember->getUserType() === '3' || $chatMember->getUserId() === $_SESSION['id'])) {
                $chatMember->setUserType(($chat->getChatType() === '3') ? 1 : 2);      //revoca i diritti di admin se già lo è
                echo "removed";
            } else {
                $chatMember->setUserType(3);      //rende l'utente selezionato come admin della chat, se non è admin
                echo "added";
            }
            \models\DAOChatMembers::updateChatMember($chatMember);
        } catch (\Exception $e) {
            // echo $e->getMessage();
            echo "error";
        }
    }

    /**
     * Aggiorna la foto gruppo/canale, la descrizione e il nome
     */
    public function updateInfoChat()
    {
        try {
            $chat = \models\DAOChat::getChat(array("chatId" => $_SESSION['chatId']))[0];
            switch ($_REQUEST['type']) {
                case 'description':
                    $chat->setDescription($_POST['value']);
                    break;
                case 'pathToChatPhoto':
                    //aggiunge foto alla cartella e rimuove quella precedente
                    $targetFilePath = $chat->getPathToChatPhoto();

                    if (!empty($_FILES["picture"]["name"])) {
                        // File upload path
                        $targetDir = "./utils/imgs/groupsPhoto/";
                        $fileName = basename($_FILES["picture"]["name"]);
                        $targetFilePath = $targetDir . $chat->getChatId() . $fileName;
                        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                        // Allow certain file formats
                        $allowTypes = array('jpg', 'png', 'jpeg');
                        if (in_array($fileType, $allowTypes)) {
                            // Upload file to server
                            if (move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFilePath)) {
                                if ($chat->getPathToChatPhoto() != './utils/imgs/groupDefault.png')
                                    unlink($chat->getPathToChatPhoto());
                            } else {
                                die("Sorry, there was an error uploading your file.");
                            }
                        } else {
                            die('Sorry, only JPG, JPEG & PNG files are allowed to upload.');
                        }
                    }

                    $chat->setPathToChatPhoto($targetFilePath);
                    break;
                case 'title':
                    $chat->setTitle($_POST['value']);
                    break;
                default:
                    throw new \Exception('error');
            }
            \models\DAOChat::updateChat($chat);
            echo 'success';
        } catch (\Exception $e) {
            //echo $e->getMessage();
            echo 'error';
        }
    }

    /**
     * Aggiorna lo stato di scrittura (sta scrivendo oppure no) dell'utente
     */
    public function updateIsTyping()
    {
        try {
            $chatMember = \models\DAOChatMembers::getChatMembers(array("userId" => $_SESSION['id'], "chatId" => $_SESSION['chatId']))[0];
            $chatMember->setIsTyping(($_POST['isTyping'] === 'true') ? 1 : 0);
            \models\DAOChatMembers::updateChatMember($chatMember);
            echo "success";
        } catch (\Exception $e) {
            echo $e->getMessage();
            //echo 'error';
        }
    }

    /**
     * Recupera quali utenti stanno scrivendo nella chat attuale
     */
    public function checkWhoIsTyping()
    {
        try {
            $chatMembers = \models\DAOChatMembers::getChatMembers(
                array(
                    "chatId" => $_SESSION['chatId'],
                    "isTyping" => 1
                ),
                FALSE,
                FALSE,
                'username',
                'chatMembers.*, user.username',
                TRUE,
                array("user" => "userId"),
                "userId"
            );
            if (!is_null($chatMembers)) {
                $mainUser = array_search($_SESSION['id'], array_column($chatMembers, 'userId'));
                if ($mainUser == 'false'){
                    unset($chatMembers[$mainUser]);
                }       
                echo json_encode($chatMembers);
            } else {
                echo "none";
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            //echo 'error';
        }
    }
}
