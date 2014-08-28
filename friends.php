<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
 
	$oUserList = new userlist();   
	$oUserList->filter("visible"); 
	$oUserList->filter("friends"); 
    
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
                	<? 
	                    echo $oSecurity->me()->html("templates/user.html");
                    ?>
                </div>
                <div class="usersfromlist row sidecenterright">
             
                    <? 
                        foreach ($oUserList->getList() as $oUser) { 
                            echo $oUser->HTML("templates/userfromlist.html");  
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
