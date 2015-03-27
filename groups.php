<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	if (!user(me())->levelrights("groepslijst")) stop("level");
 
	$oList = new grouplist(); 
    
	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload");     
	$oExperience->add(1);  

    $oPage->tab("lijsten");
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="users">
        <? echo $oPage->startTabs(); ?> 
    	<div class="body content content-lists-users container">
        	
            	<div class="row">
					<? /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                <div class="usersfromlist row sidecenterright">
                
                    <!--<div class="main market"> -->

                        <? 
                            foreach ($oList->getList() as $oUser) { 
                                echo "<div id=\"group-" . $oUser->id() . "\">" . $oUser->HTML("groupfromlist.html") . "</div>";   
                            }
                        ?>
                    <!--</div>-->
                </div>
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
