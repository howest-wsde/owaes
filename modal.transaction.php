<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	
	$iMarket = intval($_GET["m"]); 
	$oMarket = owaesitem($iMarket); 
	
	$iUser = intval($_GET["u"]); 
	$oUser = user($iUser); 
	
	if (isset($_POST["cancel"])) { 
		$oActions = new actions(me()); 
		$oAction = $oActions->search(array(
				"type" => "transaction", 
				"user" => $iUser, 
				"market" => $iMarket, 
			));  
		if ($oAction) {
			$oAction->tododate(owaestime() + (2*24*60*60)); // 2 dagen
			$oAction->update();  
		}
		exit(); 
	} else if (isset($_POST["market"])) {  
	/*
		$iStars = intval(isset($_POST["score"]) ? $_POST["score"] : $_GET["score"]);  
		$iMarket = intval(isset($_POST["market"]) ? $_POST["market"] : $_GET["market"]);  
		$iReceiver = intval(isset($_POST["receiver"]) ? $_POST["receiver"] : $_GET["receiver"]);  
		$strComment = isset($_POST["comment"]) ? $_POST["comment"] : (isset($_GET["comment"]) ? $_GET["comment"] : "");  
	 
		$oRating = new rating(array(
							"market" => $iMarket, 
							"sender" => me(), 
							"receiver" => $iReceiver,
						)); 
		$oRating->stars($iStars); 
		$oRating->comment($strComment); 
		$oRating->rated(TRUE);  
		 
		 */
		$iCredits = intval($_POST["credits"]); 
		if ($iCredits > 0) {
		
			foreach ($oMarket->subscriptions(array("state"=>SUBSCRIBE_CONFIRMED)) as $iParty=>$oSubscription) {
				$oPayment = $oSubscription->payment(); 
				if ($oPayment->sender() == me()) {
					if ($oPayment->receiver() == $iUser) {
						$oPayment->reason(1); 
						$oPayment->credits($iCredits); 
						$oPayment->signed(TRUE); 
						
						$oSubscription->state(SUBSCRIBE_FINISHED);
						$oSubscription->save(); 
						
						//$iExperience = $oMarket->timing()*600; 
						//if ($iExperience == 0) $iExperience = $oPayment->credits()*10;
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

	$strHTML = $oMarket->html("modal.transaction.html");
	$strHTML = $oUser->html($strHTML); 
	
//	$strHTML = str_replace("[credit]", 1515515151, $strHTML); 
	
	echo $strHTML; 
	
?>