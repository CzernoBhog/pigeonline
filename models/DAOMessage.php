<?php

namespace models;

class DAOMessage
{

    public static function getMessage(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*', bool $isArray = FALSE, array $joinTablesWithOnColumns = null, $tableJoinColumn = null, $joinType = 'inner', $limit = null)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'message', $orClause, $replaceWithLIKE, $orderBy, $joinTablesWithOnColumns, $tableJoinColumn, $select, $joinType, $limit);
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
            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
        $query = 'INSERT INTO message (ttl, seen, filePath, text, messageType, edited, pinned, sentBy, quotedMessage, chatId, inoltred) VALUES(:ttl, :s, :fp, :t, :mt, :e, :p, :sb, :qm, :ci, :i)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ttl", $message->getTtl());
            $stmt->bindValue(":s", $message->getSeen());
            $stmt->bindValue(":fp", $message->getFilePath());
            $stmt->bindValue(":t", $message->getText());
            $stmt->bindValue(":mt", $message->getMessageType());
            $stmt->bindValue(":e", $message->getEdited(), \PDO::PARAM_BOOL);
            $stmt->bindValue(":p", $message->getPinned(), \PDO::PARAM_BOOL);
            $stmt->bindValue(":sb", $message->getSentBy());
            $stmt->bindValue(":qm", $message->getQuotedMessage());
            $stmt->bindValue(":ci", $message->getChatId());
            $stmt->bindValue(":i", $message->getInoltred(), \PDO::PARAM_BOOL);
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

    public static function updateSeenToTrue($messageId)
    {
        $conn = \utils\Database::connect();
        $query = 'UPDATE message SET seen=1 WHERE messageId = :mi';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":mi", $messageId);
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new \Exception('Errore aggiornamento message');
        }
    }

    public static function deleteMessage($messageId)
    {
        $conn = \utils\Database::connect();
        $query = 'DELETE FROM message WHERE messageId = :mi';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":mi", $messageId);
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

    public static function getNewMessages($chatId, $userId, $limit = null)
    {
        $conn = \utils\Database::connect();
        $query =   'SELECT * FROM message
                    INNER JOIN user ON(message.sentBy = user.userId)
                    WHERE chatId = :cid AND timeStamp > 
                    (SELECT timeStamp FROM message 
                    INNER JOIN user ON(message.sentBy = user.userId)
                    LEFT JOIN seenBy ON(message.messageId = seenBy.messageId)
                    WHERE (chatId = :ci) AND (seenBy.userId = :ui OR message.seen = 1)
                    ORDER BY timeStamp DESC LIMIT 1)';
                    
        if (!is_null($limit)) {
            $query .= " LIMIT $limit";
        }
        
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ci", $chatId);
            $stmt->bindValue(":cid", $chatId);

            $stmt->bindValue(":ui", $userId);
            $result = $stmt->execute();
            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($resultSet) != 0) {
                return $resultSet;
            }
            return null;
        } catch (\Exception | \PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function getOldMessages($chatId, $userId, $limit = '100')
    {
        $conn = \utils\Database::connect();
        $query =  "SELECT * FROM
                    (
                        SELECT message.*,user.pathProfilePicture FROM message
                        INNER JOIN user ON(message.sentBy = user.userId)
                        LEFT JOIN seenBy ON(message.messageId = seenBy.messageId)
                        WHERE (chatId = :ci) AND (seenBy.userId = :ui OR message.seen = 1)
                        ORDER BY timeStamp DESC
                        LIMIT $limit
                    ) oldMessages ORDER BY timeStamp";
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ci", $chatId);
            $stmt->bindValue(":ui", $userId);
            $result = $stmt->execute();
            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($resultSet) != 0) {
                return $resultSet;
            }
            return null;
        } catch (\Exception | \PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
