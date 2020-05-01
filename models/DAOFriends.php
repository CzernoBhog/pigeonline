<?php

namespace models;

use Exception;

class DAOFriends
{
    public static function getFriends(array $params, BOOL $orClause = false, BOOL $replaceWithLIKE = false, String $orderBy = null, String $select = '*', bool $isArray = false, array $joinTablesWithOnColumns = null, $tableJoinColumn = null)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'friends', $orClause, $replaceWithLIKE, $orderBy, $joinTablesWithOnColumns, $tableJoinColumn, $select);
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
            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($resultSet) != 0) {
                return $resultSet;
            }
        } else {
            $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOFriends');
            if (count($resultSet) != 0) {
                return count($resultSet) > 1 ? $resultSet : $resultSet[0];
            }
        }

        return null;
    }

    public static function insertFriend($friends)
    {
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO friends (userId, friendId, authorizated) VALUES(:ui, :fi, :a)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ui", $friends->getUserId());
            $stmt->bindValue(":fi", $friends->getFriendId());
            $stmt->bindValue(":a", $friends->getAuthorizated());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function updateFriend($friends)
    {
        $conn = \utils\Database::connect();
        $query = 'UPDATE friends SET userId = :ui, friendId = :fi, authorizated = :a WHERE userId = :uid AND friendId = :fid';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ui", $friends->getUserId());
            $stmt->bindValue(":fi", $friends->getFriendId());
            $stmt->bindValue(":a", $friends->getAuthorizated());
            $stmt->bindValue(":uid", $friends->getUserId());
            $stmt->bindValue(":fid", $friends->getFriendId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function deleteFriend($friends)
    {
        $conn = \utils\Database::connect();
        $query = 'DELETE FROM friends WHERE userId = :uid AND friendId = :fid';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":uid", $friends->getUserId());
            $stmt->bindValue(":fid", $friends->getFriendId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function getFriendsDetails($id, $authorizated = null)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere(
            array('friends.userId' => $id, 'authorizated' => $authorizated),
            'friends',
            false,
            false,
            'username',
            array('user' => 'userId', 'userDetails' => 'userId'),
            'friendId',
            'username, isOnline, mood, pathProfilePicture, lastActivity, privacyLevel, user.userId'
        );

        $stmt = $conn->prepare($query);
        $stmt->bindValue(':friendsuserId', $id);
        if (!is_null($authorizated)) {
            $stmt->bindValue(':authorizated', $authorizated);
        }

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOExternalUserDetails');

        if (count($resultSet) != 0) {
            return $resultSet;
        }

        return null;
    }

    public static function getApplicantsUsers($id)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere(
            array('friends.friendId' => $id, 'authorizated' => 0),
            'friends',
            false,
            false,
            'username',
            array('user' => 'userId', 'userDetails' => 'userId'),
            'userId',
            'username, isOnline, mood, pathProfilePicture, lastActivity, privacyLevel, user.userId'
        );

        $stmt = $conn->prepare($query);
        $stmt->bindValue(':friendsfriendId', $id);
        $stmt->bindValue(':authorizated', 0);

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }

        $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOExternalUserDetails');

        if (count($resultSet) != 0) {
            return $resultSet;
        }

        return null;
    }
}
