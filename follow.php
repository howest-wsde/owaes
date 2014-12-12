<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	$oLog = new log("follow", array("url" => $oPage->filename()));  
	 
	$iUser = intval($_GET["u"]);  
	
	switch($_GET["action"]) {
		case "add": 
			user(me())->follows($iUser, TRUE); 
			break; 
		case "del": 
			user(me())->follows($iUser, FALSE); 
			break; 	
	} 
	
	if (isset($_GET["ajax"])) { 
		echo user($iUser)->HTML("userfromlist.html");  
	} else {  
		redirect($_SERVER["HTTP_REFERER"]);
	}
?>