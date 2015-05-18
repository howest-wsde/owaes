<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
 
 	$iUser = isset($_GET["u"]) ? intval($_GET["u"]) : 0; 
 
	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload");     
	$oExperience->add(1);  
	
	$oUserList = new userlist();   
	$oUserList->filter("visible"); 
	if ($iUser != 0) {
		$oUserList->filter("friends", $iUser); 
	} else {
		$oUserList->filter("friends"); 
	}
    
    $oPage->tab("lijsten");
	
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
                        foreach ($oUserList->getList() as $oUser) { 
                            echo $oUser->HTML("userfromlist.html");  
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
