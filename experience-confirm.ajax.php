<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  

	$oExperience = new experience(me());  
	$oExperience->confirm();  
	
	echo "Experience: " . $oExperience->total(FALSE) . "/" . $oExperience->total(TRUE); 
	 
?> 