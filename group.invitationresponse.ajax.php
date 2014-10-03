<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	 
	$iGroep = intval($_POST["g"]);  
	$oGroup = group($iGroep); 
	$oRechten = new usergrouprights(group($iGroep), me()); 
	$iAlertPerson = $oRechten->value("invitedby"); 

	$oNotification = new notification($iAlertPerson, "group." . $iGroep); 
	$oNotification->sender(me());  
	$oNotification->link($oGroup->getURL()); 

	if ($_POST["a"] == "1") { 
		if ($oRechten->value("confirmed") === FALSE) {
			$oRechten->value("confirmed", TRUE); 
			$oRechten->update(); 
			
			$oNotification->message(user(me())->getName() . " heeft lidmaatschap voor de groep " . $oGroup->naam() . " bevestigd"); 
		}
	} else {  
		$oGroup->removeUser(me()); 
		$oNotification->message(user(me())->getName() . " heeft lidmaatschap voor de groep " . $oGroup->naam() . " geweigerd"); 
	}

	$oNotification->send(); 
	
	echo "ok";  
?> 