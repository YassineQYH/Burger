<?php

/* Connexion à la base de données */

class Database
{
    private static $dbHost= "localhost";
    private static $dbName= "burger_code";
    private static $dbUser= "root";
    private static $dbUserPassword= "";
    
    private static $connection = null;


        public static function connect() // static => elle appartient à la class Database et non aux instances de ma class Database.
        {
            try
            {
                self::$connection = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName,self::$dbUser,self::$dbUserPassword);    // self => Quand je suis dans ma class et que je veux accéder à une propriété qui est statique.
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
            return self::$connection;
        }
        public static function disconnect()
        {
            self::$connection = null;
        }
}

Database::connect();


?>