<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(FALSE); 
	$oLog = new log("logout"); 
	
	$oSecurity->doLogout(); 
	
	header("Location: main.php"); 
	
?>