<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('short_open_tag', 'On');
	
	ob_start();
	
	$i_GLOBAL_starttijd = time(); 

	include "inc.config.php"; 
	include "inc.functions.php"; // handy functions (date, filehandling, ...) 
	
	if (file_exists("inc.config.db.php")) {
		include "inc.config.db.php"; 
	} else {
		loadsetup();  
	}
	
	include "inc.classes.php";   // loads all classes 
	
	// $oUser = new user();         // will be used as global in some classes
	$oPage = new page(); 		 // will be used as global in some classes

	define ("YES", 1); 
	define ("NO", 0); 
	
	if (get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}
	
	set_error_handler('owaes_error_handler');  
