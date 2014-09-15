<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	
	$iID = intval($_GET["id"]); 
	$oGroup = group($iID);  
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="profile">
    	<div class="header">
        	<a href="main.php"><img src="img/logo.png" /></a>
        </div>
    	<div class="body">
        	<? echo $oPage->startTabs(); ?>
                <div class="sideleft">
                	<? echo $oSecurity->me()->html("templates/leftuserprofile.html"); ?>
                </div>
                <div class="sidecenterright"> 
					<?  
						echo $oGroup->html("templates/groupshort.html"); 
						 
						foreach ($oGroup->users() as $oUser) {
							echo $oUser->html("templates/userfromlist.html"); 
						} 
                    ?>  
                </div> 
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
