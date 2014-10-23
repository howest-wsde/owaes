<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	$oLog = new log("add friend", array("url" => $oPage->filename()));  
	 
	$oUser = user($_GET["u"]);  
	
	switch($_GET["action"]) {
		case "add": 
			$oUser->addFriend(); 
			break; 
		case "del": 
			$oUser->removeFriend(); 
			break; 	
	}
	/*
	if ($oUser->isFriend()) {
		
	} else {
		$oUser->addFriend(FALSE); 
	}
	*/
	
	if (isset($_GET["ajax"])) { 
		echo $oUser->HTML("userfromlist.html");  
	} else {  
		redirect($_SERVER["HTTP_REFERER"]);
	}
?>