<?php

namespace utils;

class Database {

    private static $connection;

    public function connect() {

        if (!isset(self::$connection)) {
            try {
                $configs = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "/../config/config.ini");
                self::$connection = new \PDO($configs["type"] . ":host=" . $configs["host"] . ";dbname=" . $configs["dbName"], $configs["user"], $configs["password"], array(\PDO::ATTR_PERSISTENT => true));
                self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
                die();
            }
        }

        return self::$connection;
    }
}

?>