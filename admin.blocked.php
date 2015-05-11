<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
 	
	if (!$oSecurity->admin()) stop("admin"); 
 
	$oUserList = new userlist();   
	$oUserList->filter("frozen"); 
    
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
    	<div class="body content content-lists-users container admin">
        	
            	<div class="row">
					<?php /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                <div class="usersfromlist row sidecenterright">
                 
                        <?php include "admin.menu.xml"; ?>
                    <!--<div class="main market"> -->

                        <?php 
                            foreach ($oUserList->getList() as $oUser) { 
                                echo "<div id=\"user-" . $oUser->id() . "\">" . $oUser->HTML("userfromlist.html") . "</div>";   
                            }
                        ?>
                    <!--</div>-->
                </div>
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
