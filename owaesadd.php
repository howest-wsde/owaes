<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oMe = user(me()); 
	
	$oPage->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true");
	$oPage->addJS("script/owaesadd.js?v3");
	//$oPage->addJS("ckeditor/ckeditor.js");
	//$oPage->addJS("script/mugifly-jquery-simple-datetimepicker-702f729/jquery.simple-dtpicker.js"); 
	//$oPage->addCSS("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"); 
	//$oPage->addCSS("script/mugifly-jquery-simple-datetimepicker-702f729/jquery.simple-dtpicker.css"); 
	
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$iID = isset($_GET["edit"])?intval($_GET["edit"]):0;
	$oOwaesItem = owaesitem($iID);
	
	$arPossiblePosters = array();  
	$arOwaesTypes = owaesType()->getAllTypes();
	foreach($arOwaesTypes as $strKey=>$strTitle) {
		$arPossiblePosters[$strKey] = array(
			"user" => array(), 
			"group" => array(), 
		); 
		if ($oMe->level() >= owaestype($strKey)->minimumlevel()) $arPossiblePosters[$strKey]["user"][me()] = $oMe; 
		foreach ($oMe->groups() as $oGroup) {
			if ($oGroup->userrights()->owaesadd()) $arPossiblePosters[$strKey]["group"][$oGroup->id()] = $oGroup;  
		}
		if (user(me())->admin()) {
			$oAllGroepen = new grouplist();   
			foreach ($oAllGroepen->getList() as $oGroup) {
				$arPossiblePosters[$strKey]["group"][$oGroup->id()] = $oGroup;  
			} 
			$oUsers = new userlist();  
			foreach ($oUsers->getList() as $oUser) {
				if (($oUser->level() >= owaestype($strKey)->minimumlevel()) || ($oUser->admin())) $arPossiblePosters[$strKey]["user"][$oUser->id()] = $oUser; 
			}
		}
	}
	
	if ($oOwaesItem->id() != 0) {
		$strType = $oOwaesItem->type()->key();  
		$bNEW = FALSE; 
	} else { 
		$strType = (isset($_GET["t"])) ? $_GET["t"] : "ervaring";
		$oOwaesItem->type($strType);  
		$bNEW = TRUE; 
	}  
	 
	$iMaxCredits = ((owaesType($strType)->direction()==DIRECTION_EARN) ? 
						min(settings("startvalues", "credits"), $oMe->credits()) :  // verdienen
						min(settings("startvalues", "credits"), settings("credits", "max") - $oMe->credits())); // uitgeven 
						
	 
	if ($oOwaesItem->editable() !== TRUE) { 
		stop($oOwaesItem->editable()); 
		exit();  
	} 
	 
	if (isset($_POST["owaesadd"])) { 
		$oLog = new log("owaesadd", array("post" => $_POST)); 
		
		$strPoster = $_POST["poster"]; 
		$arPoster = explode(".", $strPoster); 
		switch($arPoster[0]) {
			case "g": // group
				$iGroep = intval($arPoster[1]); 
				if (isset($arPossiblePosters[$strType]["group"][$iGroep])) {
					$oOwaesItem->group($iGroep);	
					$oOwaesItem->author(me()); 
				} else stop("rechten");  
				break; 	
			case "u": // gebruiker
				$iGebruiker = intval($arPoster[1]); 
				if (isset($arPossiblePosters[$strType]["user"][$iGebruiker])) {
					$oOwaesItem->group(0);	
					$oOwaesItem->author($iGebruiker); 
				} else stop("rechten");   
				break; 	
		} 
		
		$oOwaesItem->title($_POST["title"]); 
		$oOwaesItem->body($_POST["description"]); 
		$oOwaesItem->details("verzekeringen", (isset($_POST["verzekering"])?$_POST["verzekering"]:array())); 
		$oOwaesItem->location($_POST["locationfixed"], $_POST["locationlat"], $_POST["locationlong"]); 
		
		foreach ($oOwaesItem->data() as $iDate) {
			$oOwaesItem->removeMoment($iDate);
		}  
		if (isset($_POST["data"])) foreach ($_POST["data"] as $iDatum) { 
			$oOwaesItem->addMoment(ddmmyyyyTOdate($_POST["datum-$iDatum"]), hhmmTOminutes($_POST["start-$iDatum"]), hhmmTOminutes($_POST["tijd-$iDatum"])); 
		}

		foreach ($oOwaesItem->getTags() as $strTag) {
			$oOwaesItem->removeTag($strTag);
		} 
		if (isset($_POST["tag"])) foreach ($_POST["tag"] as $strTag) { 
			$oOwaesItem->addTag($strTag);  
		}   

		$oOwaesItem->timing(isset($_POST["timingtime"])?$_POST["timingtime"]:0); 
		$oOwaesItem->physical($_POST["physical"]); 
		$oOwaesItem->mental($_POST["mental"]); 
		$oOwaesItem->emotional($_POST["emotional"]); 
		$oOwaesItem->social($_POST["social"]); 
		$oOwaesItem->credits($_POST["credits"]); 
		$oOwaesItem->type($_POST["type"]); 

		foreach ($oOwaesItem->subscriptions() as $iUser=>$oValue) {
			switch ($oValue->state()) {
				case SUBSCRIBE_CONFIRMED:  
					$oConversation = new conversation($iUser); 
					$oConversation->add("Er werden aanpassingen doorgevoerd in deze opdracht. Gelieve deze na te kijken. ", $oOwaesItem->id() );  
					break; 
			} 
		} 
			
		foreach ($oOwaesItem->files() as $strFile) {
			if (!in_array($strFile, isset($_POST["existingfile"])?$_POST["existingfile"]:array())) $oOwaesItem->files($strFile, FALSE); // remove file
		}
		for ($i=0; $i<count($_FILES["file"]["name"]); $i++) {
 			$strTempFN = owaestime() . "." . $_FILES["file"]["name"][$i];
			if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], "upload/market/" . md5($strTempFN))) $oOwaesItem->addFile($strTempFN); 
		} 
		$oOwaesItem->update(); 

		
		if ($bNEW) {
			$oMe = user(me()); 
			$iAddValue = settings("indicatoren", "owaesadd") ? settings("indicatoren", "owaesadd") : 2; 
			$oMe->addIndicators(array("physical"=>$iAddValue, "mental"=>$iAddValue, "emotional"=>$iAddValue, "social"=>$iAddValue, ), TIMEOUT_ADDEDNEW, $oOwaesItem->id()); 
			$oMe->addbadge($_POST["type"]); 

			$oExperience = new experience(me());  
			$oExperience->detail("reason", "item toegevoegd");     
			$oExperience->add(60);  
		}
	 
		redirect("index.php?t=" . $oOwaesItem->type()->key());  
		exit();  
		
	} 
	  
	list($iLat, $iLong) = $oOwaesItem->LatLong(); 

	$oPage->tab("market.$strType");  
	 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
        <script>
		
			// GOOGLE MAP
			var map; 
			<?php
				if ($iLat + $iLong != 0) {
					echo ("var startpos = new google.maps.LatLng(" . ($iLat) . ", " . ($iLong) . ");");  
				} else {
					echo ("var startpos = new google.maps.LatLng(" . settings("geo", "latitude") . ", " . settings("geo", "longitude") . ");"); 
				}
			?>
			var marker; 
			function initialize() {
				var mapOptions = {
					zoom: 12,
					center: startpos,
					disableDefaultUI: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				<?php
					if ($iLat + $iLong != 0) {
						echo (" oPos = new google.maps.LatLng(" . $iLat . ", " . $iLong . "); 
						setMarker(oPos); "); 
					} 
				?> 
			}
			google.maps.event.addDomListener(window, 'load', initialize); 
			
			$(document).ready(function(e) {
				setPosters(); 
				$("select#kiesowaestype").change(function(){
					setPosters(); 
				})
				$("select#person").change(function(){
					setTypes(); 
				}) 
				$("a.delfileinput").click(function(){ // for existing files
					$(this).parent().remove(); 
					return false; 	
				})
				$(document).on("change", "input.fileupload", function(){ 
					if (!$(this).attr("id")) {
						strID = "fileupload" + Math.floor(Math.random()*10000);
						$(this).attr("id", strID); 
						$(this).after($("<a href='#' class='delfileinput'>verwijderen</a>").attr("rel", strID).click(function(){
							$("input#" + $(this).attr("rel")).remove(); 
							$(this).remove(); 
							return false; 	
						})); 
						$(this).parent().append($('<input name="file[]" type="file" ext="pdf,doc,docx,txt,jpg,jpeg,gif,bmp,png,xls,xlsx,md,ppt,pps,odt,ods,odp,csv,svg" class="fileupload" placeholder="Bijlages (optioneel)" multiple />')); 
					}
				})
            });
			
			<?php
				$arJsonPosters = array(); 
				foreach ($arPossiblePosters as $strTypeKey=>$arList) {
					$arJsonPosters[$strTypeKey] = array(); 
					foreach ($arList["user"] as $iUser=>$oUser) $arJsonPosters[$strTypeKey][] = "u.$iUser"; 
					foreach ($arList["group"] as $iGroup=>$oGroup) $arJsonPosters[$strTypeKey][] = "g.$iGroup";  
				}
			?>
			var arP = <?php echo json_encode($arJsonPosters); ?>; 
			function setPosters() { 
				$("select#person option").attr("disabled", "disabled"); 
				strType = $("select#kiesowaestype").val();  
				arPersons = arP[strType];  
				for(i=0;i<arPersons.length;i++) {
					$("select#person option[value='" + arPersons[i] + "']").attr("disabled", false); 
				} 
			}
			function setTypes() {
				$("select#kiesowaestype option").attr("disabled", "disabled"); 
				strPerson = $("select#person").val();  
				for (strType in arP){
					if (arP[strType].indexOf(strPerson) >= 0) $("select#kiesowaestype option[value='" + strType + "']").attr("disabled", false); 
				}
			}

		</script>  
    </head>
    <body id="owaesadd">               
        <?php echo $oPage->startTabs(); ?> 
    	<div class="body content content-market content-market-add container">
        	
            	<div class="row">
					<?php 
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                <div class="container sidecenterright"> 
                 
                <div class="errors"></div>
                
                <form method="post" class="form-horizontal" id="frmowaesadd" name="frmowaesadd" enctype="multipart/form-data">
                	<?php  
						$arOwaesTypes = owaesType()->getAllTypes();
						
						if (isset($arOwaesTypes[$strType])) {
							echo ("<legend class=\"aanbod\">Aanbod toevoegen: <strong>" . $arOwaesTypes[$strType] . "</strong> <small>(" . settings("credits", "name", "x") . " " . ((owaesType($strType)->direction()==DIRECTION_EARN) ? "verdienen" : "uitgeven") . ")</small></legend>"); 
						} else {
							echo ("<legend class=\"aanbod\">Aanbod toevoegen</legend>"); 
						}  
					?>
                          
                    <ul class="nav nav-tabs" id="tabsAdd">
                      <li class="active"><a href="#algemeen" data-toggle="tab" class="algemeen">Algemeen</a></li>
                      <li><a href="#tijdlocatie" data-toggle="tab" class="tijdlocatie" id="tijdlocatietab" >Praktisch </a></li>
                      <li><a href="#compensatie" data-toggle="tab" class="compensatie">Compensatie</a></li>
                    </ul>
                    
                    <div class="tab-content">
                      <div class="tab-pane fade in active" id="algemeen">
                      <dl id="algemeen">
                            	<?php 
									//vardump ($arPossiblePosters); 
									
									$arPosters = array(
										"users" => array(), 
										"groups" => array(), 
									); 
									foreach ($arPossiblePosters as $strTypeKey=>$arList) {
										foreach ($arList["user"] as $iUser=>$oUser) {
											$arPosters["users"][$iUser] = $oUser; 
										}	
										foreach ($arList["group"] as $iGroup=>$oGroup) {
											$arPosters["groups"][$iGroup] = $oGroup; 
										}	
									}
									
									//vardump($arPosters); 
								
									if (count($arPosters["users"]) + count($arPosters["groups"]) > 1) {
										
										echo ('<div class="form-group"> 
                                				<div class="row"><div class="col-lg-2"><h4>Aanbieder</h4></div></div> 
												<div class="col-lg-12">');   
										echo '<select name="poster" id="person" class="required form-control">'; 
											
											if (count($arPosters["groups"])+count($arPosters["users"]) > 1) echo "<optgroup label=\"Groepen\">";  
											foreach ($arPosters["groups"] as $iGroup => $oGroup) {
												if ($oOwaesItem->group() && $oOwaesItem->group()->id()==$iGroup) { 
													echo ("<option selected=\"selected\" value=\"g." . $iGroup . "\">" . $oGroup->naam() . " (" . $oGroup->credits() . " " . settings("credits", "name", "x") . ")</option>"); 
												} else {
													echo ("<option value=\"g." . $iGroup . "\">" . $oGroup->naam() . " (" . $oGroup->credits() . " " . settings("credits", "name", "x") . ")</option>"); 
												}
											} 
											if (count($arPosters["groups"])+count($arPosters["users"]) > 1) echo "</optgroup>"; 
											
											if (count($arPosters["groups"])+count($arPosters["users"]) > 1) echo "<optgroup label=\"Gebruikers\">";  
											foreach ($arPosters["users"] as $iUser => $oUser) {
												if ((!$oOwaesItem->group() && $oOwaesItem->author()->id()==$oUser->id()) || ($oOwaesItem->author()->id()==0 && me()==$oUser->id())) {  
													echo ("<option selected=\"selected\" value=\"u." . $oUser->id() . "\">" . $oUser->getName() . "</option>"); 
												} else {
													echo ("<option value=\"u." . $oUser->id() . "\">" . $oUser->getName() . "</option>"); 
												}
											} 
											if (count($arPosters["groups"])+count($arPosters["users"]) > 1) echo "</optgroup>"; 
										
										echo ('</select>
												</div>
											</div>');   
										
									}  else {
										foreach ($arList["user"] as $iUser=>$oUser) $strPerson = "u.$iUser";
										foreach ($arList["group"] as $iGroup=>$oGroup) $strPerson = "g.$iGroup"; 
										echo '<input type="hidden" id="person" name="poster" value="' . $strPerson  . '" />'; 
									}
								 
									
								?>  
                                <div class="form-group">
                                
                                	<div class="row"><div class="col-lg-2"><h4>Titel</h4></div></div>
                         
                                    <div class="col-lg-10">
                                        <input type="text" name="title" id="title" class="required form-control" placeholder="Titel voor uw aanbod" value="<?php echo inputfield($oOwaesItem->title()); ?>" />
                                    </div>
                                    <div class="col-lg-2"> 
                                        <dd>
                                        <select class="form-control aanbod" name="type" id="kiesowaestype"> 
                                            <?php 
												foreach ($arPossiblePosters as $strTypeKey=>$arList) {
													if (count($arList["user"])+count($arList["group"]) > 0) {
														$oTempType = owaestype($strTypeKey);
														$strSelected = ($strType==$strTypeKey) ? "selected=\"selected\"" : "";  
														echo "<option value=\"$strTypeKey\" $strSelected>" . $oTempType->title() . "</option>";  
														
													}
												} 
                                            ?> 
                                        </select>
                                        </dd>  
                                    </div>
                                </div>
                                <div class="form-group">
                                	<div class="row"><div class="col-lg-2"><h4>Omschrijving</h4></div></div> 
                                    <div class="col-lg-12">
                                       	<textarea name="description" id="description" class="required form-control wysiwyg" placeholder="Omschrijving"><?php echo textarea($oOwaesItem->body()); ?></textarea>
                                    </div>
                                </div>
                                 
                                <div class="form-group">
                                	<div class="row"><div class="col-lg-2"><h4>Kernwoorden</h4></div></div> 
                                 
                              
                                <div class="col-lg-12"><div class="invoer" id="tags">
                                <?php
									$iTagCount = 0;  
                                	foreach ($oOwaesItem->getTags() as $strTag) {
										$strKey = "tag" . ++$iTagCount; 
										echo ("<span class=\"tag\" id=\"$strKey\"><span>$strTag</span><a title=\"verwijderen\" href=\"#\" rel=\"$strKey\">x</a><input type=\"hidden\" name=\"tag[]\" value=\"$strTag\"></span>"); 	
									}
								?><input type="text" name="tag[]" id="tag" class="tag" placeholder="Kernwoorden, gescheiden door komma's" /> 
                                </div></div> 
                                </div> 
                                
                                
                                <div class="form-group">
                                	<div class="row"><div class="col-lg-2"><h4>Bijlages</h4></div></div> 
                                    <div class="col-lg-12">  
                                    	<div class="form-control fileuploaddiv">
                                        	<?
												foreach ($oOwaesItem->files() as $strFile) {  
													$arFile = explode(".", $strFile, 2); 
													echo ('<div class="existingfile">' . $arFile[1] . ' <input type="hidden" name="existingfile[]" value="' . $strFile . '" />(<a href="#del" class="delfileinput">verwijderen</a>)</div>'); 
												}
											?>
	                                        <input name="file[]" type="file" ext="pdf,doc,docx,txt,jpg,jpeg,gif,bmp,png,xls,xlsx,md,ppt,pps,odt,ods,odp,csv,svg" class="fileupload" placeholder="Bijlages (optioneel)" multiple /> 
                                        </div>
                                    </div> 
                                </div>
                                 
                                 
                                <div class="form-group"> 
                                    <div class="col-lg-12"> 
                                        <a href="#tijdlocatie" class="tabchange">volgende</a>  
                                        <? if (!$bNEW){ echo ('<input type="submit" name="owaesadd" class="owaesadd auto border btn btn-default pull-right" value="opslaan" />'); } ?>  
                                    </div>
                                </div>
                            </dl>
                      </div>
                      <div class="tab-pane fade" id="tijdlocatie">  
                      <!-- <legend>Tijd en locatie</legend> -->
                      
                      
                        <div class="form-group">
                      		 <div class="row">
                       
                                 <div class="col-lg-12">
                                    <h4 class="tijd">Tijd</h4>
                                 </div>
                             </div>
                      		 <div class="row">
                               <div class="col-lg-3"> 
                                    <div id="calendar"></div>
                                </div> 
                                <div class="col-lg-9">
                                    <div class="errorsTime"></div>
                                     
                                   	<?php 
										  
										$arDatums = array(); 
										foreach ($oOwaesItem->data() as $iDate) {
											$oMoment = $oOwaesItem->getMoment($iDate);  
											$arDatums[($iDate == 0) ? "" : str_date($iDate, "d/m/Y")] = array(
												"start" => minutesTOhhmm($oMoment["start"]),
												"tijd" => ($oMoment["tijd"] == 0) ? "" : (floor($oMoment["tijd"]/60) . ":" . ($oMoment["tijd"]%60)),
											); 
										}
										echo ('<script>  
											var arDatums = ' . (count($arDatums) > 0 ? json_encode($arDatums) : "{}") . '; 
										</script>'); 
									?><div id="timers">
                                    
                                     
                                   
                                   </div>
                                </div>
                            </div> <!--/row--> 
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-2"> 
                                    <h4>Locatie van de opdracht</h4>
                                </div>
                                <div class="col-lg-7">
                                    <div class="errorsTime"></div>
                                    <div id="dates"></div>
                                    <div id="datesperiods"></div>
                                </div>
                                <div class="col-lg-3">
                                    <div id="calendar"></div>
                                </div>
                            </div> <!--/row-->
                            
                            <div class="row">
                                <div class="col-lg-12">
                                <dd class="locationfixed">
                                    <input type="text" name="locationfixed" class="locationfixed_required form-control" id="location" placeholder="Geef hier je locatie in" value="<?php echo inputfield($oOwaesItem->location()); ?>" />
                                	<input type="hidden" name="locationlat" id="locationlat" value="<?php echo $iLat; ?>" />
                                	<input type="hidden" name="locationlong" id="locationlong" value="<?php echo $iLong; ?>" />
                                </dd>
                                <dd class="locationfixed"><div id="map-canvas" style="height: 300px; "></div></dd> 
                                </div>
                            </div> <!--/row-->
                        </div>
                            <div class="form-group"> 
                                <div class="col-lg-12"> 
                                    <a href="#algemeen" class="tabchange">vorige</a>
                                    <a href="#compensatie" class="tabchange">volgende</a>  
                                    <? if (!$bNEW){ echo ('<input type="submit" name="owaesadd" class="owaesadd auto border btn btn-default pull-right" value="opslaan" />'); } ?>  
                                </div>
                            </div>
                             
                      </div>
                      <div class="tab-pane fade" id="compensatie">
                      <!-- <legend>Compensatie</legend> -->
                      
                        <div class="form-group">
                            <div class="row"><div class="col-lg-2"><h4><?php echo ucfirst(settings("credits", "name", "x")); ?></h4></div></div> 
                            
                           <div class="row row-credits"> 
                               <div class="col-lg-10">
                                <input type="text" min="0" max="<? echo $iMaxCredits; ?>" name="credits" id="creditsfield" class="auto border" value="<?php echo $oOwaesItem->credits(); ?>" />
                               </div>
                           </div>
                        </div>
                           
                       
                        <div class="form-group">
                            <div class="row"><div class="col-lg-2"><h4>Indicatoren</h4></div></div> 
                           
                           <div class="row row-sociaal">
                           <label for="social" class="col-lg-2">Sociaal</label>
                           <div class="col-lg-10">
                            <input type="range" min="0" max="100" name="social" id="socialslide" value="<?php echo $oOwaesItem->social(); ?>" class="development"  />
                           </div>
                           </div>
                           
                           <div class="row row-fysiek">
                           <label for="physical" class="col-lg-2">Fysiek</label>
                           <div class="col-lg-10">
                            <input type="range" min="0" max="100" name="physical" id="physicalslide" value="<?php echo $oOwaesItem->physical(); ?>" class="development" />
                           </div>
                           </div>
                           
                           <div class="row row-kennis">
                           <label for="mental" class="col-lg-2">Kennis</label>
                           <div class="col-lg-10">
                            <input type="range" min="0" max="100" name="mental" id="mentalslide" value="<?php echo $oOwaesItem->mental(); ?>" class="development"  />
                           </div>
                           </div>
                           
                           <div class="row row-welzijn">
                           <label for="emotional" class="col-lg-2">Welzijn</label>
                           <div class="col-lg-10">
                            <input type="range" min="0" max="100" name="emotional" id="emotionalslide" value="<?php echo $oOwaesItem->emotional(); ?>" class="development"  />
                           </div>
                           </div>
                       </div>
                       
                       
                        <div class="form-group">
                                <div class="row"><div class="col-lg-2"><h4>Verzekering</h4></div></div> 
                            
                            <div class="row"> 
                                <?php
                                	$arVerzekeringen = $oOwaesItem->details("verzekeringen"); 
									if (!is_array($arVerzekeringen)) $arVerzekeringen = array(); 
								?>
                                <?php foreach (settings("verzekeringen") as $iVerzekering => $strVerzekering) { ?>
                                    <div class="col-lg-4">
                                        <input type="checkbox" name="verzekering[]" id="verzekering<?php echo $iVerzekering; ?>" value="<?php echo $iVerzekering; ?>" <?php if (in_array($iVerzekering, $arVerzekeringen)) echo "checked=checked"; ?> />
                                        <label for="verzekering<?php echo $iVerzekering; ?>" class="checkboxlabel"><?php echo $strVerzekering; ?></label>                                    
                                    </div>
								<?php } ?> 
                            </div> <!--/row-->
                       </div>
                            
                            <div class="row row-buttons">
                            
                                <div class="form-group col-lg-11">
                                	<input type="checkbox" name="voorwaarden" id="voorwaarden" value="1" <?php if ($oOwaesItem->id() != 0) echo "checked=\"checked\""; ?> />
                                    <label for="voorwaarden" class="checkboxlabel">Ik bevestig dat dit aanbod conform de <a href="modal.voorwaarden.php" class="domodal">gebruiksvoorwaarden</a> is. </label>
                                </div>
                                <div class="form-group col-lg-1">
                                    <input type="submit" name="owaesadd" id="owaesadd" class="owaesadd auto border btn btn-default pull-right" value="opslaan" />
                                </div>
                            </div>
                          
                            <div class="form-group"> 
                                <div class="col-lg-12"> 
                                    <a href="#tijdlocatie" class="tabchange">vorige</a> 
                                </div>
                            </div>
                           
                      </div>
                    </div>

                        <ul id="addfouten">
                        
                        </ul>
                    </form>
            	</div> 
			<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
