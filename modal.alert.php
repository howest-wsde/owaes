<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security();  
	
	$strAlert = isset($_GET["a"]) ? $_GET["a"] : "Fout!"; 
	$strTitel = isset($_GET["t"]) ? $_GET["t"] : "Fout"; 
	  
	$oLayout = new template("modal.alert.html");
	$oLayout->tag("alert", $strAlert);  
	$oLayout->tag("title", $strTitel);  
	 
	echo $oLayout->html(); 
	
?>