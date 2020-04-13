<?php

namespace models;

class DAOUser {

    public static function getUser(Array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*')
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'user', $orClause, $replaceWithLIKE, $orderBy, $select);
        $stmt = $conn->prepare($query);
        
        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            return $e->getMessage();
        }

        $resultSet = $stmt->fetchAll('DOUser');

        if ( isset($resultSet) ) {
            return count($resultSet) > 1 ? $resultSet : $resultSet[0];
        }
        
        return NULL;
    }

}

?>