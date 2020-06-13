<?php

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
            $this->redis = new Redis();
            $this->redis->connect($this->config["redis"]["host"], $this->config["redis"]["port"]);
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

    function struuid($entropy) {
        $s=uniqid("",$entropy);
        $num= hexdec(str_replace(".","",(string)$s));
        $index = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base= strlen($index);
        $out = '';
        for($t = floor(log10($num) / log10($base)); $t >= 0; $t--) {
            $a = floor($num / pow($base,$t));
            $out = $out.substr($index,$a,1);
            $num = $num-($a*pow($base,$t));
        }
        return $out;
    }

    function addUser($token) {
        //$sql = $this->conn->prepare("INSERT INTO user (email, pwd) VALUES (?, ?)");
    }

    function register($email, $pwd) {
        $sql=$this->conn->prepare("SELECT email FROM user WHERE email=?");
        $sql->execute(array($email));
        if ($sql->rowCount() > 0) {
            echo "user already exists";
            return;
        }
        $load = json_encode (["email" => $email, "pwd" => password_hash($pwd, PASSWORD_DEFAULT)]);
        $uuid = $this->struuid(true);
        $this->redis->set($uuid, $load, $this->config["redis"]["timeout"]);
        echo "send mail to $email with code $uuid";
    }

}

$v = new userManager();
$v->initDB();
$v->register("verf@web-utils.eu", "password");

?>