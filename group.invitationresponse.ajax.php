<?php
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
		 
		/* extra message voor admins */
		$arAdmins = new userlist(); 
		$arAdmins->filter("admin"); 
		foreach ($arAdmins->getList() as $oAdmin) {
			$oMessage = new message(); 
			$oMessage->sender(0); 
			$oMessage->receiver($oAdmin->id()); 
			$oMessage->body(user(me())->getName() . " heeft lidmaatschap voor de groep " . $oGroup->naam() . " geweigerd"); 
			$oMessage->data("group", $oGroup->id()); 
			$oMessage->data("user", $iAlertPerson); 
			$oMessage->data("reporter", me());  
			$oMessage->update(); 
		}
		/* STOP extra message voor admins */

	}

	$oNotification->send(); 
	
	echo "ok";  
?> 