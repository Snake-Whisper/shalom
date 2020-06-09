<?php

interface iAliasManager
{
	public function addAlias($email, $distributor);
	public function delAlias($email, $distributor);
	public function lsAlias($distributor, $handler);
	public function delDist($distributor);
}

class aliasManagerMySQL implements iAliasManager
{
	public $conn;
	function __construct()
	{
		$config = require ("../config.php");
		try {
            $this->conn = new PDO("mysql:host="
						. $config["aliases"]["mysql"]["host"]
						. ";dbname=" . $config["aliases"]["mysql"]["databasename"],
						$config["aliases"]["mysql"]["username"],
						$config["aliases"]["mysql"]["passphrase"]);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        catch (PDOException $e) {
            echo "Es ga einen Datenbankzugriffsfehler!!!";
            die();
        }
	}

	function splitEmailAddr($emailAddr)
	{
		if (filter_var($emailAddr, FILTER_VALIDATE_EMAIL)) {
			$result = array(
				1 => strrpos($emailAddr, '@'),
			);
			$result[0] = substr($emailAddr, 0, $result[1]);
			$result[1] = substr($emailAddr, $result[1] + 1);
			return $result;
		} else {
			echo "got bullshit";
			die();
		}
	}

	function addAlias($email, $distributor)
	{
		$statement = $this->conn->prepare("INSERT
			INTO aliases (source_username, source_domain, destination_username, destination_domain, enabled)
			VALUES (?, ?, ?, ?, 1)");
			if(filter_var($email, FILTER_VALIDATE_EMAIL) && filter_var($distributor, FILTER_VALIDATE_EMAIL)) {
	    	$ddomain = strrpos($distributor, '@');
	    	$dlocal = substr($distributor, 0, $ddomain);
	    	$ddomain = substr($distributor, $ddomain + 1);
	    	$edomain = strrpos($email, '@');
				$elocal = substr($email, 0, $edomain);
				$edomain = substr($email, $edomain + 1);
		} else {
			echo "got bullshit";
			die();
		}

		try {
			$statement->execute(array($dlocal, $ddomain, $elocal, $edomain));
		} catch (PDOException $e) {
			if ($e->errorInfo[1] == 1062) {
				echo "Eintrag bereits vorhanden";
			}
		}
	}

	function delAlias($email, $distributor)
	{
		$statement = $this->conn->prepare("DELETE
		FROM aliases
		WHERE
		destination_domain = ? AND
		destination_username = ? AND
		source_domain = ? AND
		source_username = ?");
		$statement->execute(
			array_merge($this->splitEmailAddr($email), $this->splitEmailAddr($distributor)));
	}

	function lsAlias($distributor, $handler)
	{
		$statement = $this->conn->prepare("SELECT
			CONCAT(destination_username, '@', destination_domain)
			AS dist
			FROM aliases
			WHERE source_username = ? AND source_domain = ?");
		$statement->execute($this->splitEmailAddr($distributor));
		while ($row = $statement->fetch()) {
			$handler($row["dist"]);
		}
	}

	function delDist($distributor)
	{
		$statement = $this->conn->prepare("DELETE
			FROM aliases
			WHERE source_username = ? AND
			source_domain = ?");
		$statement->execute($this->splitEmailAddr($distributor));
	}

	function debugLsAliases($distributor)
	{
		$statement = $this->conn->prepare("SELECT
			CONCAT(destination_username, '@', destination_domain)
			AS dist
			FROM aliases
			WHERE source_username = ? AND source_domain = ?");
		$statement->execute($this->splitEmailAddr($distributor));
		echo "<table>";
		while ($row = $statement->fetch()) {
			echo "<tr><td>" . $row["dist"] . "</tr></td>";
		}
		echo "</table>";
	}

	function debugAddSpam()
	{
		for ($i=0; $i<10; $i++) {
			$this->addAlias("student$i@shalom.web-utils.eu",
			"inf4hse@shalom.web-utils.eu");
		}
	}
}
?>
