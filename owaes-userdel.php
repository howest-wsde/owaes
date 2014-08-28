<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE, AJAX); 
	
	$iOwaes = intval($_GET["m"]); 
	$iUser = intval($_GET["u"]); 
 
 	$oOwaes = new owaesitem($iOwaes);
	$oOwaes->addSubscription($iUser, -1); 
	
	$oConversation = new conversation($iUser); 
	$oConversation->add("u werd niet gekozen voor deze opdracht", $oOwaes->title()); 
	
	echo ("done"); 
?>