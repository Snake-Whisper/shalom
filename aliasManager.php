<?php

interface iAliasManager
{
	public function addAlias($email, $distributor);
	public function delAlias($email, $distributor);
	public function lsDist($distributor);
	public function delDist($distributor);
}

class aliasManagerMySQL implements iAliasManager
{
	public $conn;
	function __construct()
	{
		$config = include ("config.php");
		try {
            $this->conn = new PDO("mysql:host=" . $config["aliases"]["mysql"]["host"] . ";dbname=" . $config["aliases"]["mysql"]["databasename"], $config["aliases"]["mysql"]["username"], $config["aliases"]["mysql"]["passphrase"]);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
        catch (PDOException $e)
        {
            echo "Es ga einen Datenbankzugriffsfehler!!!";
            die();
        }
		
	}
	
	function addAliases($email, $distributor)
	{
		$statement = $this->conn->prepare("INSERT INTO aliases (source_username, source_domain, destination_username, destination_domain, enabled VALUES (?, ?, ?, ?, 1)");
		if(filter_var($email, FILTER_VALIDATE_EMAIL) && filter_var($distributor, FILTER_VALIDATE_EMAIL)) {
    	$ddomain = strrpos($distributor, '@');
    	$dlocal = substr($distributor, 0, $ddomain);
    	$ddomain = substr($distributor, $ddomain);
    	$edomain = strrpos($email, '@');
			$elocal = substr($email, 0, $edomain);
			$edomain = substr($email, $edomain);
		} else {
			echo "got bullshit";
			die();
		}
		
		$statement->execute(array($edomain, $elocal, $dlocal, $ddomain));
	}
}

?>