<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);   
	
	$iUser = intval($_GET["u"]); 
	$oUser = user($iUser);  
	
	if (isset($_POST["cancel"])) { 
		$oActions = new actions(me()); 
		$oAction = $oActions->search(array(
				"type" => "validateuser", 
				"user" => $iUser,  
			));  
		if ($oAction) {
			$oAction->tododate(owaestime() + (60*60)); // 1 uur
			$oAction->update();  
		}
		exit(); 
	} else if (isset($_POST["action"])) {   
		$oActions = new actions(me()); 
		$oAction = $oActions->search(array(
				"type" => "validateuser", 
				"user" => $iUser,  
			));  
		switch($_POST["action"]) {
			case "confirm": 
				$oUser->algemenevoorwaarden(1); 
				$oUser->update(); 
				$oAction->done(TRUE);
				$oAction->update();  
				break; 
			case "decline": 
				$oUser->dienstverlener(0); 
				$oUser->update(); 
				$oAction->done(TRUE);
				$oAction->update();  
				break; 	
		}  
		exit();   
	} 
	
	$oTemplate = template("modal.confirmuser.html"); 
	//$oTemplate->tag("max", $iMax); 
	$strHTML = $oTemplate->html(); 
//$strHTML = $oMarket->html($strHTML);
	$strHTML = $oUser->html($strHTML); 
	
//	$strHTML = str_replace("[credit]", 1515515151, $strHTML); 
	
	echo $strHTML; 
	
?>