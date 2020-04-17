<?php

namespace models;

class DAOUser
{

    public static function getUser(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*')
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'user', $orClause, $replaceWithLIKE, $orderBy, $select);
        $stmt = $conn->prepare($query);

        foreach ($params as $key => $value) {
            if ($value != "") {
                $stmt->bindValue($key, $value);
            }
        }

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new \Exception($e->getMessage());
        }

        $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOUser');

        if (count($resultSet) != 0) {
            return count($resultSet) > 1 ? $resultSet : $resultSet[0];
        }

        return NULL;
    }

    public static function insertUser($user)
    {
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO user (username, password, name, surname, email, token, userIp) VALUES(:un, :pw, :n, :s, :e, :t, :ip)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":un", $user->getUsername());
            $stmt->bindValue(":pw", $user->getPassword());
            $stmt->bindValue(":n", $user->getName());
            $stmt->bindValue(":s", $user->getSurname());
            $stmt->bindValue(":e", $user->getEmail());
            $stmt->bindValue(":t", $user->getToken());
            $stmt->bindValue(":ip", $user->getUserIp());
            return $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new \Exception('Errore inserimento utente');
        }
    }

    public static function updateUtente($user)
    {
        $conn = \utils\Database::connect();
        $query = 'UPDATE user SET username=:un, password=:pw, name=:n, surname=:s, email=:e, mood=:m, pathProfilePicture=:ppp, activated=:a, userIp=:ip WHERE userId = :id';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":id", $user->getUserId());
            $stmt->bindValue(":un", $user->getUsername());
            $stmt->bindValue(":pw", $user->getPassword());
            $stmt->bindValue(":n", $user->getName());
            $stmt->bindValue(":s", $user->getSurname());
            $stmt->bindValue(":e", $user->getEmail());
            $stmt->bindValue(":m", $user->getMood());
            $stmt->bindValue(":ppp", $user->getPathProfilePicture());
            $stmt->bindValue(":a", $user->getActivated());
            $stmt->bindValue(":ip", $user->getUserIp());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new \Exception('Errore aggiornamento utente');
        }
    }

    public static function getLastInsertId()
    {
        $conn = \utils\Database::connect();
        return $conn->lastInsertId();
    }
}
