<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	$oLog = new log("add friend", array("url" => $oPage->filename())); 
	 
	$oUser = user($_GET["u"]);  
	if ($oUser->isFriend()) {
		$oUser->addFriend(); 
	} else {
		$oUser->addFriend(FALSE); 
	}
	
	$strReturn = (isset($_POST["return"])) ? $_POST["return"] : ""; 
	switch($strReturn) {
		case "item": 
			echo $oUser->HTML("templates/userfromlist.html");  
			break; 
		default: 
			header("location:" . $_SERVER["HTTP_REFERER"]);
			break; 	
	}
?>