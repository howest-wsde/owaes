<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	// freeze.php?u=69&a=0
	
	if (user(me())->admin()) {
		$oUser = user($_GET["u"]);  
		$oUser->actief(($_GET["a"]==1)); 
	//	vardump($oUser); 
	//	exit(); 
		$oUser->update(); 
	}
	  
	if (isset($_GET["ajax"])) { 
		echo $oUser->HTML("userfromlist.html");  
	} else {  
		redirect();
	}
?>