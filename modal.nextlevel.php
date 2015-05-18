<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	  
	$oLayout = new template("modal.nextlevel.html");

	$oMe = user(me()); 
	$iLevel = $oMe->experience()->level(TRUE); 
	
	$oLayout->tag("level", $iLevel);  
	
	echo $oLayout->html(); 
	
?>