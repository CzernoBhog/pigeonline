<?php

namespace models;

class DAOBlockedIp
{

    public static function getBlockedIp(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*')
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'blockedIp', $orClause, $replaceWithLIKE, $orderBy, $select);
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

        $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOBlockedIp');

        if (count($resultSet) != 0) {
            return count($resultSet) > 1 ? $resultSet : $resultSet[0];
        }

        return NULL;
    }

    public static function insertBlockedIp($blockedIp)
    {
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO blockedIp (ip, userId, injId, typeVuln) VALUES(:ip, :ui, :ii, :tv)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ip", $blockedIp->getIp());
            $stmt->bindValue(":ui", $blockedIp->getUserId());
            $stmt->bindValue(":ii", $blockedIp->getInjId());
            $stmt->bindValue(":tv", $blockedIp->getTypeVuln());
            return $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function removeBlockedIp($ip){
        $conn = \utils\Database::connect();
        $query = 'DELETE FROM blockedIp WHERE ip=:ip';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":ip", $ip);
            return $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
