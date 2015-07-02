<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(FALSE);  

	$oUsers = new userlist(); 
	$arBullets = array(); 
	foreach ($oUsers->getList() as $oUser) {
		$arLL = $oUser->LatLong(); 
		if ($arLL[0] * $arLL[1] != 0) $arBullets[] = $arLL; 
	}
	echo json_encode($arBullets); 
?>