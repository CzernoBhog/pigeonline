<?php

namespace models;

class DAOUserDetails {
    public static function getUser(Array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*')
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'userDetails', $orClause, $replaceWithLIKE, $orderBy, $select);
        $stmt = $conn->prepare($query);
        
        foreach($params as $key => $value){
            if($value != ""){
                $stmt->bindValue($key, $value);
            }
        }

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            return $e->getMessage();
        }

        $resultSet = $stmt->fetchAll();

        if ( count($resultSet) != 0 ) {
            return count($resultSet) > 1 ? $resultSet : $resultSet[0];
        }
        
        return NULL;
    }
}
