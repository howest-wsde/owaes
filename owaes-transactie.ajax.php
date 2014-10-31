<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE, AJAX); 
   
	$iMarket = isset($_GET["owaes"]) ? intval($_GET["owaes"]) : 0; 
	$iUser = isset($_GET["user"]) ? intval($_GET["user"]) : 0; 
	
	$oOwaesItem = owaesitem($iMarket);  
	$oUser = user($iUser);  
	 
	/*
	if ($iMarket != 0) {
		if ($oOwaesItem->author()->id() != me()) { // if not author of the job
			$arSubscriptions = $oOwaesItem->subscriptions(); 
			if ($arSubscriptions[me()]->state() != SUBSCRIBE_CONFIRMED)  { // and not confirmed participator 
				$oSecurity->doLogout(); 
				exit(); 
			} 
		} 
	}
	*/
	
	if (isset($_POST["type"])) {
		switch($_POST["type"]) {
			case "finish": 
			
				foreach ($oOwaesItem->subscriptions(array("state"=>SUBSCRIBE_CONFIRMED)) as $iUser=>$oSubscription) {
					$oPayment = $oSubscription->payment(); 
					if ($oPayment->sender() == me()) {
						if ($oPayment->receiver() == $_POST["user"]) {
							$oPayment->reason(1); 
							$oPayment->signed(TRUE); 
							
							$iExperience = $oOwaesItem->timing()*600; 
							if ($iExperience == 0) $iExperience = $oPayment->credits()*10;
							
							$oExpSender = new experience($oPayment->sender());
							$oExpSender->sleutel("owaes." . $oOwaesItem->id());
							$oExpSender->detail("reason", "overdracht credits");
							$oExpSender->detail("receiver", $oPayment->receiver());
							$oExpSender->detail("receiver name", user($oPayment->receiver())->getName());
							$oExpSender->add($iExperience);  
							 
							$oExpReceiver = new experience($oPayment->receiver());
							$oExpReceiver->sleutel("owaes." . $oOwaesItem->id());
							$oExpReceiver->detail("reason", "overdracht credits");
							$oExpReceiver->detail("sender", $oPayment->sender());
							$oExpReceiver->detail("sender name", user($oPayment->sender())->getName());
							$oExpReceiver->add($iExperience);  
						} 
					}
				} 
			case "donate": 
				$oPayment = new payment(array(
							"receiver" => $iUser, 
							"credits" => intval($_POST["credits"]), 
					)); 
				$oPayment->signed(TRUE);
				
				$oConversation = new conversation($iUser);   
				$oConversation->add($_POST["message"]);  

		}
		exit(); 	
	}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head> 
    </head>
    <body>
    	<script>
			function transactie(oObj) { 
				if (isNaN(oObj)) {  // oObj = Form  
					frm = oObj; 
					$.post("owaes-transactie.ajax.php?user=<? echo $iUser; ?>", {"message":frm.message.value, "credits":frm.credits.value, "type":"donate"}, function(data){ 
						location.reload(); 
					}); 
				} else { // oObj = Number
					iUser = oObj; 
					$.post("owaes-transactie.ajax.php?owaes=<? echo $iMarket; ?>", {"user":iUser, "type":"finish"}, function(data){
						location.reload(); 
					}); 
				}
				return false; 
			}
		</script>
        <form method="post" onsubmit="return transactie(this); ">  
            <? 
				foreach ($oOwaesItem->subscriptions(array("state"=>SUBSCRIBE_CONFIRMED)) as $iItemUser=>$oSubscription) {
					$oPayment = $oSubscription->payment(); 
					if ($oPayment->sender() == me()) {
						if ($oPayment->signed()) {
							?><div> 
								<h2><? echo $oPayment->credits(); ?> credits overgedragen naar <? echo user($oPayment->receiver())->getName(); ?></h2> 
							</div><?
						} else {
							?><div> 
								<a href="#" onclick="return transactie(<? echo $oPayment->receiver(); ?>); "><h2>Draag <? echo $oPayment->credits(); ?> credits over naar <? echo user($oPayment->receiver())->getName(); ?></h2> </a> 
							</div><?
						}
					} else if ($oPayment->receiver() == me()) {
						?><div>
							<h2>Ik moet nog <? echo $oPayment->credits(); ?> credits krijgen van <? echo user($oPayment->sender())->getName(); ?></h2> 
							TODO: bericht sturen
						</div><?
					}
				}
				
				if ($iUser != 0) {
					echo ("
							<div>Schenk credits aan " . $oUser->getName() .  ": </div>
							<textarea name=\"message\"></textarea>
							<input type=\"number\" name=\"credits\" value=\"60\" />
							<input type=\"submit\" value=\"schenken\" />
						"); 	
				}
			?>

        </form>

    </body>
</html>
