<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	  
	$iUser = intval($_GET["u"]); 
	$oUser = user($iUser); 
	
	if (isset($_POST["credits"])) {   
		$iCredits = intval($_POST["credits"]); 
		if ($iCredits > 0) {
			$oPayment = new payment(); 
			$oPayment->sender(me()); 
			$oPayment->receiver($iUser);  
			$oPayment->reason(1); 
			$oPayment->credits($iCredits); 
			$oPayment->signed(TRUE); 

			if ($_POST["comment"] != "") {
				$oConversation = new conversation($iUser); 
				$oConversation->add($_POST["comment"]);  
			}
		}
		exit();  
	} 
 
	$strHTML = $oUser->html("modal.schenking.html"); 
	
//	$strHTML = str_replace("[credit]", 1515515151, $strHTML); 
	
	echo $strHTML; 
	
?>