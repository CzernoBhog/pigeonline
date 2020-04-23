<?php

namespace models;

use Exception;

class DAOFriends
{
    public static function getFriends(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*', bool $isArray = FALSE, Array $joinTablesWithOnColumns = null, String $tableJoinColumn = null)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'friends', $orClause, $replaceWithLIKE, $orderBy, $joinTablesWithOnColumns, $tableJoinColumn, $select);
        $stmt = $conn->prepare($query);

        foreach ($params as $key => $value) {
            if ($value != "") {
                $stmt->bindValue($key, $value);
            }
        }

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }

        if($isArray){
            $resultSet = $stmt->fetchAll();
            if (count($resultSet) != 0) {
                return $resultSet;
            }
        }else{
            $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOFriends');
            if (count($resultSet) != 0) {
                return count($resultSet) > 1 ? $resultSet : $resultSet[0];
            }
        }

        return NULL;
    }

    public static function insertUserDetails($friends)
    {
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO firends (userId, friendId) VALUES(:ui, :fi)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ui", $friends->getUserId());
            $stmt->bindValue(":fi", $friends->getFriendId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function getFriendsDetails($id)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere(
            array('userDetailsId' => $id),
            'friends',
            FALSE,
            FALSE,
            'username',
            array('user' => 'userId', 'userDetails' => 'userId'),
            'userId',
            'username, isOnline, lastActivity, privacyLevel, user.userId'
        );

        $stmt = $conn->prepare($query);
        $stmt->bindValue(':userDetailsId', $id);

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $resultSet = $stmt->fetchAll();

        if (count($resultSet) != 0) {
            return $resultSet;
        }

        return NULL;
    }
}
