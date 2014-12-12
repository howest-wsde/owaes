<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	
	$oUserList = new userlist();    
	$oUserList->search($_POST["f"]); 
	
	$arResult = array(); 
	foreach ($oUserList->getList() as $oUser)  {
		$arResult[] = "" . $oUser->html("admin.useradd.html") . ""; 
		
//		$arResult[] = "<li><button value=\"" . $oUser->id() . "\" name=\"adduser\">" . $oUser->getName() . "</button></li>";  
	}
    
	echo implode("", $arResult); 
	if (count($arResult) == 0) echo ("<p>Geen personen gevonden met deze zoekterm</p>");
	
?>