<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security();
	
	
	/* POST: 
	comment	test
m	91
market	[market:id]
reason	twist
refresh	
user	[user:id]

*/  
	if (isset($_POST["reason"])) {
		$oReport = new report($_POST["reason"]); 
		$oReport->user(intval($_POST["u"]));
		$oReport->market(intval($_POST["m"]));
		$oReport->data("comment", $_POST["comment"]);
		$oReport->update(); 
		echo "ok"; 
		exit(); 
	}
	
	$arData = array(); 
	$arReasons = array(); 
	
	$strAlert = isset($_GET["a"]) ? $_GET["a"] : "Fout!"; 
	$strTitel = isset($_GET["t"]) ? $_GET["t"] : "Fout"; 
	  
	$oLayout = new template("modal.report.html");
	$oLayout->tag("alert", $strAlert);  
	$oLayout->tag("title", $strTitel);  
	
	$arReasons["foutgebruik"] = "Oneigenlijk gebruik van het platform"; 
	
	if (isset($_GET["u"])) {
		$arData[] = "<strong>Gebruiker:</strong> " . user($_GET["u"])->getName() . "<input type=\"hidden\" name=\"u\" value=\"" . $_GET["u"] . "\" />";
		$arReasons["unrealperson"] =  user($_GET["u"])->getName() . " is geen echt persoon"; 
		$arReasons["twist"] =  "Heeft een belofte niet nagekomen"; 
	}
	if (isset($_GET["m"])) {
		$arData[] = "<strong>Aanbod:</strong> " . owaesitem($_GET["m"])->title() . "<input type=\"hidden\" name=\"m\" value=\"" . $_GET["m"] . "\" />";
		$arReasons["twist"] = "Belofte werd niet nagekomen"; 
	}
	
	$strDropdown = "<strong>Reden:</strong> <select name=\"reason\" class=\"form-control\">"; 
	foreach ($arReasons as $strKey=>$strReason) {
		if ((isset($_GET["reason"])?$_GET["reason"]:"") == $strKey) {
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