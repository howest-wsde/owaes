<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$oPage->addJS("http://code.jquery.com/ui/1.10.3/jquery-ui.js");
	$oPage->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true");
	$oPage->addJS("script/mugifly-jquery-simple-datetimepicker-702f729/jquery.simple-dtpicker.js"); 
	$oPage->addCSS("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"); 
	$oPage->addCSS("script/mugifly-jquery-simple-datetimepicker-702f729/jquery.simple-dtpicker.css"); 
	 
	
	$iID = isset($_GET["edit"])?intval($_GET["edit"]):0;
	$oOwaesItem = new owaesitem($iID);
	
	if ($oOwaesItem->author()->id() != me()) {
		if (!admin()) {
			$oSecurity->doLogout(); 
			exit(); 
		}
	}
	
	if ($oOwaesItem->id() != 0) {
		$strType = $oOwaesItem->type()->key();  
	} else {
		$strType = (isset($_GET["t"]) ? $_GET["t"] : ""); 
	} 
	 
	if (isset($_POST["owaesadd"])) {
		//$oOwaesItem = new owaesitem($iID); 
		// $oOwaesItem->author(me()); 
		
		$iGroup = intval($_POST["group"]); 
		if ($iGroup != 0){
			$oGroup = new group($iGroup); 
			if ($oGroup->userrights()->owaesadd()) $oOwaesItem->group($iGroup);
		} else {
			$oOwaesItem->group(0); 
		} 
		
		$oOwaesItem->title($_POST["title"]); 
		$oOwaesItem->body($_POST["description"]); 
		switch($_POST["location"]) {
			case "free": 
				$oOwaesItem->location("", 0, 0); 
				break; 
			default: // "fixed"	
				$oOwaesItem->location($_POST["locationfixed"], $_POST["locationlat"], $_POST["locationlong"]); 
		}
		$oOwaesItem->timingtype($_POST["timing"]); 
		
		foreach ($oOwaesItem->data() as $iDate) {
			$oOwaesItem->removeTimingStart($iDate);
		} 
		switch ($_POST["timing"]) {
			case "free": 
			case "tbc": 
				break;
			default: 
				foreach ($_POST["timingstart"] as $strDate) {
					$oOwaesItem->addTimingStart(ddmmyyyyTOdate($strDate)); 
				}  
		} 
		 
		foreach ($oOwaesItem->getTags() as $strTag) {
			$oOwaesItem->removeTag($strTag);
		} 
		if (isset($_POST["tag"])) foreach ($_POST["tag"] as $strTag) { 
			$oOwaesItem->addTag($strTag);  
		}   
		
		$oOwaesItem->timing($_POST["timingtime"]); 
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
			
		$oOwaesItem->update();   
		//switch($oOwaesItem->task())  {
		//	case TRUE: 
				header("Location: index.php?t=" . $oOwaesItem->type()->key());  
				exit(); 
		//		break; 	
		//	case FALSE:  
		//		header("Location: index.php?t=market");  
		//		exit(); 
		//		break; 	
		//} 
		
	} 
	  
	list($iLat, $iLong) = $oOwaesItem->LatLong(); 

	$oPage->tab("market.$strType");  
	 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
        <script>
			var arDev = Array(); 
			var arSliders = {}; 
			$(function() { 
				$('.datetimerpicker').appendDtpicker({
					"inline": false,
					"closeOnSelected": true, 
					"dateFormat": "DD/MM/YYYY hh:mm", 
					"locale": "nl",  
				}); 
				 
				// setup master volume 
				$("input.development").attr("type", "hidden").each(function(){  
					strDev = $(this).attr("name"); 
					iVal = $(this).attr("value"); 
					$(this).attr("value", iVal); 
					// console.log($(this).attr("value")); 
					arDev[arDev.length] = strDev; 
					$(this).after(
						$("<div />").addClass("slidervalue").attr("rel", strDev).html(iVal + "%")
					).after(
						$("<div />").addClass("slider").addClass("development").attr("rel", strDev).slider({
							min: 0,
							max: 100,
							step: 25,  
							value: iVal,
							orientation: "horizontal", 
							slide: function( event, ui ) {
								strDev = ui.handle.offsetParent.attributes["rel"].value; 
								iVal = ui.value; 
								$("input[name=" + strDev + "]").attr("value", iVal); 
								$(".slidervalue[rel=" + strDev + "]").html((iVal) + "%"); 
								var index = arDev.indexOf(strDev);
								arDev.splice(index, 1);
								arDev[arDev.length] = strDev; 
								iTotaal = 0; 
								for (i=0; i<arDev.length; i++) iTotaal += parseInt($("input[name=" + arDev[i] + "]").attr("value"));
								for (i=0; i<arDev.length; i++) {
									iVal = parseInt($("input[name=" + arDev[i] + "]").attr("value") ); 
									if ((iTotaal > 100)&&(iVal > 0)) {
										iAdd = iTotaal - 100; 
										if (iAdd > iVal) iAdd = iVal; 
										iTotaal -= iAdd; 
										iVal -= iAdd; 
										$(".slidervalue[rel=" + arDev[i] + "]").html((iVal) + "%"); 
										$("input[name=" + arDev[i] + "]").attr("value", iVal); 
										arSliders[arDev[i]].slider( "value", iVal);
									}
									if ((iTotaal < 100)&&(iVal < 100)) {
										iAdd = 100 - iTotaal; 
										if (iAdd > 100-iVal) iAdd = 100-iVal; 
										iTotaal += iAdd; 
										iVal += iAdd; 
										$(".slidervalue[rel=" + arDev[i] + "]").html((iVal) + "%"); 
										$("input[name=" + arDev[i] + "]").attr("value", iVal);
										arSliders[arDev[i]].slider( "value", iVal);
									}
								} 
							}
						}).each(function(){
							arSliders[strDev] = $(this);  
						})
					)
				}); 
				arDev.reverse();  
				
				
				$("input#timingfreeslide").change(function(){
					iVal = parseInt($(this).attr("value"));
					$("div#timerslide").slider({ 
						value: iVal,
					});
					if (!$("input#creditsfield").hasClass("changed")) {
						iMinuten = $(this).attr("value") * 60; 
						$("input#creditsfield").attr("value", iMinuten); 
						$("div#creditsslide").slider({ 
							value: iMinuten,
						});
					}
				}).each(function(){   
					iVal = parseInt($(this).attr("value"));  
					iMin = parseInt($(this).attr("min")); 
					iMax = parseInt($(this).attr("max"));  
					$(this).after(
						$("<div />").addClass("sliderref").html("uur") // .addClass("slidervalue").attr("rel", "timing")
					).before(
						$("<div />").addClass("slider").attr("rel", "timing").attr("id", "timerslide").slider({
							min: iMin, 
							max: iMax, 
							value: iVal, 
							orientation: "horizontal", 
							slide: function( event, ui ) { 
								iVal = ui.value; 
								$("input#timingfreeslide").attr("value", iVal).change(); 
								//$(".slidervalue[rel=timing]").html((iVal==0)?"onbepaald":(iVal + " uur"));  
							}
						}) 
					)
				});   
				
				$("input#creditsfield").change(function(){
					iVal = parseInt($(this).attr("value"));
					$("div#creditsslide").slider({ 
						value: iVal,
					});
				}).each(function(){   
					iVal = parseInt($(this).attr("value"));  
					iMin = parseInt($(this).attr("min")); 
					iMax = parseInt($(this).attr("max")); 
					$(this).after(
						$("<div />").addClass("sliderref credits").html("credits") // .addClass("slidervalue").addClass("creditsslide").attr("rel", "credits").html((iVal==0)?"overeen te komen":iVal)
					).before(
						$("<div />").addClass("slider").attr("id", "creditsslide").addClass("creditsslide").attr("rel", "credits").slider({
							min: iMin,
							max: iMax, 
							value: iVal,
							orientation: "horizontal", 
							slide: function( event, ui ) { 
								iVal = ui.value; 
								$("input#creditsfield").attr("value", iVal).addClass("changed").change(); 
								//$(".slidervalue[rel=credits]").html((iVal==0)?"overeen te komen":iVal);  
							}
						}) 
					)
				});   
				
				
				
				$("a.addstarttime").click(function(){
					oEl = $("dd.timingfixed:last");
					oNew = $("<dd />").attr("class", oEl.attr("class")).append(oEl.html());  
					oNew.find("a").html("tijdstip verwijderen").click(function(){
						oDD = $(this).parentsUntil("dl"); 
						oDD.remove(); 	
					});
					// oNew.find("input").attr("value", oEl.find("input").attr("value", ));
					oNew.find('.datetimerpicker').appendDtpicker({
						"inline": false,
						"closeOnSelected": true, 
						"dateFormat": "DD/MM/YYYY hh:mm", 
						"locale": "nl",  
					});  
					oEl.after(oNew);
				})
				$("a.delstarttime").click(function(){
					oDD = $(this).parentsUntil("dl"); 
					oDD.remove(); 	
				});
			});
			
			// GOOGLE MAP
			var map; 
			<?
				if ($iLat + $iLong != 0) {
					echo ("var startpos = new google.maps.LatLng(" . ($iLat) . ", " . ($iLong) . ");");  
				} else {
					echo ("var startpos = new google.maps.LatLng(" . settings("geo", "latitude") . ", " . settings("geo", "longtitude") . ");"); 
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
				<?
					if ($iLat + $iLong != 0) {
						echo (" oPos = new google.maps.LatLng(" . $iLat . ", " . $iLong . "); 
						setMarker(oPos); "); 
					} 
				?> 
			}
			google.maps.event.addDomListener(window, 'load', initialize);
			

			// MAPS CHANGE
			var iTimerZoek = 0; 
			$(document).ready(function() {
				$("input#location").keyup(function() { 
					clearTimeout(iTimerZoek);
					iTimerZoek = setTimeout("geozoek();", 1000); 
				})
				//$("input#location").change(function() { 
				$("input#location").bind("change click", function() {
					geozoek(); 
				}) 
			})
			
			
			function geozoek() {
				clearTimeout(iTimerZoek); 
				strVal = $("input#location").attr("value"); 
				if (strVal == "") {
					$("input#locationlong").attr("value", 0);
					$("input#locationlat").attr("value", 0);
					deleteMarker(); 
				} else {
					$.ajax({
						type: "POST",
						url: "details.location.php", 
						data: "search=" + escape(strVal), 
						success: function(strResult){ 
							arLoc = strResult.split("|"); 
							if (arLoc.length == 2) {
								if (($("input#locationlong").attr("value") != arLoc[0])||($("input#locationlat").attr("value") != arLoc[1])){
									$("input#locationlong").attr("value", arLoc[0]);
									$("input#locationlat").attr("value", arLoc[1]);
									setMarker(new google.maps.LatLng(arLoc[1], arLoc[0]));   
								}
							}  else deleteMarker(); 
						}
					}); 
				}
			}
			
			function setMarker(oPos) {
				if (marker) marker.setMap(null);
				marker = new google.maps.Marker({
					map:map,
					draggable: false, 
					animation: google.maps.Animation.DROP, 
					position: oPos
				  });	
				  map.panTo(oPos); 
				  map.setZoom(14); 
			}
			
			function deleteMarker() {
					if (marker) marker.setMap(null);
					map.setZoom(12); 
			}
			
		</script> 
    </head>
    <body id="owaesadd">               
        <? echo $oPage->startTabs(); ?> 
    	<div class="body content content-market content-market-add container">
        	
            	<div class="row">
					<? /*echo $oSecurity->me()->html("templates/leftuserprofile.html"); */
                    echo $oSecurity->me()->html("templates/user.html");
                    ?>
                </div>
                <div class="container sidecenterright"> 
                
                <!-- <div class="todo">
                <strong>TODO:</strong>
                <ul>
                <li>Alles wordt momenteel opgeslaan als "Delen"</li>
                </ul>
                </div> -->
                
                <form method="post" class="form-horizontal" id="frmowaesadd" name="frmowaesadd">
                	<?  
						$arOwaesTypes = owaesType()->getAllTypes();
						
						if (isset($arOwaesTypes[$strType])) {
							echo ("<legend class=\"aanbod\">Aanbod toevoegen: <strong>" . $arOwaesTypes[$strType] . "</strong> <small>(credits " . ((owaesType($strType)->direction()==DIRECTION_EARN) ? "verdienen" : "uitgeven") . ")</small></legend>"); 
						} else {
							echo ("<legend class=\"aanbod\">Aanbod toevoegen</legend>"); 
						}  
					?>
                          
                        	
                            <dl id="algemeen">
                            	<? 
									$arGroups = $oOwaesItem->author()->groups();  
									$arAddGroups = array(); 
									foreach ($arGroups as $oGroup) { 
										if ($oGroup->userrights()->owaesadd()) $arAddGroups[] = $oGroup; 
									}
									if (count($arAddGroups) > 0) {
										echo ("<dt><label for=\"group\">Poster</label></dt>"); 
										echo ("<dd><select name=\"group\" id=\"group\" class=\"required\">"); 
										echo ("<option value=\"0\" style=\"border-bottom: 1px dotted #000; \">" . $oOwaesItem->author()->getName() . "</option>");  
										foreach ($arAddGroups as $oGroup) echo ("<option value=\"" . $oGroup->id() . "\">" . $oGroup->naam() . "</option>"); 
										echo ("</select></dd>");  
									} else {
										echo ("<input type=\"hidden\" name=\"group\" value=\"0\" />"); 	
									}
									 
									
								?>  
                                <div class="form-group">
                                    <div class="col-lg-10">
                                        <input type="text" name="title" id="title" class="required form-control" placeholder="Titel voor uw aanbod" value="<? echo inputfield($oOwaesItem->title()); ?>" />
                                    </div>
                                    <div class="col-lg-2">
                                        <!-- <select class="form-control"> -->
                                            <?
                                            switch($strType) {
										        case "work": 
											        echo ("<input type='hidden' name='task' value='1' />");
											        break; 
										        case "market": 
											        echo ("<input type='hidden' name='task' value='0' />"); 
											        break; 
										        default: 
											        ?>
                                                        <!-- <dt><label for="type">Type</label></dt> -->
                                                        <dd><select class="form-control aanbod" name="type">
                                                	        <?
                                                                foreach($arOwaesTypes as $strKey=>$strTitle) {
                                                                    $strSelected = ($strType==$strKey) ? "selected=\"selected\"" : ""; 
                                                                    echo "<option value='$strKey' $strSelected>"; 
                                                                    echo $strTitle;
                                                                    //echo (owaestype($strKey)->direction() == DIRECTION_EARN) ? ": dit zal me credits opleveren" : ": dit zal me credits kosten"; 
                                                                    echo "</option>"; 	
                                                                }
													        ?> 
                                                        </select></dd>
											        <?
									        }
                                            ?>
                                        <!-- </select> -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description" class="col-lg-12">Omschrijving</label>
                                    <div class="col-lg-12">
                                        <textarea name="description" id="description" class="required form-control"><? echo textarea($oOwaesItem->body()); ?></textarea>
                                    </div>
                                </div>
                                
<!-- 
                                <dt><label for="body">Omschrijving</label></dt>
                                <dd><textarea name="description" id="description" class="required"><? echo textarea($oOwaesItem->body()); ?></textarea></dd>
     -->
                                <div class="form-group">
                                <label for="types" class="col-lg-12">Kernwoorden</label>
                                
                                <script>
									$(document).ready(function() {
										$("input.tag").keyup(function(){
											strVal = $(this).val(); 
											arVal = strVal.split(" ");  
											while (arVal.length > 1) {
												strVal = arVal.shift(); 
												addTag(strVal); 
											} 
											strVal = arVal.join(""); 
											$(this).val(strVal);
											
											if (strVal != "") { 
												if ($("dd#tags ul.tags").length == 0) $("dd#tags").append(
													$("<ul />").addClass("tags")
												);
												// $("dd#tags ul.tags").load();   
												$.getJSON( "tags.php", { s: strVal } ).done(function( arTags ) {
													$("dd#tags ul.tags li").remove(); 
													for (i=0; i<=arTags.length; i++){
														strTag = arTags[i];
														$("dd#tags ul.tags").append( 
															$("<li />").text(strTag).attr("rel", strTag).click(function(){
																$("input.tag").val("");
																addTag($(this).attr("rel")); 
																$("dd#tags ul.tags li").remove(); 
															})
														);
													}  
												});
											} else $("dd#tags ul.tags li").remove(); 
										}).change(function(){ 
											setTimeout(function() {  
												strVal = $("input.tag").val(); 
												arVal = strVal.split(" ");  
												while (arVal.length > 0) {
													strVal = arVal.shift(); 
													addTag(strVal); 
												} 
												$("input.tag").val("");
											}, 500);
										})
										$("dd#tags span.tag a").click(function(){
											$("#" + $(this).attr("rel")).remove(); 
											return false; 
										});
									})
									function addTag(strTag) {
										if (strTag != "") {
											strKey = "tag_" + ($("dd#tags span.tag").length+1) + "_" + Math.floor(1000*Math.random()); 
											$("input.tag").before(
												$("<span />").addClass("tag").attr("id", strKey).append(
													$("<span>").text(strTag)
												).append(
													$("<a />").attr("title", "verwijderen").text("x").attr("href", "#").attr("rel", strKey).click(function(){
														$("#" + $(this).attr("rel")).remove(); 
														return false; 
													})
												).append(
													$("<input />").attr("name", "tag[]").attr("type", "hidden").val(strTag)
												)
											)
										}
										$("input.tag").focus(); 
									}
								</script> 
                                <div class="col-lg-12">
                                <dd id="tags"><?
									$iTagCount = 0;  
                                	foreach ($oOwaesItem->getTags() as $strTag) {
										$strKey = "tag" . ++$iTagCount; 
										echo ("<span class=\"tag\" id=\"$strKey\"><span>$strTag</span><a title=\"verwijderen\" href=\"#\" rel=\"$strKey\">x</a><input type=\"hidden\" name=\"tag[]\" value=\"$strTag\"></span>"); 	
									}
								?><input type="text" name="tag[]" id="tag" class="tag" /></dd>
                                </div>
                                </div>
                            </dl>
                             
                            <legend>Tijd en locatie</legend>
                            
                            <dl id="plaatsentijd"> 
                                <div class="row">
                                <div class="col-lg-6">
                                <h4>Plaats</h4>
                                <div class="radio">
                                  <label>
                                    <input type="radio" name="location" id="locationfree" value="free" class="required auto" <? if ($oOwaesItem->location()=="") echo ('checked="checked"'); ?> />
                                    Vrij te kiezen
                                  </label>
                                </div>
                                <div class="radio">
                                  <label>
                                    <input type="radio" name="location" id="locationfixed" value="fixed" class="required auto" <? if ($oOwaesItem->location()!="") echo ('checked="checked"'); ?> />
                                    Op locatie
                                  </label>
                                </div>
                                <dd class="locationfixed">
                                    <input type="text" name="locationfixed" class="locationfixed_required form-control" id="location" value="<? echo inputfield($oOwaesItem->location()); ?>" />
                                	<input type="hidden" name="locationlat" id="locationlat" value="<? echo $iLat; ?>" />
                                	<input type="hidden" name="locationlong" id="locationlong" value="<? echo $iLong; ?>" />
                                </dd>
                                <dd class="locationfixed"><div id="map-canvas" style="height: 300px; "></div></dd> 
                                
                                </div>
                                <div class="col-lg-6">
                                <h4>Tijdstip</h4>
                                
                                <div class="radio">
                                  <label>
                                  <input type="radio" name="timing" id="timingfixed" value="fixed" class="required auto" <? if (count($oOwaesItem->data())>0) echo ('checked="checked"'); ?> />
                                  Vast
                                  </label>
                                </div> 
                                <? foreach ($oOwaesItem->data() as $iNr => $iDate) { ?>
                                    <dd class="timingfixed">
                                        <input type="datetime" class="datetimerpicker timingfixed_required" name="timingstart[]" value="<? echo str_date($iDate, "Y/m/d G:i"); ?>" />
                                        <? if ($iNr==0) { ?>
                                       	 <a href="#addstarttime" class="addstarttime">nog een tijdstip toevoegen</a>
                                        <? } else { ?>
                                        	<a href="#delstarttime" class="delstarttime">tijdstip verwijderen</a>
                                        <? } ?>
                                    </dd>
								<? } ?>
                                <? if (count($oOwaesItem->data()) == 0) { ?>
                                    <dd class="timingfixed">
                                        <input type="datetime" class="datetimerpicker timingfixed_required" name="timingstart[]" value="<? // echo str_date((owaesTime()-(owaesTime()%(30*60))+(30*60)), "Y/m/d G:i"); ?>" />
                                    </dd>
                                <? } else { ?> 
								<? } ?> 
                                
                                <div class="radio">
                                  <label>
                                  <input type="radio" name="timing" id="timingfree" value="free" class="required auto" <? if (count($oOwaesItem->data())==0) echo ('checked="checked"'); ?> />
                                  Vrij te kiezen
                                  </label>
                                </div> 
                                <div class="radio">
                                  <label>
                                  <input type="radio" name="timing" id="timingtbc" value="tbc" class="required auto" />
                                  Nog te bepalen
                                  </label>
                                </div> 
                                <h4>Tijdsduur</h4>
                                <input type="text" min="0" max="10" class="required auto border" name="timingtime" id="timingfreeslide" value="<? echo $oOwaesItem->timing(); ?>" />
                                
                                </div>
                                
                                
                                
                                </div>
                                
                                
                                
                               
                                
                                
                                
                           </dl>
                            
                           <legend>Compensatie</legend>
                           <dl id="compensatie">
                                 
                                <br />
                                
                                <dt><label for="credits">Credits</label></dt> 
                                <dd><input type="text" min="0" max="1000" name="credits" id="creditsfield" class="auto border" value="<? echo $oOwaesItem->credits(); ?>" /></dd>
                                
                                <br />
                                
								<div id="master" style="width: 260px; margin: 15px;"></div>
                                
                                <dt><label for="physical">Fysiek</label></dt> 
                                <dd><input type="range" min="0" max="100" name="physical" id="physicalslide" value="<? echo $oOwaesItem->physical(); ?>" class="development" /></dd>
                                <dt><label for="mental">Kennis</label></dt> 
                                <dd><input type="range" min="0" max="100" name="mental" id="mentalslide" value="<? echo $oOwaesItem->mental(); ?>" class="development"  /></dd>
                                <dt><label for="emotional">Emotioneel</label></dt> 
                                <dd><input type="range" min="0" max="100" name="emotional" id="emotionalslide" value="<? echo $oOwaesItem->emotional(); ?>" class="development"  /></dd>
                                <dt><label for="social">Sociaal</label></dt> 
                                <dd><input type="range" min="0" max="100" name="social" id="socialslide" value="<? echo $oOwaesItem->social(); ?>" class="development"  /></dd>
                            </dl>
                            
                            <div class="form-group">
                            <input type="submit" name="owaesadd" id="owaesadd" class="auto border btn btn-default" value="opslaan" />
                            </div>
                            
                        
                        <ul id="addfouten">
                        
                        </ul>
                    </form>
            	</div> 
			<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
