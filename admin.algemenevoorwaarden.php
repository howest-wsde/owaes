<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);   
	
	if (user(me())->admin()) {
		$oUser = user($_GET["u"]);  
		$oUser->algemenevoorwaarden(1); 
		$oUser->update(); 
	}
	  
	if (isset($_GET["ajax"])) { 
		echo $oUser->HTML("userfromlist.html");  
	} else {  
		redirect();
	}
?>