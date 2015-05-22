<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
   
   // $oPage->tab("home");
	
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
					<?php
                    	switch(isset($_GET["e"]) ? $_GET["e"] : "") {
							case "algemenevoorwaarden": 
								echo ("<p>Deze actie is nog niet mogelijk zonder de gebruikersvoorwaarden te tekenen.</p>"); 
								break; 	
							case "admin": 
								echo ("<p>Deze pagina is enkel toegankelijk voor administrators.</p>"); 
								break; 	
							case "group": 
								echo ("<p>Deze pagina is enkel toegankelijk voor groepsbeheerders.</p>"); 
								break; 	
							case "rechten": 
								echo ("<p>U heeft niet de nodige rechten deze actie uit te voeren. </p>"); 
								break; 	
							case "level": 
								echo ("<p>Om deze pagina te openen moet u eerst een hoger level behalen. </p>"); 
								break; 	
							default: 
								echo ("<p>U heeft niet de nodige rechten deze actie uit te voeren. </p>"); 
						}
					
					?>
                </div>
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
