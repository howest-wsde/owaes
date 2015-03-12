<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
   
	$iID = intval($_GET["owaes"]); 
	$oOwaesItem = owaesitem($iID);   
	
	$oNotification = new notification(); 
	$oNotification->read("subscription." . $iID );  
	
	if (!$oOwaesItem->userrights("select", me())) {  
		redirect("owaes.php?owaes=" . $iID); 
		exit(); 
	}
	

	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload");     
	$oExperience->add(1);  
	
	$oNotification = new notification(); 
	$oNotification->read("owaes." . $iID); 
	
	if (isset($_POST["close"])) {
		$oOwaesItem->state(STATE_FINISHED); 
		$oOwaesItem->update(); 
	} else if (isset($_POST["delete"])) {
	//	$oOwaesItem->state(STATE_DELETED); 
	//	$oOwaesItem->update();  
	//	redirect(fixPath("main.php")); 
	}  else if (isset($_POST["edit"])) {
		redirect(fixPath("owaesadd.php?edit=" . $iID));  
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
					$("form.selecteerform").append(
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
					$("form.selecteerform").append( 
						$("<input />").attr("name", "afgekeurd[]").attr("type", "hidden").attr("id", strID).addClass("unsaved").addClass("nieuwafgekeurd").val(iUser)
					);	
					sameHeight();
					return false; 
				})
				$("form.selecteerform").submit(function(){
				//$("input#aanpassen").click(function(){
					arModals = Array(); 
					arGoedgekeurd = Array(); 
					arAfgekeurd = Array(); 
					$(this).find(".nieuwgoedgekeurd").each(function(){
						arGoedgekeurd[arGoedgekeurd.length] = $(this).val(); 
					})
					$(this).find(".nieuwafgekeurd").each(function(){
						arAfgekeurd[arAfgekeurd.length] = $(this).val(); 
					})
					if (arGoedgekeurd.length > 0) arModals[arModals.length] = "modal.mailconfirm.php?m=<? echo $iID; ?>&s=1&u=" + arGoedgekeurd.join(","); 
					if (arAfgekeurd.length > 0) arModals[arModals.length] = "modal.mailconfirm.php?m=<? echo $iID; ?>&s=0&u=" + arAfgekeurd.join(","); 
					arModals[arModals.length - 1] += "&refresh=1"; 
					loadModals(arModals);  
					return (arModals.length==0);  
				}); 
			}); 
		</script> 
    </head>
    <body id="owaes"> 
    	<div class="body">                
            <? echo $oPage->startTabs(); ?> 
            <div class="container content content-marktitem">
            	<div class="row">
					<?
						echo $oSecurity->me()->html("user.html");
                    ?>
                </div> 
                <div class="ownerDetail">  
					<? echo $oOwaesItem->HTML("owaesdetail.html");  ?> 
					
                    <? if ($oOwaesItem->state() == STATE_DELETED) { ?>
						<p>Dit item werd verwijderd</p>
					<? } else { ?>
                        <form method="post" class="selecteerform"> 
                            <div class="row">
                                <div class="col-md-6 nieuwInschrijvingen">
                                    <div class="bucket box sameheight col-md-4" id="nieuw">
                                        <h2>Nieuw</h2>
                                        <?  
                                            foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
                                                switch ($oValue->state()) {
                                                    case SUBSCRIBE_SUBSCRIBE: 
                                                        $oUser = user($iUser); 
                                                        echo ("<div id='user" . $oUser->id() . "'>");  
                                                            echo ($oUser->html("userid.html"));  
                                                            echo("<div class='toestemming'>");
                                                            echo ("<span class='cursor'><a href='#goedkeuren' class='goedkeuren' rel='" . $oUser->id() . "'><span class='icon icon-check'></span><span>Goedkeuren</span></a></span>"); 
                                                            echo ("<span class='cursor'><a href='#afkeuren' class='afkeuren' rel='" . $oUser->id() . "'><span class='icon icon-close'></span><span>Afkeuren</span></a></span>");
                                                            echo("</div>");
                                                        echo ("</div>"); 
                                                        break;  
                                                } 
                                            }  
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="row geweigerd">
                                        <div class="bucket box sameheight col-md-4" id="geweigerd">
                                            <h2>Niet geselecteerd</h2>
                                            <? 
                                                foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
                                                    switch ($oValue->state()) {
                                                        case SUBSCRIBE_DECLINED: 
                                                        case SUBSCRIBE_ANNULATION: 
                                                            $oUser = user($iUser);
                                                            echo ("<div id='user" . $oUser->id() . "'>");  
                                                            echo ($oUser->html("userid.html"));  
                                                            echo ("</div>"); 
                                                            break;  
                                                    }
                                                }  
                                            ?>
                                            <div class="added"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="row geselecteerd">
                                        <?
                                            $iConfirmed = 0;  
                                        ?>
                                        <div class="buckets">
                                        <div class="bucket box sameheight col-md-4" id="geselecteerd">
                                            <h2>Geselecteerd</h2>
                                            <?
                                                foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
                                                    switch ($oValue->state()) {
                                                        case SUBSCRIBE_CONFIRMED: 
                                                            $iConfirmed ++; 
                                                            $oUser = user($iUser);
                                                            $oTransaction = $oValue->payment(); 
                                                            echo ("<div id='user" . $oUser->id() . "'>");  
                                                            echo ($oUser->html("userid.html")); 
                                                             
                                                            echo '<button data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle pull-right" type="button">
                                                                Acties <span class="caret"></span>
                                                                </button>
                                                                <ul role="menu" class="dropdown-menu pull-right">';  
                                                            if (!$oTransaction->signed()) { 
                                                                if ($oOwaesItem->task()) {
                                                                    echo ("<li><a href=\"modal.transaction.php?m=" . $iID . "&u=" . $iUser . "&refresh=1\" class=\"domodal\">Transactie uitvoeren</a></li>"); 
                                                                    echo ("<li class=\"divider\"></li>"); 
                                                                }
                                                                echo ("<li><a href=\"owaes-annulation.php?u=" . $iUser . "&m=" . $iID . "\">Annulatie met akkoord</a></li>");  
                                                                echo ("<li><a href=\"modal.report.php?u=" . $iUser . "&m=" . $iID . "&reason=twist\" class=\"domodal\">Afspraak niet nagekomen</a></li>");  
                                                            }  
                                                            echo $oUser->html("[actions:noicon]"); 
                                                            //echo ("<li><a href=\"#\">Toevoegen aan groep</a></li>"); 
                                                            //echo ("<li><a href=\"#\">Vriend worden</a></li>"); 
                                                            echo '</ul> ' ; 
                                                            break;  
                                                    }
                                                } 
                                            ?>
                                            <div class="added"></div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                          
                            <?
								if ($oOwaesItem->userrights("edit", me())) echo (' <a href="' . fixPath("owaesadd.php?edit=" . $iID) . '" class="knop edit btn btn-default btn-sm pull-right">aanpassen</a> '); 
                            
                                if ($oOwaesItem->state() != STATE_FINISHED) echo ('<input type="submit" value="inschrijvingen afsluiten" name="close" class="knoprood btn btn-default btn-sm pull-right" /> ');  
                                //if ($iConfirmed == 0) echo (' <input type="submit" value="verwijderen" name="delete" class="knoprood btn btn-default btn-sm pull-right" onclick="return confirm(\'Weet u zeker dat u deze opdracht wilt verwijderen?\'); " /> '); 
                                if ($iConfirmed == 0) echo (' <a href="modal.deleteitem.php?i=' . $iID . '" class="domodal knoprood btn btn-default btn-sm pull-right">verwijderen</a> '); 
                                if (count($oOwaesItem->subscriptions()) > 0) { 
                                    echo ('<input type="submit" value="opslaan" name="save" id="savestep1" class="knopgroen btn btn-default btn-sm pull-right" /> '); 
                                }
                       
                            ?> 
                        </form>
					<? } ?>
            	</div> 
				<? echo $oPage->endTabs(); ?>
            </div>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
   
    </body>
</html>
