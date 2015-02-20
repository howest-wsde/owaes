<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
    $arUsers =  array();
    
    $oInbox = new inbox();
    $arDiscussions = $oInbox->discussions();  // overzicht van users waarmee conversations gestart zijn 
	//if (!isset($arDiscussions[0])) { 
	//	$arDiscussions[0] = array("lastpost" => 0, "name"=>"Owaes sitebeheerders");  
	//}
	
	$iUser = isset($_GET["u"]) ? intval($_GET["u"]) : 0; 

	$oConversation = new conversation($iUser);  

	$oNotification = new notification(); 
	$oNotification->read("conversation." . $iUser);  

	if (isset($_POST["addmessage"])) { 
		$oMessage = $oConversation->add($_POST["message"], intval($_POST["market"]));   
		user(me())->addbadge("heyhallo"); 
	}
	 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
            global $oPage;
            echo $oPage->getHeader(); 
        ?>
        <script>
			$(document).ready(function(e) {
                $("form.reactieform textarea").focus(function(){
					console.log($(this).parentsUntil("form").parent()); 
					$(this).parentsUntil("form").parent().addClass("focus"); 
					$(this).parent("form").addClass("focus"); 	
				})
            });
		</script>
    </head>
    <body id="conversation">
        <? echo $oPage->startTabs(); ?> 
    <div class="body content content-lists-users container">
    
                <div class="row">
                    <? /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    global $oSecurity;
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>

                 <div class="row conversationPage">
                
                        <div class="col-md-4 convoCollection">
                            <div class="list-group">
                                <? 
                                    foreach ($arDiscussions as $iDiscussUser=>$arDiscussion) {

                                       
                                        if ($iDiscussUser == $iUser){
                                             echo('<a href="conversation.php?u='.$iDiscussUser.'" class="list-group-item convo conversationActive">');
                                        } else {
                                            echo('<a href="conversation.php?u='.$iDiscussUser.'" class="list-group-item convo">');
                                        }
							   
											echo('<div class="media">');
												echo(' <img class="media-object pull-left" src="profileimg.php?id='.$iDiscussUser.'&w=64&h=64&v=464">');
											   
												echo('<div class="media-body">');
													echo('<h4 class="media-heading">'.$arDiscussion["name"].'</h4><span class="convDate">' . str_date($arDiscussion["lastpost"]) . '</span>');
												echo('</div>');
											echo('</div>');
                                         echo('</a>');
                                         echo('<hr class="hrConvoCollection">');
                                           
                                    }
                            
                            
                                ?>
                       
                             </div>
                    
                        </div><!-- end convoCollection -->
                        <div class="col-md-8 convoColumn">  
                            <?   
                                foreach ($oConversation->messages()as $oMessage) {
									$arSub = isset($arSubjects[$oMessage->market()]) ? $arSubjects[$oMessage->market()] : array();
									if (isset($arSubjects[$oMessage->market()])) unset ($arSubjects[$oMessage->market()]); 
									$arSub[] = $oMessage; 
									$arSubjects[$oMessage->market()] = $arSub;   // arSubjects-item wordt opnieuw gemaakt om discussie naar onder te verplaatsen 
                                }
								
                                if (!isset($arSubjects[0])) $arSubjects[0] = array();  // subject 0: gewone discussie, los van owaesitem
                
                                $oMe = user(me());   
                        
                                foreach($arSubjects as $iMarket=>$arGroep) { 
									$oPrevUser = NULL; 
									$iPrevDate = 0; 
									echo "<div class=\"conversation-subject\">"; 
                        
                                    switch($iMarket) {
                                        case 0: 
                                            $arUsers = array(); 
                                          //  foreach ($oConversation->users() as $iUser) $arUsers[] = user($iUser)->getname(); 
                                            echo ("<h2>" . user($iUser)->getName() . "</h2>");  
                                            break; 
                                        default: 
                                            $oMarket = owaesitem($iMarket) ; 
                                            echo ("<h2><a href=\"" . $oMarket->getLink() . "\">" . $oMarket->title() . "</a></h2>"); 	
                                    } 
                                    $iCounter = 0; 
                    
                                   foreach ($arGroep as $oMessage) { 
                                        if ($oMessage->receiver() == me()) $oMessage->doRead(); 
										
										
										
//										vardump($oMessage); 
										
                        
                                        if ($oMessage->sender() != $oPrevUser) {
											   
												echo ('<div class="clear"></div>
													<div class="user">
                                                        <div class="img">' . $oMessage->sender()->getImage("90x90", TRUE) . '</div>
                                                        <div class="name"><a href="' . $oMessage->sender()->getURL() . '">' . $oMessage->sender()->getName() . '</a></div>
                                                    </div>');
										}
										 
                                        echo ('<div class="message">');  
											if ($iPrevDate < $oMessage->sent()-(60*5)) {
	                                            echo ('<div class="date">' . str_date($oMessage->sent()) . '</div>'); 
											}
											$iPrevDate = $oMessage->sent(); 
											echo ('<div class="msg">' . html($oMessage->body()) . '</div>'); 
											
											$arExtras = array(); 
											foreach ($oMessage->data() as $strK => $strV) {
												switch($strK) {
													case "market":  
														if (intval($strV)>0) $arExtras[] = "<dt>Aanbod:</dt><dd>" . owaesitem($strV)->link() . "</dd>"; 
														break; 
													case "user": 
														if (intval($strV)>0) $arExtras[] = "<dt>Gebruiker:</dt><dd>" . user($strV)->getLink() . "</dd>"; 
														break;  
													case "reporter": 
														if (intval($strV)>0) $arExtras[] = "<dt>Reporter:</dt><dd>" . user($strV)->getLink() . "</dd>"; 
														break;  
													case "info": 
														if (trim($strV) != "") $arExtras[] = "<dt>Informatie:</dt><dd>" . $strV . "</dd>"; 
														break;  
													case "report": 
														$arReport = explode(".", $strV); 
														$arExtras[] = "<dt>Rapporteer:</dt><dd><a href=\"modal.report.php?u=" . $arReport[1] . "&m=" . $arReport[2] . "&reason=" . $arReport[0] . "\" class=\"domodal\">Dit is niet overeengekomen</a></dd>"; 
														break;  
												} 
											} 
											if (count($arExtras)>0) echo "<dl class=\"well\">" . implode("", $arExtras) . "</dl>"; 
                                            //if (($oMessage->market() != 0) && (++$iCounter==1)) {
                                            //    $oMarket = new owaesitem($oMessage->market()) ;
                                            //    echo ('<div class="market"><a href="' . $oMarket->getLink() . '">' . $oMarket->title() . '</a>hello</div>'); 
                                            //}
                                           // echo ('<div class="spacer"></div>');
                                        echo ('</div>'); 
                         
                                        $oPrevUser = $oMessage->sender();
                                    }
									
                   
                                    ?>
										<? if ($oPrevUser != user(me())) { ?>
                                        	<div class="clear"></div>
                                            <div class="user">
                                                <div class="img"><? echo $oMe->getImage("60x60", TRUE); ?></div>
                                                <div class="name"><a href="<? echo $oMe->getURL(); ?>"><? echo $oMe->getName(); ?></a></div>
                                            </div>
                                        <? } ?>
                                        <div class="message">
                                            <form method="post" class="reactieform">
                                                <input type="hidden" name="market" value="<? echo $iMarket; ?>" />
                                                <textarea name="message" placeholder="Plaats een reactie"></textarea>
                                                <input class="btn btn-default btn-sm pull-right" type="submit" name="addmessage" value="Verzenden" />
                                            </form>
                                        </div> 
                                    <?
									
									echo "</div>";  // newsubject
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
