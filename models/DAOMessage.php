<?php

namespace models;

class DAOMessage
{

    public static function getMessage(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*', bool $isArray = FALSE, array $joinTablesWithOnColumns = null, String $tableJoinColumn = null)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'message', $orClause, $replaceWithLIKE, $orderBy, $joinTablesWithOnColumns, $tableJoinColumn, $select);
        $stmt = $conn->prepare($query);

        foreach ($params as $key => $value) {
            if ($value != "") {
                $stmt->bindValue(str_replace('.', '', $key), $value);
            }
        }

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new \Exception($e->getMessage());
        }

        if ($isArray) {
            $resultSet = $stmt->fetchAll();
            if (count($resultSet) != 0) {
                return $resultSet;
            }
        } else {
            $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOMessage');
            if (count($resultSet) != 0) {
                return count($resultSet) > 1 ? $resultSet : $resultSet[0];
            }
        }

        return NULL;
    }

    public static function insertMessage($message)
    {
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO message (ttl, seen, filePath, text, timeStamp, messageType, edited, pinned, sentBy, quotedMessage, chatId, inoltred) VALUES(:ttl, :s, :fp, :t, :ts, :mt, :e, :p, :sb, :qm, :ci, :i)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ttl", $message->getTtl());
            $stmt->bindValue(":s", $message->getSeen());
            $stmt->bindValue(":fp", $message->getFilePath());
            $stmt->bindValue(":t", $message->getText());
            $stmt->bindValue(":ts", $message->getTimeStamp());
            $stmt->bindValue(":mt", $message->getMessageType());
            $stmt->bindValue(":e", $message->getEdited());
            $stmt->bindValue(":p", $message->getPinned());
            $stmt->bindValue(":sb", $message->getSentBy());
            $stmt->bindValue(":qm", $message->getQuotedMessage());
            $stmt->bindValue(":ci", $message->getChatId());
            $stmt->bindValue(":i", $message->getInoltred());
            return $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new \Exception('Errore inserimento message');
        }
    }

    public static function updateMessage($message)
    {
        $conn = \utils\Database::connect();
        $query = 'UPDATE message SET ttl=:ttl, seen=:s, filePath=:fp, text=:t, timeStamp=:ts, messageType=:mt, edited=:e, pinned=:p, sentBy=:sb, quotedMessage=:qm, chatId=:ci, inoltred=:i WHERE messageId = :mi';
        try {
            $stmt = $conn->prepare($query);
            $$stmt->bindValue(":ttl", $message->getTtl());
            $stmt->bindValue(":s", $message->getSeen());
            $stmt->bindValue(":fp", $message->getFilePath());
            $stmt->bindValue(":t", $message->getText());
            $stmt->bindValue(":ts", $message->getTimeStamp());
            $stmt->bindValue(":mt", $message->getMessageType());
            $stmt->bindValue(":e", $message->getEdited());
            $stmt->bindValue(":p", $message->getPinned());
            $stmt->bindValue(":sb", $message->getSentBy());
            $stmt->bindValue(":qm", $message->getQuotedMessage());
            $stmt->bindValue(":ci", $message->getChatId());
            $stmt->bindValue(":i", $message->getInoltred());
            $stmt->bindValue(":mi", $message->getMessageId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new \Exception('Errore aggiornamento message');
        }
    }

    public static function deleteMessage($message)
    {
        $conn = \utils\Database::connect();
        $query = 'DELETE FROM message WHERE messageId = :mi';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":mi", $message->getMessageId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new \Exception('Errore eliminazione message');
        }
    }

    public static function getLastInsertId()
    {
        $conn = \utils\Database::connect();
        return $conn->lastInsertId();
    }
}
