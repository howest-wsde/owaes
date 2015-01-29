<?
	include "inc.default.php"; // should be included in EVERY file 

	$oSecurity = new security(TRUE); 
	$oMe = user(me()); 
	$strMelding = ""; 
	
	$strModal = template("modal.voorwaarden.html");
	if (!$oMe->algemenevoorwaarden()) $strMelding = "<p>U kan pas deelnemen aan het platform vanaf een OWAES-medewerker een ondertekend exemplaar van de algemene voorwaarden ontvangen heeft. </p>"; 
	
	$strModal->tag("melding", $strMelding); 
	
	echo $strModal->html(); 
  
?>