<?php
	include_once ("inc.functions.php");  
	
	$arLoc = getXY($_REQUEST["search"]); 
	if (isset($arLoc["latitude"])) echo $arLoc["longitude"] . "|" . $arLoc["latitude"]; 
?>