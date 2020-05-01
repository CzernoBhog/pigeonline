<?php

namespace models;

class DAOSeenBy
{

    public static function getSeenBy(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*', bool $isArray = FALSE, Array $joinTablesWithOnColumns = null, String $tableJoinColumn = null, $joinType = null) {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'seenBy', $orClause, $replaceWithLIKE, $orderBy, $joinTablesWithOnColumns, $tableJoinColumn, $select, $joinType);
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

        if($isArray){
            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($resultSet) != 0) {
                return $resultSet;
            }
        }else{
            $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOSeenBy');
            if (count($resultSet) != 0) {
                return $resultSet;
            }
        }

        return NULL;
    }

    public static function insertSeenBy($seenBy)
    {
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO seenBy (userId, messageId) VALUES(:ui, :mi)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ui", $seenBy->getUserId());
            $stmt->bindValue(":mi", $seenBy->getMessageId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
