<?
	// get/owaes?help => help
	
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security();  
	
	$strFormat = $_GET["format"]; 
	$strFile = $_GET["file"]; 
	$arInfo = array();
	$arInfo["timestamp"] = owaesTime(); 
	 
	$arResult = array(); 
	$arError = array(); 
	
	if (isset($_GET["help"])) {
		$arInfo["help"] = array(
			"request" => array(
				"get/owaes" => "", 
				"get/FORMAT/owaes" => "xml / json / txt / html", 
				"get/FORMAT/owaes/?PARAMETERS" => "zie onder", 
				"get/owaes/TEMPLATE.HTML" => "use template uit template-folder", 
				"get/FORMAT/owaes/TEMPLATE.HTML" => "include template uit template-folder", 
			), 
			"parameters" => array(
				"token" => array(
					"format" => "string", 
					"info" => "token van de gebruiker", 
				), 
				"type" => array(
					"format" => "string", 
					"accepted" => "'market' / 'work'", 
					"info" => "", 
				), 
				"author" => array(
					"format" => "integer", 
					"info" => "enkel berichten van deze author (id) worden getoond", 
				), 
				"group" => array(
					"format" => "integer", 
					"info" => "enkel berichten van deze groep worden getoond", 
				), 
				"status" => array(
					"format" => "string / integer", 
					"accepted" => "recrute / 0 , selected / 1 , finished / 2, open", 
					"info" => "", 
				), 
				"subscribed" => array(
					"format" => "string", 
					"accepted" => "'YES', 'CONFIRMED', 'NOTCONFIRMED'", 
					"info" => "enkel berichten waar de huidige gebruiker wel/niet voor ingeschreven is. Token verplicht! ", 
				), 
				"payed" => array(
					"format" => "string", 
					"accepted" => "'YES', 'NO'", 
					"info" => "berichten filteren naargelang de creditsoverdracht gebeurd is. (yes = transactie is gebeurd, no = transactie moet nog gebeuren).  Token verplicht! ", 
				), 
				"rating" => array(
					"format" => "string", 
					"accepted" => "'YES', 'NO'", 
					"info" => "berichten filteren naargelang de sterren gegeven.  Token verplicht! ", 
				), 
				"search" => array(
					"format" => "string", 
					"info" => "", 
				), 
				"id" => array(
					"format" => "integer", 
					"info" => "toont enkel het bericht met opgegeven ID", 
				), 
			),
		); 
		output (array("info" => $arInfo), $strFormat); 
	}
	
	
	$oOwaesList = new owaeslist();  
	if (isset($_GET["type"])) {
		switch(strtolower($_GET["type"])) {
			case "market": 
			case "work":
				$oOwaesList->filterByType($_GET["type"]); 
				break; 
			default:  
				output (array("error" => "type: invalid value"), $strFormat); 
		}
		
	}
	
	if (isset($_GET["id"])) { 
		$oOwaesList->filterByID(intval($_GET["id"])); 	
	}
	
	if (isset($_GET["status"])) {
		switch(strtolower($_GET["status"])) {
			case "open": 
				$oOwaesList->filterByState(array(STATE_RECRUTE, STATE_SELECTED)); 	
				break; 
			case "recrute": 
			case STATE_RECRUTE: 
				$oOwaesList->filterByState(STATE_RECRUTE); 	
				break; 
			case "selected": 
			case STATE_SELECTED: 
				$oOwaesList->filterByState(STATE_SELECTED); 	
				break; 
			case "finished": 
			case STATE_FINISHED: 
				$oOwaesList->filterByState(STATE_FINISHED); 	
				break;  
			default:  
				output (array("error" => "status: invalid value"), $strFormat); 
		}  
	}
						
	if (isset($_GET["subscribed"])) {
		switch(strtolower($_GET["subscribed"])) {
			case "yes":  
			case "true": 
			case 1: 
				$oOwaesList->subscribed(me(), "yes");
				break;  
			case "confirmed": 
				$oOwaesList->subscribed(me(), "confirmed");
				break;  
			case "notconfirmed": 
				$oOwaesList->subscribed(me(), "notconfirmed");
				break;  
			case "no": 
			case "false": 
			case 0: 
				$oOwaesList->subscribed(me(), "no");
				break;  
			default:  
				output (array("error" => "subscribed: invalid value"), $strFormat); 
		}  
	} 
						
	if (isset($_GET["payed"])) {
		switch(strtolower($_GET["payed"])) {
			case "yes":  
			case "true": 
			case 1: 
				$oOwaesList->payment(me(), "yes");
				break;  
			case "no": 
			case "false": 
			case 0: 
				$oOwaesList->payment(me(), "no");
				break;  
			default:  
				output (array("error" => "transaction: invalid value"), $strFormat); 
		}  
	} 	 
	
						
	if (isset($_GET["rating"])) {
		switch(strtolower($_GET["rating"])) {
			case "yes":  
			case "true": 
			case 1: 
				$oOwaesList->rated(me(), "yes");
				break;  
			case "no": 
			case "false": 
			case 0: 
				$oOwaesList->rated(me(), "no");
				break;  
			default:  
				output (array("error" => "rating: invalid value"), $strFormat); 
		}  
	} 	 
	
	if (isset($_GET["author"])) $oOwaesList->filterByUser(intval($_GET["author"])); 
	
	//$oOwaesList->filterByState(STATE_RECRUTE); 
	
	foreach ((isset($_POST["show"])?$_POST["show"]:array()) as $iCat) $oOwaesList->hasCategory($iCat); 
	foreach ((isset($_POST["hide"])?$_POST["hide"]:array()) as $iCat) $oOwaesList->notCategory($iCat); 
	foreach ((isset($_POST["waarden"])?$_POST["waarden"]:array()) as $strWaarde) $oOwaesList->hasWaarde($strWaarde); 

	$strTemplate = "templates/" . $strFile; 
	foreach ($oOwaesList->getList() as $oItem) {  
		$arItem = array(
			"id" => $oItem->id(), 
			"link" => $oItem->url(), 
			"author" => $oItem->author()->id(), 
			"group" => $oItem->group() ? $oItem->group()->id() : 0,  
			"title" => $oItem->title(), 
			"body" => $oItem->body(), 
			"location" => array(
				"text" => $oItem->location(),
				"latitude" => $oItem->latitude(),
				"longitude" => $oItem->longitude(),
			), 
			"data" => $oItem->data(), 
			"timing" => $oItem->timing(), 
			"indicators" => array(
				"mental" => $oItem->mental(),
				"emotional" => $oItem->emotional(), 
				"physical" => $oItem->physical(), 
				"social" => $oItem->social(),  
			), 
			"credits" => $oItem->credits(), 
		);  
		if ($strFile != "") $arItem["html"] = $oItem->html($strTemplate); 
		$arResult[] = $arItem; 
		// echo $oItem->HTML("templates/owaeskort.html"); 
	}

	output (array("info" => $arInfo, "result" => $arResult), $strFormat, $strFile); 
	
	function output($arOutput, $strFormat = "txt", $strFile = "") { 
		switch(strtolower($strFormat)) {
			case "json": 
				echo json_encode($arOutput); 
				break; 	 
				
			case "xml": 
				break; 
				
			case "txt": 
				echo (outputTXT($arOutput)); 
				break;  
				
			case "": 
			case "htm": 
			case "html": 
				echo (outputHTML($arOutput, $strFile));
				break; 
			 
			default: 
				echo ("ongeldig formaat ('$strFormat'), momenteel enkel 'json', 'xml' of 'html'"); 
		}	
		exit(); 
	}
	
	function outputTXT($arOutput, $iDepth = 0) {
		$strResult = ""; 
		foreach ($arOutput as $strKey=>$strValue) {
			$strResult .= str_repeat(" ",$iDepth);
			if (is_array($strValue)) {
				$strResult .= $strKey . ": \n" . outputTxt($strValue, $iDepth+1); 
			} else {
				$strResult .= $strKey . ": " . $strValue . "\n"; 
			}   
		}
		return $strResult; 
	}
	function outputHTML($arOutput, $strFile) {
		if (isset($arOutput["result"]) && ($strFile != "")) {
			$strTemplate = "template/" . $strFile; 
			$strResult = ""; 
			foreach ($arOutput["result"] as $arItem) {
				$strResult .= $arItem["html"]; 
			}
		} else {
			$strResult = "<dl>"; 
			foreach ($arOutput as $strKey=>$strValue) {
				$strResult .= "<dt>" . $strKey . "</dt>"; 
				$strResult .= "<dd>"; 
				if (is_array($strValue)) {
					$strResult .= outputHTML($strValue, $strFile); 
				} else {
					$strResult .= $strValue; 
				}  
				$strResult .= "</dd>"; 
			}
			$strResult .= "</dl>"; 
		}
		return $strResult; 
	}
?>