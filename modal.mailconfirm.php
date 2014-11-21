<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	
	$iMarket = intval($_GET["m"]); 
	$arUsers = explode(",", $_GET["u"]);  
	$bGoedgekeurd = ($_GET["s"]==1); 
	$oMarket = owaesitem($iMarket); 
	
	if ($oMarket->author()->id() != $oSecurity->me()->id()) exit();  // check if rights to be here
	
	if (isset($_POST["msg"])) {  
		foreach ($arUsers as $iUser) { 
			$oMarket->addSubscription(intval($iUser), ( $bGoedgekeurd ? SUBSCRIBE_CONFIRMED : SUBSCRIBE_DECLINED )); 
			$oConversation = new conversation(intval($iUser)); 
			$oConversation->add($_POST["msg"], $iMarket ); 
		} 
		echo ("ok"); 
		exit(); 
	}
	 
	
	$oHTML = new template("modal.mailconfirm.html");
	$oHTML->tag("market:id", $iMarket); 
	  

	
	$strUsers = ""; 	
	for ($i=0; $i<count($arUsers); $i++) {
		switch ($i) {
			case 0:
				$strUsers .= user($arUsers[$i])->getName (); 
				break; 
			case count($arUsers)-1: 
				$strUsers .= " en " . user($arUsers[$i])->getName ();
				break; 
			default: 
				$strUsers .= ", " . user($arUsers[$i])->getName ();
		}
	}
	
	$oHTML->tag("userids", implode(",", $arUsers));
	$oHTML->tag("users", $strUsers);
	
	if ($bGoedgekeurd) {
		$oHTML->tag("goedkeuren-afkeuren", (count($arUsers) == 1 ? "Bevestigde gebruiker" : "Bevestigde gebruikers")); 
		$oHTML->tag("defaultmail", new template("modal.mailconfirm.mailconfirm.html")); 
	} else {
		$oHTML->tag("goedkeuren-afkeuren", (count($arUsers) == 1 ? "Geweigerde gebruiker" : "Geweigerde gebruikers")); 
		$oHTML->tag("defaultmail", new template("modal.mailconfirm.maildeny.html")); 
	} 
	 
	$strHTML = $oHTML->html(); 
	 
	switch($oMarket->type()->key()) {
		case "ervaring": 
			$strHTML = str_replace("[owaestype]", "deze werkervaring", $strHTML); 
			break; 
		case "opleiding": 
			$strHTML = str_replace("[owaestype]", "deze opleiding", $strHTML); 
			break; 
		default: 
			$strHTML = str_replace("[owaestype]", "dit aanbod", $strHTML); 
	} 
	
	echo $strHTML; 
	
?>