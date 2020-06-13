<?php

require_once "TokenSender.php";


class userManager
{
    public $conn;
    function __construct()
    {
    $config = require ("../config.php");
        try {
            $this->conn = new PDO("mysql:host=" . $config["user"]["mysql"]["host"] . ";dbname=" . $config["user"]["mysql"]["databasename"], $config["user"]["mysql"]["username"], $config["user"]["mysql"]["passphrase"]);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
        catch (PDOException $e)
        {
            echo "Es ga einen Datenbankzugriffsfehler!!!";
            die();
        }
    }

    function getSchema () {
        ob_start();
        require("schema.sql");
        return ob_get_clean();
    }
    
    function initDB() {
        $schema = $this->getSchema();
        $this->conn->exec($schema);
    }
    
    function addMailUser ($name, $pwd, $email, $visibleEmail = 0) {
        $statement = $this->conn->prepare("INSERT INTO dists (name, pwd, email, visibleEmail) VALUES (?, PASSWORD (?), ?, ?)");
        $statement->execute(array($name, $pwd, $email, $visibleEmail));
    }
    
    function delMailUserByName ($name) {
        $statement = $this->conn->prepare("DELETE FROM dists WHERE name = ?");
        $statement->execute(array($name));
    }
    
    function delMailUserByEmail ($email) {
        $statement = $this->conn->prepare("DELETE FROM dists WHERE email = ?");
        $statement->execute(array($email));
    }
    
    function chkUser ($nameOrEmail, $pwd) {
        $statement = $this->conn->prepare("SELECT COUNT(*) FROM dists WHERE (email = ? or name = ?) and pwd = PASSWORD (?)");
        $statement->execute(array($nameOrEmail, $nameOrEmail, $pwd));
        return $statement->fetchColumn() > 0;
    }

}

$v = new userManager();
$v->initDB();
?>