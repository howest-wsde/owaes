<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);   

	$oActions = new actions(me());  
	$oAction = $oActions->search(array(
			"type" => "location"
		));  
	if ($oAction) {
		$oAction->tododate(owaestime() + (7*24*60*60)); // 7 dagen
		$oAction->update();  
	}
		 
	$oTemplate = template("modal.location.html"); 
	$oTemplate->tag("target", "settings.php"); 
	$strHTML = $oTemplate->html(); 
	
	echo $strHTML; 
	
?>