<?php 
namespace utils;

class Transaction{
    public static function beginTransaction(){
        $conn = Database::connect();
        $conn->beginTransaction();
        return;
    }

    public static function rollBackTransaction(){
        $conn = Database::connect();
        $conn->rollBack();
        return;
    }

    public static function commitTransaction(){
        $conn = Database::connect();
        $conn->commit();
        return;
    }
}

?>