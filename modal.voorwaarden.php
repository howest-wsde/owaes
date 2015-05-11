<?php
	include "inc.default.php"; // should be included in EVERY file 

	$oSecurity = new security(FALSE); 
	$oMe = user(me()); 
	$strMelding = ""; 
	
	$strModal = template("modal.voorwaarden.html");
	if (!$oMe->algemenevoorwaarden()) $strMelding = "<p>U kan pas deelnemen aan het platform vanaf een OWAES-medewerker een ondertekend exemplaar van de gebruikersvoorwaarden ontvangen heeft. </p>"; 
	
	$strModal->tag("melding", $strMelding); 
	
	echo $strModal->html(); 
  
?>