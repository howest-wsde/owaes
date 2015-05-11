<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = security(TRUE); 
	if (!$oSecurity->admin()) stop("admin"); 
	
	$oPage->addJS("script/admin.js"); 
	$oPage->addCSS("style/admin.css"); 
 
 	if (isset($_POST["addgroup"])) {
		$oGroep = new group(); 
		$oGroep->naam($_POST["naam"]);
		$oGroep->info($_POST["info"]);
		$oGroep->admin($_POST["admin"]);
		$oGroep->update(); 
		if ($_FILES["img"]["error"] == 0){  
			$strTmp = "upload/tmp/" . $_FILES["img"]["name"]; 
			move_uploaded_file($_FILES["img"]["tmp_name"], $strTmp);
			createGroupPicture($strTmp, $oGroep->id()); 
		}
		$oGroep->update(); 
		$oGroep->addUser(intval($_POST["admin"])); 
	}
	
 	if (isset($_POST["delgroup"])) {
		$oGroep = group(intval($_POST["delgroup"])); 
		$oGroep->delete(TRUE); 
		$oGroep->update(); 
	}
	

	$oExperience = new experience(me());  
	$oExperience->detail("reason", "admin-groepen");     
	$oExperience->add(2);  
 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
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
                    <div class="main market admin-groepen"> 
                        <?php include "admin.menu.xml"; ?>
                    	<h1>Groepsbeheer </h1>
                        <form method="post" class="groepToevoegenForm form-horizontal" enctype="multipart/form-data"> 

							<fieldset>
                                <legend>Groepen</legend>
                                <div class="form-group">
                                    <table>
                                        <tr>
                                            <th class="order asc">naam</th>
                                            <th class="order">info</th>
                                            <th class="order">beheerder</th>
                                            <th>...</th>
                                        </tr>
                                        <?php
                                            $oGroepen = new grouplist(); 
                                            foreach ($oGroepen->getList() as $oGroep) {
                                                echo "<tr>";  
                                                echo "<td>" . $oGroep->naam() . "</td>"; 
                                                echo "<td>" . shorten($oGroep->info()) . "</td>"; 
                                                echo "<td>" . $oGroep->admin()->getName() . "</td>"; 
                                                echo "<td>
														<a href=\"admin.groepusers.php?group=" . $oGroep->id() . "\">aanpassen</a> 
														<button class=\"actiondelete\" value=\"" . $oGroep->id() . "\" onclick=\"return confirm('Weet je zeker?');\" name=\"delgroup\" />
													</td>"; 
                                                echo "</tr>"; 
                                            }
                                        ?>
                                    </table>
                                </div>
                            </fieldset>
                            
                            
                            <fieldset>
                                <legend>Nieuwe groep toevoegen</legend>
                                <div class="form-group">
                                    <label for="username" class="control-label col-lg-2">Groepsnaam:</label>
                                    <div class="col-lg-10">
                                        <input type="text" name="naam" class="naam form-control" id="naam" placeholder="Groepsnaam" value="" />
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label for="username" class="control-label col-lg-2">Beheerder:</label>
                                    <div class="col-lg-10">
                                        <select name="admin" class="form-control" id="admin">
                                        	<option value="0">- selecteer een beheerder -</option>
											<?php
												$oUserList = new userlist();   
												foreach ($oUserList->getList() as $oUser) { 
													echo  "<option value=\"" . $oUser->id() . "\">" . $oUser->getName() . "</option>"; 	
												}
                                            ?>
                                        </select> 
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label for="description" class="control-label col-lg-2">Omschrijving:</label>
                                    <div class="col-lg-10">
                                        <textarea name="info" id="info" class="form-control" placeholder="Vertel ons iets over deze groep..."></textarea>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label for="img" class="control-label col-lg-2">Foto:</label>
                                    <div class="col-lg-10">
                                        <input type="file" name="img" class="img image form-control" id="img" placeholder="" value="" /> 
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <input type="submit" value="Gegevens opslaan" id="profile" class="btn btn-default pull-right" name="addgroup" />
                                    </div>
                                </div>
                            </fieldset>  
                            
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
