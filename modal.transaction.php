<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	
	$iMarket = intval($_GET["m"]); 
	$oMarket = owaesitem($iMarket); 
	
	$iUser = intval($_GET["u"]); 
	$oUser = user($iUser); 
	
	$oMe = user(me()); 
	$iMax = $oMe->credits();  
	
	if (isset($_POST["cancel"])) { 
		$oActions = new actions(me()); 
		$oAction = $oActions->search(array(
				"type" => "transaction", 
				"user" => $iUser, 
				"market" => $iMarket, 
			));  
		if ($oAction) { 
			$iPostPone = isset($_POST["postpone"]) ? intval($_POST["postpone"]) : 2; 
			if ($iPostPone == 0) $iPostPone = 1/24/60*2; // 2 minuten
			//echo $iPostPone; 
			$oAction->tododate(owaestime() + ($iPostPone*24*60*60)); // x dagen
			$oAction->update();   
		}
		exit(); 
	} else if (isset($_POST["market"])) {  
		$iCredits = intval($_POST["credits"]); 
		if ($iCredits > 0) {
			if ($iCredits > $iMax) $iCredits = $iMax; 
			foreach ($oMarket->subscriptions(array("state"=>SUBSCRIBE_CONFIRMED)) as $iParty=>$oSubscription) {
				$oPayment = $oSubscription->payment(); 
				if ($oPayment->sender() == me()) {
					if ($oPayment->receiver() == $iUser) {
						$oPayment->reason(1); 
						$oPayment->credits($iCredits); 
						$oPayment->signed(TRUE); 
						
						switch($_POST["voorschot"]) {
							case "voorschot": 
								break; 
							default: 
								$oSubscription->state(SUBSCRIBE_FINISHED);
								$oSubscription->save(); 
						}
						 
						$iExperience = 100; 
						
						$oExpSender = new experience($oPayment->sender());
						$oExpSender->sleutel("owaes." . $oMarket->id());
						$oExpSender->detail("reason", "overdracht credits");
						$oExpSender->detail("receiver", $oPayment->receiver());
						$oExpSender->detail("receiver name", user($oPayment->receiver())->getName());
						$oExpSender->add($iExperience);  
						 
						$oExpReceiver = new experience($oPayment->receiver());
						$oExpReceiver->sleutel("owaes." . $oMarket->id());
						$oExpReceiver->detail("reason", "overdracht credits");
						$oExpReceiver->detail("sender", $oPayment->sender());
						$oExpReceiver->detail("sender name", user($oPayment->sender())->getName());
						$oExpReceiver->add($iExperience);  
						
						$oAction = new action($iUser);
						$oAction->type("feedback");   
						$oAction->data("market", $iMarket); 
						$oAction->data("user", me()); 
						$oAction->tododate(owaestime() + (3*24*60*60));  
						$oAction->update(); 
						
						$oAction = new action(me());
						$oAction->type("feedback");   
						$oAction->data("market", $iMarket); 
						$oAction->data("user", $iUser); 
						$oAction->tododate(owaestime() + (3*24*60*60));  
						$oAction->update(); 
						 
						user($iParty)->addbadge("done-" . $oMarket->type()->key()); 
							  
					} 
				}
			} 		 
			 
			$oActions = new actions(me()); 
			$oAction = $oActions->search(array(
					"type" => "transaction", 
					"user" => $iUser, 
					"market" => $iMarket, 
				));  
			if ($oAction) {
				$oAction->done(owaestime()); 
				$oAction->update();  
			}
		}
		exit();  
	} 
	
	$oTemplate = template("modal.transaction.html"); 
	$oTemplate->tag("max", $iMax); 
	$strHTML = $oTemplate->html(); 
	$strHTML = $oMarket->html($strHTML);
	$strHTML = $oUser->html($strHTML); 
	
//	$strHTML = str_replace("[credit]", 1515515151, $strHTML); 
	
	echo $strHTML; 
	
?>