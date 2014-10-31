<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE, (isset($_GET["ajax"]))?AJAX:PAGE); 
	
	
	$iOwaes = intval($_GET["m"]);
	$iType = intval($_GET["t"]); 
 
 	$oOwaes = owaesitem($iOwaes);
	$oOwaes->addSubscription($oSecurity->getUserID(), $iType);  
	
	if (isset($_GET["ajax"])) {
		echo $oOwaes->subscriptionDiv(); 
	} else {
		header('Location: ' . $oOwaes->getLink()); 
	}
?>