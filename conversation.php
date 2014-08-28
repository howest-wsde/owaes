<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
    $arUsers =  array();
    
    $oInbox = new inbox();
    $arDiscussions = $oInbox->discussions();  // overzicht van users waarmee conversations gestart zijn
	
	// vardump($arDiscussions); 
	    
	if (isset($_GET["users"])){
		$arUsers = explode(",", $_GET["users"]);
	} else {
		$arUsers = (count($arDiscussions)>0) ? explode(",", $arDiscussions[0]["ids"]) : array(); 	
	}

	$oConversation = new conversation($arUsers);  

	$oNotification = new notification(); 
	$oNotification->read("conversation." . implode(",", $oConversation->users()));  

	if (isset($_POST["addmessage"])) { 
		$oMessage = $oConversation->add($_POST["message"], intval($_POST["market"]));   
	}
	 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
            global $oPage;
            echo $oPage->getHeader(); 
        ?>
    </head>
    <body id="conversation">
        <? echo $oPage->startTabs(); ?> 
    <div class="body content content-lists-users container">
    
                <div class="row">
                    <? /*echo $oSecurity->me()->html("templates/leftuserprofile.html"); */
                    global $oSecurity;
                    echo $oSecurity->me()->html("templates/user.html");
                    ?>
                </div>

                 <div class="row conversationPage">
                
                        <div class="col-md-4 convoCollection">
                            <div class="list-group">
                                <? 
                                    foreach ($arDiscussions as $iKey=>$arDiscussion) {

                                       
                                        if (join(",", $arUsers) == $arDiscussion["ids"]){
                                             echo('<a href="conversation.php?users='.$arDiscussion["ids"].'" class="list-group-item convo conversationActive">');
                                        } else {
                                            echo('<a href="conversation.php?users='.$arDiscussion["ids"].'" class="list-group-item convo">');
                                        }
                               
                                            echo('<div class="media">');
                                                $ids = explode(",",$arDiscussion["ids"]);
                                                if( $ids[0] == me()){
                                                    echo(' <img class="media-object pull-left" src="profileimg.php?id='.$ids[1].'&w=64&h=64&v=464">');
                                                }else{
                                                    echo(' <img class="media-object pull-left" src="profileimg.php?id='.$ids[0].'&w=64&h=64&v=464">');
                                                }
                                                echo('<div class="media-body">');
                                                    echo('<h4 class="media-heading">'.$arDiscussion["names"].'</h4><span class="convDate">' . str_date($arDiscussion["lastpost"]) . '</span>');
                                                echo('</div>');
                                            echo('</div>');
                                         echo('</a>');
                                         echo('<hr class="hrConvoCollection">');
                                           
                                    }
                            
                            
                                ?>
                       
                             </div>
                    
                        </div><!-- end convoCollection -->
                        <div class="col-md-8 convoColumn">
                                <div class="sidecenter conversation">  
        
          
           
                            <?  
                         
                                foreach ($oConversation->messages()as $oMessage) {  // meest recente discussie onderaan
                                    if (($oMessage->sender()->id() != me()) && (!$oMessage->read())) $oMessage->doRead(); 
                                    if (!isset($arGroepen[$oMessage->market()])) $arGroepen[$oMessage->market()] = array(); 
                                    array_push ($arGroepen[$oMessage->market()], $oMessage); 
                                }
                                ;
                                if (!isset($arGroepen[0])) $arGroepen[0] = array(); 
                
                                $oMe = user(me());  
                        
                                foreach($arGroepen as $iKey=>$arGroep) {
                        
                                    switch($iKey) {
                                        case 0: 
                                            $arSTRusers = array(); 
                                            foreach ($oConversation->users() as $iUser) $arSTRusers[] = user($iUser)->getname(); 
                                            echo ("<h1>" . implode(" - ", $arSTRusers) . "</h1>");  
                                            break; 
                                        default: 
                                            $oMarket = new owaesitem($iKey) ;
                                            echo("</div><div class='conversation'>");
                                            echo ("<h1><a href=\"" . $oMarket->getLink() . "\">" . $oMarket->title() . "</a></h1>"); 	
                                    }
                                    $oPrevUser = NULL; 
                                    $iCounter = 0; 
                    
                                    echo('<div class="bericht">');
                                    foreach ($arGroep as $oMessage) { 
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
                                            //if (($oMessage->market() != 0) && (++$iCounter==1)) {
                                            //    $oMarket = new owaesitem($oMessage->market()) ;
                                            //    echo ('<div class="market"><a href="' . $oMarket->getLink() . '">' . $oMarket->title() . '</a>hello</div>'); 
                                            //}
                                            echo ('<div class="spacer"></div>');
                                        echo ('</div>'); 
                        
                       
                                        $oPrevUser = $oMessage->sender();
                                    }
                   
                                    ?>
                                    <div class="message">
                                        <? if ($oPrevUser != $oMe) { ?>
                                            <div class="user">
                                                <div class="img"><? echo $oMe->getImage("60x60", TRUE); ?></div>
                                                <div class="name"><a href="<? echo $oMe->getURL(); ?>"><? echo $oMe->getName(); ?></a></div>
                                            </div>
                                        <? } ?>
                                        <form method="post">
                                            <input type="hidden" name="market" value="<? echo $iKey; ?>" />
                                            <textarea name="message"></textarea>
                                            <input class="btn btn-default btn-sm pull-right" type="submit" name="addmessage" value="Verzenden" />
                                        </form>
                                    </div>
                                    </div>
                                    <?
                                }
                
                  
                            ?> 

                        </div>
                      </div> <!-- einde col-md-8 -->
                   </div> <!-- einde row --> 
        
            <? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
            <? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
