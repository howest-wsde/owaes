<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
   
	$iID = intval($_GET["owaes"]); 
	$oOwaesItem = new owaesitem($iID);  
	
	if ($oOwaesItem->author()->id() == $oSecurity->me()->id()) {
		redirect("owaes-selecteer.php?owaes=" . $iID); 
		exit(); 
	}
	
	$strType = $oOwaesItem->type()->key(); 
	$oPage->tab("market.$strType");  
	
	if (isset($_POST["addmessage"])) {
		$oConversation = new conversation($oOwaesItem->author()->id()); 
		$oConversation->add($_POST["message"], $oOwaesItem->id() ); 	
	}
	
	/* -- SET $iStatus TO TYPE OF USER (CREATOR TASK, EXECUTOR, SUBSCRIBED OR JUST GUEST -- */
		define ("JOB_SUBSCRIBED", 4); 
		define ("JOB_EXECUTOR", 2); 
		define ("JOB_VIEWER", 3);
		define ("JOB_CREATOR", 1);
		$iStatus = JOB_VIEWER; 
		if ($oSecurity->me()->id() == $oOwaesItem->author()->id())  {
			$iStatus = JOB_CREATOR; 
		} else { 
			foreach ($oOwaesItem->subscriptions() as $iUser=>$oSubscription) {
				if ($iUser == me()) $iStatus = ($oSubscription->state() == SUBSCRIBE_CONFIRMED)?JOB_EXECUTOR:JOB_SUBSCRIBED; 
			}
		}
	/* -- stop -- */
	   
	$oRightcolumnUser = NULL; // als er maar 1 uitvoerder of ingeschrevene is zal deze getoond worden aan de rechterkant 
 
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="owaes">               
            <? echo $oPage->startTabs(); ?> 
    	<div class="container body content content-owaes">
        	
        <div class="row">
					<? /*echo $oSecurity->me()->html("templates/leftuserprofile.html"); */
                    echo $oSecurity->me()->html("templates/user.html");
                    ?>
                </div>
        
        
      
            
                <div class="sidecenter"> 
                
                
                
                
					<? echo $oOwaesItem->HTML("templates/owaesdetail.html");  ?>
				 	
                    <div class="messages">
                             <? 
                   		        $oConversation = new conversation(array($oOwaesItem->author()->id(), me()));  
						        $oConversation->filter("owaes", $iID); 
						        $oPrevUser = NULL; 
                                 echo('<div class="bericht">');
						        foreach ($oConversation->messages() as $oMessage) {
							        if ($oMessage->sender()->id() != me()) $oMessage->doRead(); 
                                   
                                    
                                    if ($oPrevUser!=null & $oMessage->sender() != $oPrevUser) {
                                            echo('</div>');
                                            echo('<div class="bericht">');
                                    }
                                    
							        echo ('<div class="message">'); 
								        if ($oMessage->sender() != $oPrevUser) {
									        echo ('<div class="user">
											        <div class="img">' . $oMessage->sender()->getImage("90x90", TRUE) . '</div>
											        <div class="name"><a href="' . $oMessage->sender()->getURL() . '">' . $oMessage->sender()->getName() . '</a></div>
										        </div>');
								        }
								        echo ('<div class="date">' . str_date($oMessage->sent()) . '</div>
									        <div class="msg">' . $oMessage->body() . '</div>'); 
								        echo ('<div class="spacer"></div>'); 
							        echo ('</div>'); 
							        $oPrevUser = $oMessage->sender();
						        }
						
						        $oMe = user(me());  
                            ?>
                            <hr/>
                            <div class="message">
                    	        <?/* if ($oPrevUser != $oMe) { ?>
                                    <div class="user">
                                        <div class="img"><? echo $oMe->getImage("90x90", TRUE); ?></div>
                                        <div class="name"><a href="<? echo $oMe->getURL(); ?>"><? echo $oMe->getName(); ?></a></div>
                                    </div>
                                 <? } */?>
                                  <div class="user">
                                        <div class="img"><? echo $oMe->getImage("90x90", TRUE); ?></div>
                              <!--           <div class="name"><a href="<? /* echo $oMe->getURL(); ?>"><? echo $oMe->getName(); */?></a></div> -->
                                    </div>
                                <form method="post">
                                    <textarea name="message" placeholder="Type hier uw bericht..."></textarea>
                                    <input class="btn btn-default pull-right" type="submit" name="addmessage" value="Verzenden" />
                                </form>
                            </div> 
                    </div>
                   
            	</div>
                <!-- <div class="sideright">
                	<? 
						if ($iStatus != JOB_CREATOR) echo $oOwaesItem->author()->html("templates/leftuserprofile.html");
                        if (!is_null($oRightcolumnUser)) echo $oRightcolumnUser->html("templates/leftuserprofile.html"); 
					 ?>
                </div> -->
            <? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
