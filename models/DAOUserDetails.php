<?php

namespace models;

use Exception;

class DAOUserDetails {
    public static function getUserDetails(array $params, BOOL $orClause = FALSE, BOOL $replaceWithLIKE = FALSE, String $orderBy = NULL, String $select = '*', bool $isArray = FALSE, Array $joinTablesWithOnColumns = null, String $tableJoinColumn = null)
    {
        $conn = \utils\Database::connect();
        $query = \utils\Utility::createWhere($params, 'userDetails', $orClause, $replaceWithLIKE, $orderBy, $joinTablesWithOnColumns, $tableJoinColumn, $select);
        $stmt = $conn->prepare($query);
        
        foreach($params as $key => $value){
            if($value != ""){
                $stmt->bindValue(str_replace('.', '', $key), $value);
            }
        }

        try {
            $stmt->execute();
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }

        if($isArray){
            $resultSet = $stmt->fetchAll();
            if ( count($resultSet) != 0 ) {
                return $resultSet;
            }
            
        }else{
            $resultSet = $stmt->fetchAll(\PDO::FETCH_CLASS, '\models\DOUserDetails');
            if ( count($resultSet) != 0 ) {
                return count($resultSet) > 1 ? $resultSet : $resultSet[0];
            }
            
        }

        return NULL;
    }

    public static function insertUserDetails($userDetails){
        $conn = \utils\Database::connect();
        $query = 'INSERT INTO userDetails (isOnline, lastActivity, userId) VALUES(:io, :la, :ui)';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":io", $userDetails->getIsOnline());
            $stmt->bindValue(":la", $userDetails->getLastActivity());
            $stmt->bindValue(":ui", $userDetails->getUserId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function updateUserDetails($userDetails){
        $conn = \utils\Database::connect();
        $query = 'UPDATE userDetails SET isOnline = :io, lastActivity = :la WHERE userDetailsId = :udi';
        try {
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":io", $userDetails->getIsOnline());
            $stmt->bindValue(":la", $userDetails->getLastActivity());
            $stmt->bindValue(":udi", $userDetails->getUserDetailsId());
            $result = $stmt->execute();
            return $result;
        } catch (\Exception | \PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
