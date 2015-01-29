<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);   
	
	$strMail = $_POST["m"]; 
  
	echo (user(me())->email($strMail)) ? "yes" : "no"; 
	// GEEN SAVE! 
?>