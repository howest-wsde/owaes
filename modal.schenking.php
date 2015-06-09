<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	  
	$iUser = intval($_GET["u"]); 
	$oUser = user($iUser); 
	
	$oMe = user(me()); 
	
	if (!$oMe->levelrights("donate")) exit(); 
	
	$iMax = $oMe->credits() - settings("credits", "donation", "limit"); 
	if ($iMax < 0) $iMax = 0; 
	
	if (isset($_POST["credits"])) {   
		$iCredits = intval($_POST["credits"]); 
		if ($iCredits > 0 && $iCredits <= $iMax) {
			$oPayment = new payment(); 
			$oPayment->sender(me()); 
			$oPayment->receiver($iUser);  
			$oPayment->reason(1); 
			$oPayment->credits($iCredits); 
			$oPayment->signed(TRUE);  

			$oExpReceiver = new experience(me()); 
			$oExpReceiver->detail("reason", "schenking"); 
			$oExpReceiver->add(90);  

			if ($_POST["comment"] != "") {
				$oConversation = new conversation($iUser); 
				$oConversation->add($_POST["comment"]);  
			}
		}
		exit();  
	} 
 
 	$oTekst = template("modal.schenking.html"); 
	$oTekst->tag("max", $iMax); 
	$strHTML = $oUser->html($oTekst->html()); 
	
//	$strHTML = str_replace("[credit]", 1515515151, $strHTML); 
	
	echo $strHTML; 
	
?>