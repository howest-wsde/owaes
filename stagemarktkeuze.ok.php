<?php
	include "inc.default.php"; // should be included in EVERY file  
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	 
	 
	$oDB = new database();  
	$oDB->execute("select count(student) as aantal from tblStagemarktStudInschrijvingen where student = " . me() . ";"); 
	if ($oDB->get("aantal") == 0) redirect ("stagemarktkeuze.php"); 
  
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?> 
    </head>
    <body id="users">
        <?php echo $oPage->startTabs(); ?> 
    	<div class="body content content-lists-users container">
        	
            	<div class="row">
					<?php  
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                <div class="usersfromlist row sidecenterright"> 
                	<div class="info">
                    	<h2>Inschrijving Stagemarkt</h2> 
                    	<p>Uw keuze werd reeds ingegeven. </p>
                    </div> 
                </div>
        	<?php echo $oPage->endTabs(); ?>
        </div>
 
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
