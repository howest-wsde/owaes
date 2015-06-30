<?php
	include "inc.default.php"; // should be included in EVERY file 

	$oSecurity = new security(FALSE); 
	$oMe = user(me()); 
	$strMelding = ""; 
	
	$strModal = template("modal.voorwaarden.html");
	if (!$oMe->algemenevoorwaarden()) $strMelding = "<p>Uw inschrijving werd doorgestuurd naar een OWAES-medewerker. U kan deelnemen aan het platform van zodra deze uw inschrijving goedgekeurd heeft. </p>"; 
	
	$strModal->tag("melding", $strMelding); 
	
	echo $strModal->html(); 
  
?>
