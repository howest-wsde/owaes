<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security();
	 
	if (isset($_POST["reason"])) {
		$oReport = new report($_POST["reason"]); 
		$oReport->user(isset($_POST["u"]) ? intval($_POST["u"]) : 0);
		$oReport->market(isset($_POST["m"]) ? intval($_POST["m"]) : 0);
		$oReport->data("comment", $_POST["comment"]);
		$oReport->update(); 
		echo "ok"; 
		
		switch($_POST["reason"]) {
			case "twist": 
				if (isset($_POST["u"]) && isset($_POST["m"])) {
					owaesitem($_POST["m"])->addSubscription($_POST["u"], SUBSCRIBE_ANNULATION); 
				} 
				break; 	
		}
			
		exit(); 
	}
	
	$strSetReason = isset($_GET["reason"])?$_GET["reason"]:""; 
	
	$arData = array(); 
	$arReasons = array(); 
	
	$strAlert = isset($_GET["a"]) ? $_GET["a"] : "Fout!"; 
	$strTitel = isset($_GET["t"]) ? $_GET["t"] : "Fout"; 
	  
	$oLayout = new template("modal.report.html");
	$oLayout->tag("alert", $strAlert);  
	$oLayout->tag("title", $strTitel);  
	
	$arReasons["foutgebruik"] = "Oneigenlijk gebruik van het platform"; 
	
	switch($strSetReason) {
		case "annulation": 	
			$arReasons["foutgebruik"] = "De inschrijving werd zonder afspraak geannuleerd"; 
			break; 
	}
	
	if ((isset($_GET["reason"])?$_GET["reason"]:"") == "twist") $arData[] = "<p><strong>Indien u kiest voor de optie \"afspraak niet nagekomen\" betekent dit dat er geen prestatie geleverd is. Deze taak wordt afgesloten voor deze gebruiker en er gebeurt geen transactie of waardering.</strong></p>"; 
	
	if (isset($_GET["u"])) {
		$arData[] = "<strong>Gebruiker:</strong> " . user($_GET["u"])->getName() . "<input type=\"hidden\" name=\"u\" value=\"" . $_GET["u"] . "\" />";
		$arReasons["unrealperson"] =  user($_GET["u"])->getName() . " is geen echt persoon"; 
		$arReasons["twist"] =  "Heeft een afspraak niet nagekomen"; 
	}
	if (isset($_GET["m"])) {
		$arData[] = "<strong>Aanbod:</strong> " . owaesitem($_GET["m"])->title() . "<input type=\"hidden\" name=\"m\" value=\"" . $_GET["m"] . "\" />";
		$arReasons["twist"] = "Een gemaakte afspraak werd niet nagekomen"; 
	}
	
	$strDropdown = "<strong>Reden:</strong> <select name=\"reason\" class=\"form-control\">"; 
	foreach ($arReasons as $strKey=>$strReason) {
		if ($strSetReason == $strKey) {
			$strDropdown .= "<option selected=\"selected\" value=\"$strKey\">$strReason</option>"; 
		} else {
			$strDropdown .= "<option value=\"$strKey\">$strReason</option>"; 
		}
	}
	$strDropdown .= "</select>";
	$arData[] = $strDropdown;  
	
	$strData = "<ul>
                    <li>" . implode("</li><li>", $arData) . "</li> 
                </ul>"; 
				
	$oLayout->tag("data", $strData);  
	 
	echo $oLayout->html(); 
	
?>