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
        $userIds = $_POST['users'];     // un solo ID se private, altrimenti array di IDs

        if (count($userIds) === 0) {
            die("Nessun utente selezionato");
        }

        if ($_POST['chatType'] === 'privateChat' && count($userIds) > 1) {
            die("Errore creazione chat");
        }

        $creatorUser = \models\DAOUser::getUser(array("userId" => $_SESSION['id']));
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
                    $chatMember = new \models\DOChatMembers($_SESSION['id'], $chatId, null, 2);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    break;

                case '2':
                    $chat = new \models\DOChat(NULL, $_POST['chatType'], $chatTitle, $chatDescription, $targetFilePath);
                    \models\DAOChat::insertChat($chat);
                    $chatId = \models\DAOChat::getLastInsertId();
                    foreach ($userIds as $id) {
                        $chatMember = new \models\DOChatMembers($id, $chatId, null, 2);
                        \models\DAOChatMembers::insertChatMember($chatMember);
                    }
                    $chatMember = new \models\DOChatMembers($_SESSION['id'], $chatId, null, 3);
                    \models\DAOChatMembers::insertChatMember($chatMember);
                    break;

                case '3':
                    $chat = new \models\DOChat(NULL, $_POST['chatType'], $chatTitle, $chatDescription, $targetFilePath);
                    \models\DAOChat::insertChat($chat);
                    $chatId = \models\DAOChat::getLastInsertId();
                    foreach ($userIds as $id) {
                        $chatMember = new \models\DOChatMembers($id, $chatId, null, 1);
                        \models\DAOChatMembers::insertChatMember($chatMember);
                    }
                    $chatMember = new \models\DOChatMembers($_SESSION['id'], $chatId, null, 3);
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
     * Visualizza la chat selezionata
     */
    public function viewChatPage()
    {
        $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
        $chat = \models\DAOChat::getChat(array("chatId" => $_GET['chatId']));
        //recupera il ChatMember dell'utente per verificare che sia all'interno della chat
        $chatMemeber = \models\DAOChatMembers::getChatMembers(array("chatId" => $_GET['chatId'], "userId" => $_SERVER['id']));

        if (is_null($chatMemeber) || is_null($chat)) {
            die("Sei proprio un utente burlone ;D");
        }

        //recupero dei membri della chat
        $chatMember = \models\DAOChatMembers::getChatMembers(array("chatId" => $_GET['chatId']), FALSE, FALSE, 'username', '*', FALSE, array('user' => 'userId'), 'userId');

        include('views/chatPage.php');
    }
}
