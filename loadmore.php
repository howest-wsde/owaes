<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);   
	
	$strTemplate = $_POST["template"]; 
	$bHasNext = FALSE; 
	
	switch($_POST["list"]) {
		case "friends": 
			$oUser = user($_POST["user"]); 
			$iCurrent = 1; 
			$iStart = intval($_POST["start"]); 
			$iStop = intval($_POST["start"]) + intval($_POST["count"]); 
			foreach ($oUser->friends() as $oFriend) {
				if ($iCurrent >= $iStart && $iCurrent < $iStop) {
					echo $oFriend->html($strTemplate); 
				} 
				if ($iCurrent >= $iStop) $bHasNext = TRUE; 
				$iCurrent ++; 	
			}
			break; 	
			
		case "payments": 
			$oUser = user($_POST["user"]); 
			$iCurrent = 1; 
			$iStart = intval($_POST["start"]); 
			$iStop = intval($_POST["start"]) + intval($_POST["count"]); 
			foreach ($oUser->payments("all") as $oPayment) {
				if ($iCurrent >= $iStart && $iCurrent < $iStop) {
					echo $oPayment->html($strTemplate); 
				} 
				if ($iCurrent >= $iStop) $bHasNext = TRUE; 
				$iCurrent ++; 	
			}
			break; 	
			
		case "activities":  
			$iCurrent = 1; 
			$iStart = intval($_POST["start"]); 
			$iStop = intval($_POST["start"]) + intval($_POST["count"]); 

			$oList = new owaeslist(); 
			$oList->filterByUser($_POST["user"]);   
			foreach ($oList->getList() as $oActivity) {
				if ($iCurrent >= $iStart && $iCurrent < $iStop) {
					echo $oActivity->html($strTemplate); 
				} 
				if ($iCurrent >= $iStop) $bHasNext = TRUE; 
				$iCurrent ++; 	
			}
			break; 	
			
		case "market":  
			$iCurrent = 1; 
			$iStart = intval($_POST["start"]); 
			$iStop = intval($_POST["start"]) + intval($_POST["count"]); 

			$oList = new owaeslist(); 
			$oList->filterByGroup($_POST["group"]); 
			foreach ($oList->getList() as $oActivity) {
				if ($iCurrent >= $iStart && $iCurrent < $iStop) {
					echo $oActivity->html($strTemplate); 
				} 
				if ($iCurrent >= $iStop) $bHasNext = TRUE; 
				$iCurrent ++; 	
			}
			break; 	
			
		case "members":  
			$iCurrent = 1; 
			$iStart = intval($_POST["start"]); 
			$iStop = intval($_POST["start"]) + intval($_POST["count"]); 

			$oGroup = group($_POST["group"]);  
			foreach ($oGroup->users() as $oUser) {
				if ($iCurrent >= $iStart && $iCurrent < $iStop) {
					echo $oUser->html($strTemplate); 
				} 
				if ($iCurrent >= $iStop) $bHasNext = TRUE; 
				$iCurrent ++; 	
			}
			break; 	
			
		default: 
			
	} 
	
	if (!$bHasNext) echo ("<!-- EOL -->");  
?>