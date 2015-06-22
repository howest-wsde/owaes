<?php
	include "inc.default.php"; // should be included in EVERY file 

	$oSecurity = new security(FALSE); 
	
	$strKey = $_GET["k"]; 
	$iUser = intval($_GET["u"]); 
	
	$oUser = user($iUser); 
	$oUser->validateEmail($strKey); 
	
	redirect("index.php"); 