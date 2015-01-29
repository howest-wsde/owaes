<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$oSecurity = new security(TRUE); 
	$oMe = user(me()); 

	$iMarket = $_GET["m"]; 
	$oMarket = owaesitem($iMarket); 	
	
	if (!isset($_POST["cancel"])) { 
		if (isset($_POST["akkoord"])) {  
			if ($_POST["akkoord"]==1) $oMarket->addSubscription(me(), SUBSCRIBE_SUBSCRIBE);  
			exit();
		}  
	} 
	
	echo ($oMarket->html("modal.subscribe.html"));  
?>