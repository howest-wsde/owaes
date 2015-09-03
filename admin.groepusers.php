<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = security(TRUE); 
//	if (!$oSecurity->admin()) $oSecurity->doLogout(); 
	
	$oPage->addJS("script/admin.js"); 
	$oPage->addCSS("style/admin.css"); 

	$iGroep = intval($_GET["group"]); 
	$oGroep = group($iGroep); 
	$oMijnRechten = $oGroep->userrights(me());
	
	if (!($oMijnRechten->userrights() || $oMijnRechten->useradd() || $oMijnRechten->userdel() || $oMijnRechten->groupinfo())) stop("group"); 
	
    $groupNr = $_GET["group"];
 	if ((isset($_POST["adduser"]))&&(intval($_POST["adduser"]) != 0)) {  
		if ($oMijnRechten->useradd()) {
			$oGroep->addUser(intval($_POST["adduser"])); 
		} // else $oSecurity->doLogout();  
	}
	if (isset($_POST["setadmin"])) {   
		if ($oMijnRechten->userrights()) {
			$iSetAdmin = intval($_POST["setadmin"]); 
			if($oGroep->users($iSetAdmin) != FALSE) {
				// oude admin alle rechten geven 
				$oCurrentAdminRechten = $oGroep->userrights($oGroep->admin()->id());
				$arRechten = array("useradd", "userdel", "userrights", "owaesadd", "owaesedit", "owaesdel", "owaesselect", "owaespay", "groupinfo"); 
				foreach ($arRechten as $strRecht) $oCurrentAdminRechten->right($strRecht, TRUE); 
				$oCurrentAdminRechten->update(); 
				
				// nieuwe admin
				$oGroep->admin($iSetAdmin);  
				$oGroep->update(); 
		
				$oExperience = new experience($iSetAdmin);  
				$oExperience->detail("reason", "admin for group");  
				$oExperience->sleutel("group.admin." . $oGroep->id());   
				$oExperience->add(50);  
			}
		} else stop("rechten"); 
	}
	if (isset($_POST["deluser"])) {   
		if ($oMijnRechten->userdel()) {
			$oGroep->removeUser($_POST["deluser"]); 
		} else stop("rechten"); 
	}
	
	if (isset($_POST["changeprops"])) {
		if ($oMijnRechten->groupinfo()) {
			$oGroep->naam($_POST["groepsnaam"]); 
			$oGroep->website($_POST["groepsurl"]); 
			if (user(me())->admin()) $oGroep->isDienstverlener(isset($_POST["isdienstverlener"])); 
			$oGroep->info($_POST["info"]); 	 	
			$oGroep->wijzoeken($_POST["wijzoeken"]); 	 	
			if ($_FILES["img"]["error"] == 0){  
				$strTmp = "upload/tmp/" . $_FILES["img"]["name"];  
				move_uploaded_file($_FILES["img"]["tmp_name"], $strTmp);
				createGroupPicture($strTmp, $oGroep->id()); 
			}
			
			$oGroep->update(); 
		} else stop("rechten"); 
	}
	
 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
        <script>
			function persoonZoeken() { 
				strSearch = $("#persoonzoeken").val();
				if (strSearch.length > 2) {
					$("div#persoonzoekenresult").load("admin.groepusers.list.php", {"f": strSearch});  
				} else {
					$("div#persoonzoekenresult").html(""); 
				}
			}
			var arTimerPersoonZoeken; 
			$(document).ready(function(e) {
                $("#persoonzoeken").keydown(function(event){
					if (arTimerPersoonZoeken) window.clearTimeout(arTimerPersoonZoeken); 
					switch(event.keyCode) {
						case 13: 
							persoonZoeken();
							event.preventDefault();
							return false; 
							break; 
						default:  
							arTimerPersoonZoeken = window.setTimeout(function() {
								persoonZoeken();
							}, 500); 
							break; 	
					}
				})
			
				$(document).on("click", "a.userrights", function() { // admin.groepusers
					$(this).parent().load($(this).attr("href")); 
					return false; 	
				});  
				$(document).on("click", "a.adduser", function() { // admin.groepusers
					$("#adduser").val($(this).attr("rel")); 
					$("#admingroupform").submit(); 
					return false; 	
				});  
				
				$("a#personenzoeken").click(function(){
					$("div#persoonzoekenresult").load("admin.groepusers.list.php", {"f": $("#persoonzoeken").val()}); 
					return false; 
				}); 
            });
		</script>
    </head>
    <body id="index">
    <?php echo $oPage->startTabs(); ?> 
    	<div class="body">
        	
                <div class="container">
                    <div class="row">
					        <?php 
                                echo $oSecurity->me()->html("user.html");
                            ?>
                    </div>
                    <div class="main market admin-groepusers"> 
                    	<?php if (user(me())->admin()) { ?>
                        <?php include "admin.menu.xml"; ?>
                        <?php } ?>
                        <form method="post" class="form-horizontal" id="admingroupform" enctype="multipart/form-data"> 
                        
                       	<?php if ($oMijnRechten->groupinfo()) { ?>
                            <fieldset>
                                <legend>Algemene gegevens</legend>
                                <div class="form-group">
                                    <label for="username" class="control-label col-lg-2">Groepsnaam:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="groepsnaam" class="naam form-control" id="naam" placeholder="Groepsnaam" value="<?php echo inputfield($oGroep->naam()); ?>" />
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label for="groepsurl" class="control-label col-lg-2">Website:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="groepsurl" class="groepsurl form-control" id="groepsurl" placeholder="Website" value="<?php echo inputfield($oGroep->website()); ?>" />
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label for="description" class="control-label col-lg-2">Omschrijving:</label>
                                    <div class="col-lg-10">
                                        <textarea name="info" id="info" class="form-control wysiwyg" placeholder="Vertel ons iets over deze groep..."><?php echo textarea($oGroep->info()); ?></textarea>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label for="wijzoeken" class="control-label col-lg-2">Wij zoeken:</label>
                                    <div class="col-lg-10">
                                        <textarea name="wijzoeken" id="wijzoeken" class="form-control wysiwyg" placeholder="Naar wie is uw bedrijf op zoek?"><?php echo textarea($oGroep->wijzoeken()); ?></textarea>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label for="img" class="control-label col-lg-2">Foto:</label>
                                    <div class="col-lg-10">
                                        <input type="file" name="img" ext="jpg,jpeg,gif,bmp,png" class="img image form-control" id="img" placeholder="" value="" />
                                        <?php echo $oGroep->getImage(); ?>
                                    </div> 
                                </div>
                                <?php if (user(me())->admin()) { ?>
                                    <div class="form-group">
                                        <label for="dienstverlener" class="control-label col-lg-2">Dienstverlener:</label>
                                        <div class="col-lg-10"><div class="checkbox-control form-control">
                                            <input type="checkbox" name="isdienstverlener" id="isdienstverlener" value="1" <? if ($oGroep->isDienstverlener()) echo ('checked="checked"');  ?> />
                                            <label for="isdienstverlener">Deze groep vertegenwoordigt een dienstverlener</label>
                                        </div></div>
                                    </div>
                                <?php }  ?> 
 
                    
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <input type="submit" value="Gegevens opslaan" id="profile" class="btn btn-default pull-right" name="changeprops" />
                                    </div>
                                </div>
                            </fieldset>    
                        <?php } ?>        
                         
                        <?php if ($oMijnRechten->useradd()) { ?>
                            <fieldset>
                                <legend>Persoon toevoegen:</legend> 
                                <input type="hidden" name="adduser" id="adduser" value="0" />
                                <div class="form-group">
                                    <label for="username" class="control-label col-lg-2">Persoon zoeken:</label>
                                    <div class="col-lg-10"> 
                                    	<input name="persoonzoeken" id="persoonzoeken" class="form-control" placeholder="Tik een naam in" />
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-2"></div>
                                    <div class="col-lg-10" id="persoonzoekenresult"></div> 
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <a href="#" id="personenzoeken" class="btn btn-default pull-right">Personen zoeken</a>
                                    </div>
                                </div> 
                            </fieldset> 
                        <?php } ?>
                        
                         <?php if ($oMijnRechten->useradd()) { ?>
                            <fieldset>
                                <legend>Gebruikerslijst:</legend>  
                                <table>
                                    <tr>
                                        <th colspan="2" class="borderright">Gebruiker</th>
                                        <?php if ($oMijnRechten->userrights()) { ?> 
                                            <th colspan="3" class="borderright">Groepsleden</th>
                                            <th colspan="3" class="borderright">OWAES-items</th>
                                            <th colspan="2" class="borderright">Inschrijvingen</th>
                                            <th colspan="2" class="borderright">Groep</th>
                                        <?php } ?>
                                        <?php
                                            if ($oMijnRechten->userdel()) echo "<th>Actie's</th>"
                                        ?>
                                    </tr>
                                    <tr> 
                                        <th>voornaam</th>
                                        <th class="borderright">familienaam</th>
                                        <?php if ($oMijnRechten->userrights()) { ?> 
                                            <th>toevoegen</th>
                                            <th>verwijderen</th>
                                            <th class="borderright">rechten aanpassen</th>
                                            <th>toevoegen</th>
                                            <th>aanpassen</th>
                                            <th class="borderright">verwijderen</th>
                                            <th>bevestigen</th>
                                            <th class="borderright">afhandelen</th>
                                            <th>gegevens</th>
                                            <th class="borderright">beheerder</th>
                                        <?php } ?>
                                        <?php
                                            if ($oMijnRechten->userdel()) echo "<th></th>"
                                        ?>
                                    </tr>
                                    <?php   
									
									$oLeden = new userlist(); 
									$oLeden->group($oGroep->id(), FALSE); 
                                    foreach ($oLeden->getList() as $oUser) {
                                    //	$oRechten = $oGroep->userrights($oUser->id());  
                                        echo "<tr class=\"confirmed" . ($oGroep->userrights($oUser->id())->value("confirmed") ? 1:0) . "\">";  
                                        $oUser->unlocked(TRUE); 
                                        echo "<td>" . $oUser->firstname() . "</td>"; 
                                        echo "<td>" . $oUser->lastname() . "</td>";   
                                        if ($oMijnRechten->userrights()) {
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "useradd") . "</td>";  
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "userdel") . "</td>";  
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "userrights") . "</td>";  
                                            
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "owaesadd") . "</td>";  
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "owaesedit") . "</td>";  
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "owaesdel") . "</td>";  
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "owaesselect") . "</td>";  
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "owaespay") . "</td>";   
                                            echo "<td>" . checkbox($oGroep, $oUser->id(), "groupinfo") . "</td>";  
            
                                            if ($oGroep->admin()->id() == $oUser->id()) {
                                                echo "<td><span class=\"checkbox on fixed\">1</span></td>";   
                                            } else {
                                                echo "<td><button class=\"checkbox off\" value=\"" . $oUser->id() . "\" name=\"setadmin\" /></td>";  
                                            }  
                                        } 
                                        
                                        if ($oMijnRechten->userdel()) {
                                            if ($oGroep->admin()->id() == $oUser->id()) { 
                                                echo "<td></td>"; 
                                            } else {
                                                echo "<td><button class=\"actiondelete\" value=\"" . $oUser->id() . "\" name=\"deluser\" /></td>"; 
                                            }  
                                        }
                                        
                                        
                                        echo "</tr>"; 
                                    }
									
                                    ?>
                                </table>
						 	</fieldset>
                            <?php } ?>
                             
                            <?php

								function checkbox($oGroep, $iUser, $strWut) {
									$oRechten = $oGroep->userrights($iUser);  
									if ($oRechten->admin()) {
										return "<span class=\"checkbox on fixed\">1</span>"; 
									} else {
										if ($oRechten->right($strWut)) {
											return "<a class=\"checkbox on userrights\" href=\"" . fixpath("admin.groepusers.change.php?g=" . $oGroep->id() . "&u=" . $iUser . "&w=" . $strWut . "&v=0") . "\">1</a>"; 
										} else {
											return "<a class=\"checkbox off userrights\" href=\"" . fixpath("admin.groepusers.change.php?g=" . $oGroep->id() . "&u=" . $iUser . "&w=" . $strWut . "&v=1") . "\">0</a>"; 
										}
									}
								}
							?>
                        
                        </form>
                    </div>
                </div> 
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
