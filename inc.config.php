<?php 
	// default values / overwritten in database
	
	$arConfig = array( 
		"database" => array( 
			"host" => "localhost", 
			"name" => NULL, 
			"user" => NULL, 
			"password" => NULL, 
			"loaded" => FALSE, 
		), 
		"domain" => array( 
			"templatefolder" => NULL, 
			"name" => NULL, 
			"root" => NULL, 
			"absroot" => NULL, 
		), 
		"startvalues" => array( 
			"credits" => 4800, 
			"physical" => 60, 
			"social" => 60, 
			"mental" => 60, 
			"emotional" => 60, 
			"visibility" => FALSE,
			"algemenevoorwaarden" => FALSE,
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
				"threshold" => 1400, 
				"multiplier" =>1.25, 
			), 
			3 => array( 
				"threshold" => 3000, 
				"multiplier" => 1.5, 
			), 
			4 => array( 
				"threshold" => 8000, 
				"multiplier" => 1.75, 
			), 
			5 => array( 
				"threshold" => 16000, 
				"multiplier" => 2, 
			), 
			6 => array( 
				"threshold" => 25000, 
				"multiplier" => 2, 
			), 
		), 
		"warnings" => array( 
			1 => array( 
				"schenkingen" => 30,
				"transactiediversiteit" => 0.4,
				"credits" => 2400,
				"waardering" => 2.5, 
				"physical" => 5.0, 
				"social" => 50,
				"mental" => 50,
				"emotional" => 50,
				"indicatorsom" => 220,
			), 
			2 => array( 
				"schenkingen" => 60,
				"transactiediversiteit" => 0.25, 
				"credits" => 3600,
				"waardering" => 2,
				"physical" => 30,
				"social" => 30,
				"mental" => 30,
				"emotional" => 30, 
				"indicatorsom" => 130, 
			), 
			3 => array( 
				"schenkingen" => 80, 
				"transactiediversiteit" => 0.1, 
				"credits" => 4200,
				"waardering" => 1.5, 
				"physical" => 10,
				"social" => 10,
				"mental" => 10,
				"emotional" => 10,
				"indicatorsom" => 50,
			), 
			4 => array( 
				"schenkingen" => 100,
				"transactiediversiteit" => 0.05, 
				"credits" => 4700,
				"waardering" => 1,
				"physical" => 0,
				"social" => 0,
				"mental" => 0,
				"emotional" => 0,
				"indicatorsom" => 10,
			), 
		), 
		"crons" => array( 
			"indicators" => 86400,
			"hourstoworkfordelay" => 4,
			"x" => NULL
		), 
		"date" => array( 
			"speed" => 1,
			"start" => 1395100800, 
			"timezone" => "Europe/Brussels",
		), 
		"geo" => array( 
			"latitude" => 50.8492265, 
			"longitude" => 2.8779388,
		), 
		"debugging" => array( 
			"showwarnings" => TRUE,
			"demo" => FALSE,
		), 
		"credits" => array( 
			"min" => 0, 
			"max" => 9600, 
			"name" => array( 
				"1" => "owa",
				"x" => "owa",
				"overdracht" => "owa-overdracht",
			), 
		), 
		"verzekeringen" => array( 
			1 => "Schade aan derden verzekerd",
			2 => "Arbeidsongevallen-verzekering afgesloten",
		), 
		"analytics" => "", 
		"indicatoren" => array( 
			"multiplier" => 10, 
			"owaesadd" => 5, 
		), 
		"mail" => array( 
			"smtp" => FALSE,
			"Host" => "",
			"SMTPAuth" => FALSE,
			"SMTPSecure" => "",
			"Port" => "",
			"Username" => "",
			"Password" => NULL,
		), 
		"facebook" => array( 
			"loginapp" => array( 
				"id" => NULL, 
				"secret" => NULL, 
			), 
		), 
		"mailalert" => array( 
			"newmessage" => 86400,
			"newsubscription" => 86400,
			"platform" => 1, 
			"reminderunread" => 259200,
			"remindersubscription" => 259200,
		)
	);   