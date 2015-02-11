<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$oSecurity = new security(TRUE); 
	$oMe = user(me()); 
 
	if (isset($_POST["old"]) && isset($_POST["new"])) { 
		if (md5($_POST["old"]) == $oMe->password()) {  
			$oMe->password($_POST["new"]); 
			$oMe->update();  
			echo "ok"; 
		} else echo "Paswoord niet correct"; 
		exit(); 
	}  
	
	echo (template("modal.changepass.html"));  
?>