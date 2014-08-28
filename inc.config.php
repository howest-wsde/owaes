<?php
	// opvragen kan via settings("startvalues", "credits"); 
	$arConfig = array(
		"domain" => array(
			"name" => strtolower($_SERVER['HTTP_HOST']) 
		), 
		"startvalues" => array(
			"credits" => 4800, 
			"physical" => 60, 
			"social" => 60, 
			"mental" => 60, 
			"emotional" => 60, 
		),
		"levels" => array(
			0 => array(
				"threshold" => 0, 
				"multiplier" => 1,  
			), 
			1 => array(
				"threshold" => 0, 
				"multiplier" => 1,  
			), 
			2 => array(
				"threshold" => 2800, 
				"multiplier" => 1.25,  
			), 
			3 => array(
				"threshold" => 8200, 
				"multiplier" => 1.50,  
			), 
			4 => array(
				"threshold" => 16200, 
				"multiplier" => 1.75,  
			), 
			5 => array(
				"threshold" => 26800, 
				"multiplier" => 2,  
			), 
			6 => array(
				"threshold" => 40000, 
				"multiplier" => 2,  
			), 
		), 
		"crons" => array(
			"indicators" => 24*60*60, // elke 24 uur zakken de indicatoren 1 waarde
			"hourstoworkfordelay" => 4,  // per begonnen schijf van 8 uur werk wordt het zakken van indicatoren 1 tijdseenheid ("crons/indicators") uitgesteld 
			"x" => "", 
		),  
		"date" => array(
			"speed" => 1,  // voor demo's kan de tijd sneller gezet worden: 1 real life minute = 3 owaes minuten
			"start" => mktime(12,00,00,  3,18,2014), // indien speed != 1 : vanaf deze datum startte de versnelling
			"servertime" => 0, 
		), 
		"geo" => array( 
			"latitude" => 50.8305303, 
			"longtitude" => 3.2644603, 
		), 
		"debugging" => array(
			"showwarnings" => TRUE, 
		),  
	);  

	/* DATABASE SETTINGS */
	switch($arConfig["domain"]["name"]) {
		case "quq.be": 
		case "www.quq.be": 
		case "localhost": 
			$arConfig["database"] = array(
				"host" => "localhost",
				"name" => "******************",
				"user" => "******************",  
				"password" => "******************", 
			); 
			$arConfig["facebook"] = array(
				"loginapp" => array(
					"id" => "1451650378401710", 
					"secret" => "******************", 
				));  
			break; 
		case "www.owaes.org": 
		case "owaes.org": 
		case "dev.owaes.org": 
		case "dev2.owaes.org": 
			$arConfig["database"] = array(
				"host" => "******************",
				"name" => "******************",
				"user" => "******************", 
				"password" => "******************!002", 
			);
			$arConfig["facebook"] = array(
				"loginapp" => array(
					"id" => "457889804338188", 
					"secret" => "******************", 
				)); 
			$arConfig["date"]["speed"] = 1;
			$arConfig["date"]["servertime"] = 9*60*60;  
			$arConfig["debugging"]["showwarnings"] = FALSE;  
			break; 
		default: 
			echo ("domeinnaam niet herkend (inc.config.php)"); 
			exit();
	}


	/* PATH SETTINGS */
	switch($arConfig["domain"]["name"]) {
		case "quq.be": 
		case "www.quq.be": 
			$arFolder = explode("/", $_SERVER["REQUEST_URI"]);
			switch($arFolder[1]){
				case "owaes2": 
					$arConfig["domain"]["root"] = "/owaes2/"; 
					$arConfig["domain"]["absroot"] = "http://" . $arConfig["domain"]["name"] . "/owaes2/"; 
					break; 
				default: 
					$arConfig["domain"]["root"] = "/owaes/"; 
					$arConfig["domain"]["absroot"] = "http://" . $arConfig["domain"]["name"] . "/owaes/"; 
			}
			break; 
		case "owaes.org": 
		case "www.owaes.org": 
			$arFolder = explode("/", $_SERVER["REQUEST_URI"]);
			switch($arFolder[1]){
				case "dev2": 
					$arConfig["domain"]["root"] = "/dev2/"; 
					$arConfig["domain"]["absroot"] = "http://" . $arConfig["domain"]["name"] . "/dev2/"; 
					break; 
				default: 
					$arConfig["domain"]["root"] = "/dev/"; 
					$arConfig["domain"]["absroot"] = "http://" . $arConfig["domain"]["name"] . "/dev/"; 
			}
			break; 
		case "dev.owaes.org": 
		case "dev2.owaes.org": 
			$arConfig["domain"]["root"] = "/"; 
			$arConfig["domain"]["absroot"] = "http://" . $arConfig["domain"]["name"] . "/"; 
			break;   
	}


?>