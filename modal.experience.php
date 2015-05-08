<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	
	if (isset($_POST["add"])) {
		$oExperience = new experience(me());  
		$oExperience->confirm();   
		exit(); 	
	}
	
	$oLayout = new template("modal.experience.html");

	$oMe = user(me()); 
	$iExp1 = $oMe->experience()->total(); 
	$iExp2 = $oMe->experience()->total(TRUE); 
	$iDiff = $iExp2 - $iExp1; 
	
	$oLayout->tag("newexp", $iDiff);  
	 
	echo $oLayout->html(); 
	
?>