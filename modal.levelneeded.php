<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security();  
	
	$iLevel = isset($_GET["l"]) ? $_GET["l"] : "Fout!";  
	  
	$oLayout = new template("modal.levelneeded.html");
	$oLayout->tag("level", $iLevel);   
	 
	echo $oLayout->html(); 
	
?>