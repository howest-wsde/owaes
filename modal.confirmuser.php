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
				
				if (intval($oUser->data("stagemarkt")) > 0) {
					$oDB = new database(); 
					$oDB->execute("select * from tblStagemarkt where id = " . intval($oUser->data("stagemarkt")) . ";");  
					$oGroep = new group(); 
					$oGroep->naam($oDB->get("groepsnaam"));
					$oGroep->info($oDB->get("description"));
					$oGroep->admin($oUser->id());
					$oGroep->update();   
					$oGroep->addUser($oUser->id()); 
					if ($oDB->get("logo")!="") createGroupPicture($oDB->get("logo"), $oGroep->id()); 
				}
				
				$oAction->done(TRUE);
				$oAction->update();  
				break; 
			case "decline": 
				if (user(me())->admin()) {
					$oUser->delete(TRUE);  
				} else {
					$oUser->dienstverlener(0); 
					$oUser->update(); 
					
					$arAdmins = new userlist(); 
					$arAdmins->filter("admin"); 
					foreach ($arAdmins->getList() as $oAdmin) {
						$oAction = new action($oAdmin->id()); 
						$oAction->type("validateuser");  
						$oAction->data("user", $iUser); 
						$oAction->data("declinedby", me()); 
						$oAction->tododate(owaestime()); 
						$oAction->update();  
					} 
				}
				$oAction->done(TRUE);
				$oAction->update();   
				break; 	
		}  
		exit();   
	} 
	
	$oTemplate = template("modal.confirmuser.html"); 
	if (isset($_GET["d"])) {
		$oTemplate->tag("gegevens", "<dt>Naam: </dt><dd>[firstname] [lastname]</dd><dt>Doorgestuurd door: </dt><dd>" . user($_GET["d"])->getName() . "</dd>"); 
	} else {
		$oTemplate->tag("gegevens", "<dt>Naam: </dt><dd>[firstname] [lastname]</dd>"); 
	}
	if ($oUser->dienstverlener()->id() > 0){
		$oTemplate->tag("dienstverlener", $oUser->dienstverlener()->naam()); 
	} else {
		$oTemplate->tag("dienstverlener", "OWAES"); 
	}
	//$oTemplate->tag("max", $iMax); 
	$strHTML = $oTemplate->html(); 
//$strHTML = $oMarket->html($strHTML);
	$strHTML = $oUser->html($strHTML); 
	
//	$strHTML = str_replace("[credit]", 1515515151, $strHTML); 
	
	echo $strHTML; 
	
?>