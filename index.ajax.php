<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
 
	$oOwaesList = new owaeslist();  
	$oOwaesList->filterByType(isset($_POST["t"]) ? $_POST["t"] : ""); 
	$oOwaesList->filterByState(STATE_RECRUTE); 
	
	foreach ((isset($_POST["show"])?$_POST["show"]:array()) as $iCat) $oOwaesList->hasCategory($iCat); 
	foreach ((isset($_POST["hide"])?$_POST["hide"]:array()) as $iCat) $oOwaesList->notCategory($iCat); 
	foreach ((isset($_POST["waarden"])?$_POST["waarden"]:array()) as $strWaarde) $oOwaesList->hasWaarde($strWaarde); 

	$oOwaesList->filterPassedDate(owaesTime()); 

	foreach ($oOwaesList->getList() as $oItem) { 
		echo $oItem->HTML("owaeskort.html"); 
	}
 
?>