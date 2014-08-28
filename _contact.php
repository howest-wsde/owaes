<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	 
	$strKey = isset($_GET["alias"]) 
		? $_GET["alias"]
		: ( isset($_GET["id"]) ? intval($_GET["id"]) : me() ); 
	$oProfile = new user($strKey); 

	$oConversation = new conversation($oProfile->id()); 
	if (isset($_POST["mail"])) {
		$oConversation->add($_POST["message"], $_POST["subject"] ); 
	}
	
	if (isset($_POST["ajax"])) {
		echo ("<form action=\"" . $oPage->filename() . "\" method=\"post\">"); 
		echo ("<input type=\"hidden\" name=\"ajax\" value=\"1\" />"); 
		echo ("<dl>"); 
		echo ("<dd><textarea name=\"message\" id=\"message\"></textarea></dd>"); 
		echo ("<dd><input type=\"submit\" value=\"verzenden\" name=\"mail\" /></dd>"); 
		echo ("</dl>"); 
		echo ("</form>"); 
		exit(); 	
	}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="profile">
    	<div class="header">
        
        </div>
    	<div class="body">
        	<? echo $oPage->startTabs(); ?>
                <div class="sideleft">
                	<? echo $oSecurity->me()->html("templates/leftuserprofile.html"); ?>
                </div>
                <div class="sidecenterright"> 
                	<?
						foreach($oConversation->messages() as $oMessage) {
							$oFrom = new user($oMessage->sender()); 
							echo "<h3>" . $oMessage->subject() . "</h3>"; 	 
							echo "<small>from " . $oFrom->getName() . "</small>"; 
							echo "<p>" . $oMessage->body() . "</p>"; 	
						}
					?>
					<form method="post">
                    	<dl>
                        	<dt>Naar: </dt>
                            <dd><? 
                            	echo $oProfile->getName(); 
                            ?></dd>
                        	<dt>Titel: </dt>
                            <dd><input type="text" name="subject" id="subject" /></dd>
                            <dt>Bericht: </dt>
                            <dd><textarea name="message" id="message"></textarea></dd>
                            
                            <dd><input type="submit" value="verzenden" name="mail" /></dd>
						</dl>
                    </form>
                </div>
                <div class="sideright">
               		<? 
						if ($oSecurity->getUserID() != $oProfile->id()) echo $oProfile->html("templates/leftuserprofile.html"); 
					?>
                    <div class="search box">
                        <form method="get">
                            <input type="text" name="search" id="search" />
                            <input type="submit" value="zoeken" />
                        </form>
                    </div>
                </div>
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
