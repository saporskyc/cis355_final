<!-- --
    Author: Calob Saporsky
    Description: creates class to handle database connection
                 referenced github.com/cis355/fr when creating this file
-->

<?php
    class Database {
        //database configuration variables
        private static $dbHost = "localhost";
        private static $dbUsername = "root";
        private static $dbPass = "";
        private static $dbName = "cis355_final";

        //PDO object variable
        private static $instance = null;

        //method checking and establishing database connection
        public static function connect() {
            if (self::$instance == null) {
                try {
                    self::$instance = new PDO('mysql:host=' . self::$dbHost . ';dbname=' .self::$dbName, self::$dbUsername, self::$dbPass);
                } catch (PDOException $e) {
                    die($e -> getMessage());
                }
            }
            return self::$instance;
        }

        //method to disconnect/terminate instance
        public static function disconnect() {
            self::$instance = null;
        }
    }
?>