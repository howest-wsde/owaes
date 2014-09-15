<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
   
	$iID = intval($_GET["owaes"]); 
	$oOwaesItem = owaesitem($iID);   
	
	$oNotification = new notification(); 
	$oNotification->read("subscription." . $iID );  
	
	if ($oOwaesItem->author()->id() != $oSecurity->me()->id()) {
		redirect("owaes.php?owaes=" . $iID); 
		exit(); 
	}
	
	$oNotification = new notification(); 
	$oNotification->read("owaes." . $iID); 
	
	if (isset($_POST["close"])) {
		$oOwaesItem->state(STATE_FINISHED); 
		$oOwaesItem->update(); 
	} else if (isset($_POST["delete"])) {
		$oOwaesItem->state(STATE_DELETED); 
		$oOwaesItem->update();  
		redirect(fixPath("main.php")); 
	}  else if (isset($_POST["edit"])) {
		redirect(fixPath("owaesadd.php?edit=" . $iID));  
	} else {
		
		if (isset($_POST["goedgekeurd"])) foreach ($_POST["goedgekeurd"] as $iUser){  
			$oOwaesItem->addSubscription($iUser, SUBSCRIBE_CONFIRMED); 
			$oConversation = new conversation($iUser); 
			$oConversation->add($_POST["mailgoedgekeurd"], $oOwaesItem->id() ); 

		}
		if (isset($_POST["afgekeurd"])) foreach ($_POST["afgekeurd"] as $iUser){  
			$oOwaesItem->addSubscription($iUser, SUBSCRIBE_DECLINED); 
			$oConversation = new conversation($iUser); 
			$oConversation->add($_POST["mailgeweigerd"], $oOwaesItem->id() ); 
		} 
	} 
	

	$strType = $oOwaesItem->type()->key(); 
	$oPage->tab("market.$strType");  
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?> 
        <script>
			$(document).ready(function() {
				$("div.bucket a.goedkeuren").click(function() {
					iUser = $(this).attr("rel"); 
					strID = "input" + iUser; 
					$("div#geselecteerd div.added").append($("div#user" + iUser)); 
					$("input[id=" + strID + "]").remove(); 
					$("form").append(
						$("<input />").attr("name", "goedgekeurd[]").attr("type", "hidden").attr("id", strID).addClass("unsaved").addClass("nieuwgoedgekeurd").val(iUser)
					);	
					sameHeight();
					return false; 
				})
				$("div.bucket a.afkeuren").click(function() {
					iUser = $(this).attr("rel"); 
					strID = "input" + iUser; 
					$("div#geweigerd div.added").append($("div#user" + iUser)); 
					$("input[id=" + strID + "]").remove(); 
					$("form").append( 
						$("<input />").attr("name", "afgekeurd[]").attr("type", "hidden").attr("id", strID).addClass("unsaved").addClass("nieuwafgekeurd").val(iUser)
					);	
					sameHeight();
					return false; 
				})
				$("form").submit(function(){
					arShow = Array(); 
					if ($(this).find(".nieuwgoedgekeurd").length > 0) arShow[arShow.length] = $("#mailgoedgekeurd"); 
					if ($(this).find(".nieuwafgekeurd").length > 0) arShow[arShow.length] = $("#mailafgewezen"); 
					if (arShow.length > 0) {
						if ($("#savestep1").hasClass("clicked")) {
							arShow[arShow.length] = "<input type=submit value='opslaan en verzenden' id='savestep2' class='btn btn-default btn-sm pull-right' />"; 
                            
							popwindow(arShow, $(this));  
                          //  if ($(this).find(".nieuwafgekeurd").length > 0) $('#modelAfgekeurd').modal('show');
                          //if ($(this).find(".nieuwgoedgekeurd").length > 0)  $('#modelGoed').modal('show');
                          
							
							return false; 
						} 
					}
				}); 
			}); 
		</script> 
    </head>
    <body id="owaes"> 
    	<div class="body">                
            <? echo $oPage->startTabs(); ?> 
            <div class="container content content-marktitem">
            	<div class="row">
					<? /*echo $oSecurity->me()->html("templates/leftuserprofile.html"); */
                    echo $oSecurity->me()->html("templates/user.html");
                    ?>
                </div>
                <!-- <div class="sidecenterright">  -->
                <div class="ownerDetail"> 
					<? echo $oOwaesItem->HTML("templates/owaesdetail.html");  ?> 
					
                    <? echo ("<form method=\"post\">"); ?>
                    
                    <div class="row">
                        <div class="col-md-6 nieuwInschrijvingen">
                            <? 
                            
                            echo ("<div class=\"bucket box sameheight col-md-4\" id=\"nieuw\">
											<h2>Nieuw</h2> 
										"); 
										foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
											switch ($oValue->state()) {
												case SUBSCRIBE_SUBSCRIBE: 
													$oUser = new user($iUser); 
													echo ("<div id='user" . $oUser->id() . "'>");  
                                                        echo ($oUser->html("templates/userid.html"));  
                                                        echo("<div class='toestemming'>");
														echo ("<span class='cursor'><a href='#goedkeuren' class='goedkeuren' rel='" . $oUser->id() . "'><span class='icon icon-check'></span><span>Goedkeuren</span></a></span>"); 
														echo ("<span class='cursor'><a href='#afkeuren' class='afkeuren' rel='" . $oUser->id() . "'><span class='icon icon-close'></span><span>Afkeuren</span></a></span>");
                                                        echo("</div>");
													echo ("</div>"); 
													break; 
												case SUBSCRIBE_NEGOTIATE: 
													$oUser = new user($iUser);
													echo ("<div id='user" . $oUser->id() . "'>");
                                                        echo ($oUser->html("templates/userid.html"));  
														echo ("<span class='cursor'><a href='#goedkeuren' class='goedkeuren' rel='" . $oUser->id() . "'><span class='icon icon-check'>Goedkeuren</span></a></span>");
														echo ("<span class='cursor'><a href='#afkeuren' class='afkeuren' rel='" . $oUser->id() . "'><span class='icon icon-close'>Afkeuren</span></a></span>"); 
													echo ("</div>"); 
													break;  
											} 
										} 
										echo ("</div>"); 
                            ?>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row geweigerd">
                                <?
                                echo ("<div class=\"bucket box sameheight col-md-4\" id=\"geweigerd\">
											<h2>Geweigerd</h2> 
										"); 
										foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
											switch ($oValue->state()) {
												case SUBSCRIBE_DECLINED: 
													$oUser = new user($iUser);
													echo ("<div id='user" . $oUser->id() . "'>");  
													echo ($oUser->html("templates/userid.html")); 
													echo ("</div>"); 
													break;  
											}
										} 
										echo ("<div class=\"added\"></div>"); 
										echo ("</div>"); 
                                //echo ("</div>");  
                                ?>
                            </div>
                            
                            <div class="row geselecteerd">
                                <?
                                	    $iConfirmed = 0; 
						                if (count($oOwaesItem->subscriptions()) > 0) { 
								        echo ("<div class=\"buckets\">"); 
									    echo ("<div class=\"bucket box sameheight col-md-4\" id=\"geselecteerd\">
											    <h2>Geselecteerd</h2> 
										    ");  
										foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
											switch ($oValue->state()) {
												case SUBSCRIBE_CONFIRMED: 
													$iConfirmed ++; 
													$oUser = new user($iUser);
													echo ("<div id='user" . $oUser->id() . "'>");  
													echo ($oUser->html("templates/userid.html")); 
													$oTransaction = $oValue->payment(); 
													//$oTransaction = $oOwaesItem->transactions($iUser); 
													$strIMG = fixPath("img/handshake" . ($oTransaction->signed()?1:0) . ".png");
													if ($oTransaction->signed()) {
														echo ("<img src=\"" . $strIMG . "\" alt=\"start transactie\" align=\"right\" />"); 
													} else {
														echo ("<a href=\"owaes-transactie.ajax.php?owaes=" . $iID . "&user=" . $iUser . "\" class=\"transactie\"><img src=\"" . $strIMG . "\" alt=\"start transactie\" align=\"right\" /></a>"); 
													}
													echo ("</div>"); 
													break;  
											}
										} 
										echo ("<div class=\"added\"></div>"); 
										echo ("</div>"); 
                                ?>
                            </div>
                            
                        </div>
                    </div>
                    <?
                        //echo ("<form method=\"post\">"); 
                        //$iConfirmed = 0; 
                        //if (count($oOwaesItem->subscriptions()) > 0) { 
                        //        echo ("<div class=\"buckets\">"); 
                        //            echo ("<div class=\"bucket box sameheight col-md-4\" id=\"geselecteerd\">
                        //                    <h2>Geselecteerd</h2> 
                        //                ");  
                        //                foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
                        //                    switch ($oValue->state()) {
                        //                        case SUBSCRIBE_CONFIRMED: 
                        //                            $iConfirmed ++; 
                        //                            $oUser = new user($iUser);
                        //                            echo ("<div id='user" . $oUser->id() . "'>");  
                        //                            echo ($oUser->html("templates/userid.html")); 
                        //                            $oTransaction = $oValue->payment(); 
                        //                            //$oTransaction = $oOwaesItem->transactions($iUser); 
                        //                            $strIMG = fixPath("img/handshake" . ($oTransaction->signed()?1:0) . ".png");
                        //                            if ($oTransaction->signed()) {
                        //                                echo ("<img src=\"" . $strIMG . "\" alt=\"start transactie\" align=\"right\" />"); 
                        //                            } else {
                        //                                echo ("<a href=\"owaes-transactie.ajax.php?owaes=" . $iID . "&user=" . $iUser . "\" class=\"transactie\"><img src=\"" . $strIMG . "\" alt=\"start transactie\" align=\"right\" /></a>"); 
                        //                            }
                        //                            echo ("</div>"); 
                        //                            break;  
                        //                    }
                        //                } 
                        //                echo ("<div class=\"added\"></div>"); 
                        //                echo ("</div>"); 
                                    //echo ("<div class=\"bucket box sameheight col-md-4\" id=\"nieuw\">
                                    //        <h2>Nieuw</h2> 
                                    //    "); 
                                    //    foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
                                    //        switch ($oValue->state()) {
                                    //            case SUBSCRIBE_SUBSCRIBE: 
                                    //                $oUser = new user($iUser); 
                                    //                echo ("<div id='user" . $oUser->id() . "'>");  
                                    //                    echo ("<a href='#goedkeuren' class='goedkeuren' rel='" . $oUser->id() . "'>goedkeuren</a>"); 
                                    //                    echo ($oUser->html("templates/userid.html"));  
                                    //                    echo ("<a href='#afkeuren' class='afkeuren' rel='" . $oUser->id() . "'>afkeuren</a>");  
                                    //                echo ("</div>"); 
                                    //                break; 
                                    //            case SUBSCRIBE_NEGOTIATE: 
                                    //                $oUser = new user($iUser);
                                    //                echo ("<div id='user" . $oUser->id() . "'>");  
                                    //                    echo ("<a href='#goedkeuren' class='goedkeuren' rel='" . $oUser->id() . "'>goedkeuren</a>"); 
                                    //                    echo ($oUser->html("templates/userid.html"));  
                                    //                    echo ("<a href='#afkeuren' class='afkeuren' rel='" . $oUser->id() . "'>afkeuren</a>"); 
                                    //                echo ("</div>"); 
                                    //                break;  
                                    //        } 
                                    //    } 
                                    //    echo ("</div>"); 
                                //    echo ("<div class=\"bucket box sameheight col-md-4\" id=\"geweigerd\">
                                //            <h2>Geweigerd</h2> 
                                //        "); 
                                //        foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
                                //            switch ($oValue->state()) {
                                //                case SUBSCRIBE_DECLINED: 
                                //                    $oUser = new user($iUser);
                                //                    echo ("<div id='user" . $oUser->id() . "'>");  
                                //                    echo ($oUser->html("templates/userid.html")); 
                                //                    echo ("</div>"); 
                                //                    break;  
                                //            }
                                //        } 
                                //        echo ("<div class=\"added\"></div>"); 
                                //        echo ("</div>"); 
                                //echo ("</div>"); 
								
								
								 
								?>
									 
										<div style="display: none; ">
											<div id="mailgoedgekeurd">
												<h3>Mail voor geselecteerde personen: </h3>
<textarea name="mailgoedgekeurd">Beste, 

U werd geselecteerd voor deze opdracht. </textarea>
											</div>
											<div id="mailafgewezen">
												<h3>Mail voor geweigerde personen: </h3>
												<textarea name="mailgeweigerd">Beste, 

U werd niet gekozen voor deze opdracht.</textarea>
											</div>
										</div>
									<input type="submit" value="opslaan" name="save" id="savestep1" class="knopgroen btn btn-default btn-sm pull-right"/>
									<!--a href="owaes-transacties.php?owaes=<? echo $iID; ?>" class="knopgeel">transacties</a-->
									<?
										echo (' <input type="submit" value="aanpassen" name="edit" class="knop edit btn btn-default btn-sm pull-right" /> ');
										if ($oOwaesItem->state() != STATE_FINISHED) echo ('<input type="submit" value="inschrijvingen afsluiten" name="close" class="knoprood btn btn-default btn-sm pull-right" /> ');  
										if ($iConfirmed == 0) echo (' <input type="submit" value="verwijderen" name="delete" class="knoprood btn btn-default btn-sm pull-right" onclick="return confirm(\'Weet u zeker dat u deze opdracht wilt verwijderen?\'); " /> '); 
									?>
									
								<? 
						} else { 
							echo (' <input type="submit" value="aanpassen" name="edit" class="knop edit btn btn-default btn-sm pull-right" /> ');
							if ($oOwaesItem->state() != STATE_FINISHED) echo (' <input type="submit" value="inschrijvingen afsluiten" name="close" class="knoprood btn btn-default btn-sm pull-right" /> ');  
							if ($iConfirmed == 0) echo (' <input type="submit" value="verwijderen" name="delete" class="knoprood btn btn-default btn-sm pull-right" onclick="return confirm(\'Weet u zeker dat u deze opdracht wilt verwijderen?\'); " /> ');  
						}
						echo ("</form>"); 
                            
                    ?> 
            	</div> 
            <? echo $oPage->endTabs(); ?>
        </div>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
   
    </body>
</html>
