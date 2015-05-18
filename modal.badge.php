<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	
	$oTag = new badge($_GET["m"]); 
	echo $oTag->html("modal.badge.html");

	$oMe = user(me());  
	
	//$oLayout->tag("level", $iLevel);  
	 
	
?>