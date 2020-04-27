<?php

namespace models;

use Exception;

class DAOUsersBlocked
{

    public static function getUsersBlocked(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*', bool $isArray = FALSE, array $joinTablesWithOnColumns = null, String $tableJoinColumn = null)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'usersBlocked', $orClause, $replaceWithLIKE, $orderBy, $joinTablesWithOnColumns, $tableJoinColumn, $select);
        $stmt = $conn->prepare($query);

        foreach ($params as $key => $value) {
            if ($value != "") {
                $stmt->bindValue(str_replace('.', '', $key), $value);
            }
        }

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }

        if ($isArray) {
            $resultSet = $stmt->fetchAll();
            if (count($resultSet) != 0) {
                return $resultSet;
            }
        } else {
            $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOUsersBlocked');
            if (count($resultSet) != 0) {
                return $resultSet;
            }
        }

        return NULL;
    }

    public static function insertUserBlocked($userBlocked)
    {
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO usersBlocked (blockedBy, userBlocked) VALUES(:bb, :ub)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":bb", $userBlocked->getBlockedBy());
            $stmt->bindValue(":ub", $userBlocked->getUserBlocked());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function deleteUserBlocked($userBlocked)
    {
        $conn = \utils\Database::connect();
        $query = 'DELETE FROM usersBlocked WHERE blockedBy = :bid AND userBlocked = :ub';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":bid", $userBlocked->getBlockedBy());
            $stmt->bindValue(":ub", $userBlocked->getUserBlocked());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function getBlockedDetails($id)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere(
            array('blockedBy' => $id),
            'usersBlocked',
            FALSE,
            FALSE,
            'username',
            array('user' => 'userId', 'userDetails' => 'userId'),
            'userBlocked',
            'username, isOnline, mood, pathProfilePicture, lastActivity, privacyLevel, user.userId'
        );

        $stmt = $conn->prepare($query);
        $stmt->bindValue(":blockedBy", $id);

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOExternalUserDetails');

        if (count($resultSet) != 0) {
            return $resultSet;
        }

        return NULL;
    }
    
}
