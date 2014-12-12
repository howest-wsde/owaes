<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE, AJAX); 
   
	$iMarket = isset($_GET["m"]) ? intval($_GET["m"]) : 0; 
	$iUser = isset($_GET["u"]) ? intval($_GET["u"]) : 0; 
	
	$oMarket = owaesitem($iMarket);  
	$oUser = user($iUser); 
	
	if ($oMarket->userrights("select", me())) {
		$oMarket->addSubscription($iUser, SUBSCRIBE_ANNULATION); 
		
		$strMSG = "Uw inschrijving werd geannuleerd. "; 
		$oMessage = new message(); 
		$oMessage->receiver($iUser); 
		$oMessage->body($strMSG);   
		$oMessage->market($iMarket);  
		//$oMessage->data("reporter", me());    
		$oMessage->data("report", "annulation." . me() . ".$iMarket" );     
		$oMessage->update();  
	}
	
	redirect();  
?>