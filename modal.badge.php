<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	  
	$oLayout = new template("modal.badge.html");

	$oMe = user(me());  
	
	//$oLayout->tag("level", $iLevel);  
	
	echo $oLayout->html(); 
	
?>