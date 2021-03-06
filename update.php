<?php
	include_once "inc.default.php";
 
	function isTblDbChanges($dbPDO) {
		$query = "SHOW TABLES LIKE 'tblDbChanges'";

		$result = $dbPDO->query($query);

		if (!$result) {
			return false;
		}

		if ($result->rowCount() > 0) {
			return true;
		}

		return false;
	}

	function applyChanges($dbPDO, $query) {
		say ("<hr/><br/>");
		say ($query["name"] . "\tTag: " . $query["tag"] . "<br/>");
		say ($query["sql"] . "<br/><br/>");

		$result = $dbPDO->exec($query["sql"]);

		//if ($result !== false) { // WEGGEDAAN, WANT NIET ALLE QUERIES GEVEN EEN RESULT TERUG 
			// Update tblDbChanges with applied changes
			$query2 = "INSERT INTO tblDbChanges (date, tag, action) VALUES (NOW(), :tag, :action)";

			$stmt = $dbPDO->prepare($query2);
			$stmt->bindParam(":tag", $query["tag"]);
			$stmt->bindParam(":action", $query["name"]);
			$stmt->execute();
		//} else {
		//	$result = "<b>No changes!</b><br/>Note: already executed or error in the query.";
		//	$result .= "<p>Resolve error &quot;already executed&quot; by checking tblDbChanges for duplicate. The query probably got executed with another tag.</p>";
		//}

		say ("Output:<br/>" . $result . "<br/><br/>");
	}


	// connectie met database
	global $arConfig;


	$dbPDO = new PDO("mysql:host=" . settings("database", "host") . ";dbname=" . settings("database", "name"), settings("database", "user"), settings("database", "password"));


	// Check if tblDbChanges exists
	if (!isTblDbChanges($dbPDO)) {
		// Create table
		$query = "CREATE TABLE IF NOT EXISTS `tblDbChanges` (";
		$query .= "`id` bigint(20) NOT NULL AUTO_INCREMENT, ";
		$query .= "`date` datetime NOT NULL, ";
		$query .= "`tag` varchar(255) NOT NULL, ";
		$query .= "`action` varchar(255) NOT NULL, ";
		$query .= "PRIMARY KEY (`id`)";
		$query .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

		$result = $dbPDO->exec($query);

		if ($result == 0) {
			$result = "tblDbChanges created!";
		}

		say ($result . "<br/><br/>");
	}

	// Read queries from _sql.inc
	if (file_exists("_sql.inc")) {
		$queries = simplexml_load_file("_sql.inc");
	}
	else {
		die("<b>File &quot;_sql.inc&quot; not found!</b>");
	}

	// Read data from tblDbChanges
	$query = "SELECT tag, action FROM tblDbChanges";
	$result = $dbPDO->query($query);


	$newQueries = array();
	$executedQueries = array();
	$i = 0;

	// Associative array from SimpleXMLElementObjects
	foreach ($queries as $query) {
		if (!isset($query["name"]) && !isset($query["tag"])) {
			$error = "<b>Missing &quot;name&quot; and/or &quot;tag&quot; attribute(s) in &lt;sql&gt; (_sql.inc)</b>";
			die($error);
		}

		$newQueries[$i]["name"] = (string) $query["name"];
		$newQueries[$i]["tag"] = (string) $query["tag"];
		$newQueries[$i]["sql"] = (string) $query;

		$i++;
	}


	$i = 0;

	// Associative array from tblDbChanges records
	if ($result->rowCount() > 0) {
		foreach ($result as $row) {
			$executedQueries[$i]["name"] = $row["action"];
			$executedQueries[$i]["tag"] = $row["tag"];

			$i++;
		}
	}

	$i = 0;
	$lenNewQ = count($newQueries);
	$lenExecQ = count($executedQueries);

	// Check if a query has already been executed
	for ($i = 0; $i < $lenNewQ; $i++) {
		for ($j = 0; $j < $lenExecQ; $j++) {
			if (($newQueries[$i]["name"] == $executedQueries[$j]["name"]) && ($newQueries[$i]["tag"] == $executedQueries[$j]["tag"])) {
				$newQueries[$i] = NULL;
			}
		}
	}


	if (count($newQueries) > 0) {
		$newQueries = array_unique($newQueries, SORT_REGULAR);

		if ((count($newQueries) == 1) && (is_null($newQueries[0]))) {
			$msg = "Database is up-to-date";
			say ($msg);
		}

		foreach ($newQueries as $query) {
			if (!is_null($query)) {
				applyChanges($dbPDO, $query);
			}
		}
	}
	
	say ("update done");
	
	if (isset($_GET["redirect"])) redirect($_GET["redirect"]); 
	
	function say($strText) {
		if (!isset($_GET["redirect"])) echo $strText; 
	}
?>
