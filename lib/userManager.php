<?php

require_once "TokenManager.php";

/*
- addUser (token)
- delUser (email)
- chkUser ()
- login (email, pwd)
- register (email, pwd)
*/

class userManager
{
    public $conn;
    function __construct()
    {
    $this->config = require ("../config.php");
        try {
            $this->conn = new PDO("mysql:host=" . $this->config["user"]["mysql"]["host"] . ";dbname=" . $this->config["user"]["mysql"]["databasename"], $this->config["user"]["mysql"]["username"], $this->config["user"]["mysql"]["passphrase"]);
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

    function addUser($email, $pwd) {
        try {
            $sql = $this->conn->prepare("INSERT INTO user (email, pwd) VALUES (?, ?)");
            $sql->execute(array($email, $pwd));
            echo "Successfull registered $email.";
        } catch ( PROException $e) {
            echo $e;
        }
        echo "There were some errors during registration.";
    }

    function register($email, $pwd) {
        $sql=$this->conn->prepare("SELECT email FROM user WHERE email=?");
        $sql->execute(array($email));
        if ($sql->rowCount() > 0) {
            echo "user already exists";
            return;
        }
        $tokenSender = new TokenSender();
        $tokenSender->sendRegisterToken($email, $pwd);
        echo "send mail to $email";
    }

}

$v = new userManager();
$v->initDB();
$v->register("verf@web-utils.eu", "password");

?>