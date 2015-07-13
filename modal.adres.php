<?php
	include "inc.default.php"; // should be included in EVERY file 
	
	$oSecurity = new security(TRUE); 
	$oMe = user(me()); 
 
	if (isset($_POST["location"])) { 
 		$strVal = $_POST["location"]; 

		if (trim($strVal) != "") {
			$arLoc = getXY($strVal);  
		} else {
			$arLoc = array("latitude"=>0, "longitude"=>0); 
		}
		$oMe->location($strVal, $arLoc["latitude"], $arLoc["longitude"]); 
		$oMe->visible("location", $_POST["showlocation"]);
		$oMe->update();  

		exit(); 
	}  
	$oHTML = template("modal.adres.html"); 
	$oHTML->tag("redirect", $_GET["r"]); 
	echo ($oHTML->html());  
?>