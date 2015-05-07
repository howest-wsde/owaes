<?php
	// opvragen kan via settings("startvalues", "credits");

	/* DATABASE SETTINGS */

	// Credentials required to connect to the database
	// for further configurations

	$strVersie = "quq";

	$host = "localhost";
	$name = "quq_owaes";
	$user = "quq";
	$password = "**********";

	$domainRoot = "/owaes/";
	$domainAbsRoot = "http://localhost/owaes/";

	// connectie met database
	$dbCon = new PDO("mysql:host=" . $host . ";dbname=" . $name, $user, $password);

	// haal data van tblConfig
	$query = "SELECT `key`, `value` FROM `tblConfig`";
	$result = $dbCon->query($query);

	$arConfig = array();

	// maak een associatief array met de database waardes
	if (!$result) {
		die("Geen configuratie gevonden");
	}

	if ($result->rowCount() > 0) {
		foreach ($result as $row) {
			$keys = explode(".", $row["key"]);
			$lenKeys = count($keys);

			switch ($lenKeys) {
				case 1:
					$arConfig[$row["key"]] = $row["value"];
					break;
				case 2:
					$arConfig[$keys[0]][$keys[1]] = $row["value"];
					break;
				case 3:
					$arConfig[$keys[0]][$keys[1]][$keys[2]] = $row["value"];
					break;
			}
		}
	}

	$arConfig["domain"]["name"] = strtolower($_SERVER['HTTP_HOST']);
	$arConfig["domain"]["root"] = $domainRoot;
	$arConfig["domain"]["absroot"] = $domainAbsRoot;
	$arConfig["database"] = array(
		"host" => $host,
		"name" => $name,
		"user" => $user,
		"password" => $password
	);
?>
