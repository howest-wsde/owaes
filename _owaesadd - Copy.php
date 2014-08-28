<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oPage->addJS("http://code.jquery.com/ui/1.10.3/jquery-ui.js");
	$oPage->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true");
	$oPage->addJS("script/mugifly-jquery-simple-datetimepicker-702f729/jquery.simple-dtpicker.js");
	$oPage->addCSS("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"); 
	$oPage->addCSS("script/mugifly-jquery-simple-datetimepicker-702f729/jquery.simple-dtpicker.css"); 
	 
	
	if (isset($_POST["owaesadd"])) {
		$oOwaesItem = new owaesitem(); 
			$oOwaesItem->author($oSecurity->getUserID()); 
			$oOwaesItem->title($_POST["title"]); 
			$oOwaesItem->body($_POST["description"]); 
			$oOwaesItem->location($_POST["location"], $_POST["locationlat"], $_POST["locationlong"]); 
			switch ($_POST["timing"]) {
				case "free": 
					break;
				default: 
					// var_dump($_POST["timingstart"]);  
					foreach ($_POST["timingstart"] as $strDate) {
						$oOwaesItem->addTimingStart(ddmmyyyyTOdate($strDate)); 
						// echo "<br>" . $strDate . ": " . ddmmyyyyTOdate($strDate) . "<br>";
					}  
			} 
			$oOwaesItem->timing($_POST["timing"]); 
			$oOwaesItem->physical($_POST["physical"]); 
			$oOwaesItem->mental($_POST["mental"]); 
			$oOwaesItem->emotional($_POST["emotional"]); 
			$oOwaesItem->social($_POST["social"]); 
			$oOwaesItem->credits($_POST["credits"]); 
			$oOwaesItem->task($_POST["task"]==1); 
		$oOwaesItem->update();  
		/*
		echo ("<textarea>"); 
		var_dump($oOwaesItem); 
		echo "\n"; 
		var_dump($_POST);  
		echo ("<textarea>"); 
		exit(); 
		*/
		header('Location: ' . $oOwaesItem->getLink());  
	}
	
	$strType = (isset($_GET["t"]) ? $_GET["t"] : ""); 
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
					iVal = $(this).val(); 
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
								$("input[name=" + strDev + "]").val(iVal); 
								$(".slidervalue[rel=" + strDev + "]").html((iVal) + "%"); 
								var index = arDev.indexOf(strDev);
								arDev.splice(index, 1);
								arDev[arDev.length] = strDev; 
								iTotaal = 0; 
								for (i=0; i<arDev.length; i++) iTotaal += parseInt($("input[name=" + arDev[i] + "]").val());
								for (i=0; i<arDev.length; i++) {
									iVal = parseInt($("input[name=" + arDev[i] + "]").val() ); 
									if ((iTotaal > 100)&&(iVal > 0)) {
										iAdd = iTotaal - 100; 
										if (iAdd > iVal) iAdd = iVal; 
										iTotaal -= iAdd; 
										iVal -= iAdd; 
										$(".slidervalue[rel=" + arDev[i] + "]").html((iVal) + "%"); 
										$("input[name=" + arDev[i] + "]").val(iVal); 
										arSliders[arDev[i]].slider( "value", iVal);
									}
									if ((iTotaal < 100)&&(iVal < 100)) {
										iAdd = 100 - iTotaal; 
										if (iAdd > 100-iVal) iAdd = 100-iVal; 
										iTotaal += iAdd; 
										iVal += iAdd; 
										$(".slidervalue[rel=" + arDev[i] + "]").html((iVal) + "%"); 
										$("input[name=" + arDev[i] + "]").val(iVal);
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
					iVal = parseInt($(this).val());
					$("div#timerslide").slider({ 
						value: iVal,
					});
					if (!$("input#creditsfield").hasClass("changed")) {
						iMinuten = $(this).val() * 60; 
						$("input#creditsfield").val(iMinuten); 
						$("div#creditsslide").slider({ 
							value: iMinuten,
						});
					}
				}).each(function(){   
					iVal = parseInt($(this).val());  
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
								$("input#timingfreeslide").val(iVal).change(); 
								//$(".slidervalue[rel=timing]").html((iVal==0)?"onbepaald":(iVal + " uur"));  
							}
						}) 
					)
				});   
				
				$("input#creditsfield").change(function(){
					iVal = parseInt($(this).val());
					$("div#creditsslide").slider({ 
						value: iVal,
					});
				}).each(function(){   
					iVal = parseInt($(this).val());  
					iMin = parseInt($(this).attr("min")); 
					iMax = parseInt($(this).attr("max")); 
					$(this).after(
						$("<div />").addClass("sliderref").html("credits") // .addClass("slidervalue").addClass("creditsslide").attr("rel", "credits").html((iVal==0)?"overeen te komen":iVal)
					).before(
						$("<div />").addClass("slider").attr("id", "creditsslide").addClass("creditsslide").attr("rel", "credits").slider({
							min: iMin,
							max: iMax, 
							value: iVal,
							orientation: "horizontal", 
							slide: function( event, ui ) { 
								iVal = ui.value; 
								$("input#creditsfield").val(iVal).addClass("changed").change(); 
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
						console.log(oDD); 
						oDD.remove(); 	
					});
					// oNew.find("input").val(oEl.find("input").val());
					oNew.find('.datetimerpicker').appendDtpicker({
						"inline": false,
						"closeOnSelected": true, 
						"dateFormat": "DD/MM/YYYY hh:mm", 
						"locale": "nl",  
					});  
					oEl.after(oNew);
				})
				
				 
				
				
			});
			
			
			
			// GOOGLE MAP
			var map;
			var startpos = new google.maps.LatLng(50.8305300, 3.2644600);
			var marker; 
			function initialize() {
				var mapOptions = {
					zoom: 12,
					center: startpos,
					disableDefaultUI: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
			}
			google.maps.event.addDomListener(window, 'load', initialize);
			

			// MAPS CHANGE
			var iTimerZoek = 0; 
			$(document).ready(function() {
				$("input#location").keyup(function() { 
					clearTimeout(iTimerZoek);
					iTimerZoek = setTimeout("geozoek();", 1000); 
				})
				$("input#location").change(function() { 
					geozoek(); 
				})
			})
			
			
			function geozoek() {
				clearTimeout(iTimerZoek); 
				strVal = $("input#location").val(); 
				if (strVal == "") {
					$("input#locationlong").val(0);
					$("input#locationlat").val(0);
					deleteMarker(); 
				} else {
					$.ajax({
						type: "POST",
						url: "details.location.php", 
						data: "search=" + escape(strVal), 
						success: function(strResult){ 
							arLoc = strResult.split("|"); 
							if (arLoc.length == 2) {
								if (($("input#locationlong").val() != arLoc[0])||($("input#locationlat").val() != arLoc[1])){
									$("input#locationlong").val(arLoc[0]);
									$("input#locationlat").val(arLoc[1]);
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
				  map.panTo(marker.getPosition()); 
				  map.setZoom(14); 
			}
			
			function deleteMarker() {
					if (marker) marker.setMap(null);
					map.setZoom(12); 
			}
			
 

		</script>
    </head>
    <body id="owaesadd">
    	<div class="header">
        	<a href="main.php"><img src="img/logo.png" /></a>
        </div>
    	<div class="body">
        	<? echo $oPage->startTabs(); ?>
                <div class="sideleft">
               		<? echo $oSecurity->me()->html("templates/leftuserprofile.html"); ?>
                </div>
                <div class="sidecenterright"> 
                   	<h1>Aanbod toevoegen</h1>
                	<div class="box">
                        <form method="post">  
                            <dl class="steps">
                                <dt><label for="title">Type</label></dt>
                                <dd><input type="radio" name="task" id="task0" value="0" class="required" <? if ($strType=="work") echo "checked=\"checked\""; ?> /> <label for="task0">ik bied iets aan</label></dd>
                                <dd><input type="radio" name="task" id="task1" value="1" class="required" <? if ($strType=="market") echo "checked=\"checked\""; ?>/> <label for="task1">ik vraag iets / iemand</label></dd>
                                
                                
                                <dt><label for="title">Titel</label></dt>
                                <dd><input type="text" name="title" id="title" class="required" /></dd>
                                
                                <dt><label for="location">Plaats</label></dt>
                                <dd><input type="radio" name="location" id="locationfixed" value="fixed" class="required" /> <label for="locationfixed">Op locatie</label></dd>
                                <dd class="locationfixed">
                                    <input type="text" name="locationfixed" class="locationfixed_required" id="location" value="" />
                                	<input type="hidden" name="locationlat" id="locationlat" value="0" />
                                	<input type="hidden" name="locationlong" id="locationlong" value="0" />
                                </dd>
                                <dd class="locationfixed"><div id="map-canvas" style="height: 200px; "></div></dd> 
                                
                                <dd><input type="radio" name="location" id="locationfree" value="free" class="required" /> <label for="locationfree">Vrij te kiezen</label></dd>
                                

                                
                                <dt><label for="timing">Tijdstip</label></dt>
                                <dd><input type="radio" name="timing" id="timingfixed" value="fixed" class="required" /> <label for="timingfixed">Vast</label></dd>
                                <dd class="timingfixed">
                                	<input type="datetime" class="datetimerpicker timingfixed_required" name="timingstart[]" value="<? echo str_date((owaesTime()-(owaesTime()%(30*60))+(30*60)), "Y/m/d G:i"); ?>" />
                                	<a href="#addstarttime" class="addstarttime">nog een tijdstip toevoegen</a>
                                </dd>
                                <dd><input type="radio" name="timing" id="timingfree" value="free" class="required" /> <label for="timingfree">Vrij te kiezen</label></dd>
                                
                                <dt><label for="timing">Tijdsduur</label></dt>
                                <dd><input type="text" min="0" max="10" class="required" name="timing" id="timingfreeslide" value="0" /></dd>
                                
                                <dt><label for="body">Omschrijving</label></dt>
                                <dd><textarea name="description" id="description" class="required"></textarea></dd>
                                
                                <dt><label for="types">Domeinen</label></dt>
                                <dd><select name="types" id="types" multiple="multiple" class="required">  
                                    <option value="">Administratie en Economie </option>
                                    <option value="">Creatief </option>
                                    <option value="">Dierenzorg  </option>
                                    <option value="">Eten en drinken</option>
                                    <option value="">Gezondheid en Welzijn </option>
                                    <option value="">Huis</option>
                                    <option value="">Informatica, PC en Internet </option>
                                    <option value="">Tuin </option>
                                    <option value="">Logistiek en Transport  </option>
                                    <option value="">Maatschappij  </option>
                                    <option value="">Muziek </option>
                                    <option value="">Onderwijs </option>
                                    <option value="">Uiterlijke verzorging en Styling  </option>
                                    <option value="">Andere</option> 
                                </select></dd>
                                 
                                <br />
                                
                                <dt><label for="credits">Credits</label></dt> 
                                <dd><input type="text" min="0" max="1000" name="credits" id="creditsfield" value="0" /></dd>
                                
                                <br />
                                
								<div id="master" style="width: 260px; margin: 15px;"></div>
                                
                                <dt><label for="physical">Fysiek</label></dt> 
                                <dd><input type="range" min="0" max="100" name="physical" id="physicalslide" value="25" class="development" /></dd>
                                <dt><label for="mental">Kennis</label></dt> 
                                <dd><input type="range" min="0" max="100" name="mental" id="mentalslide" value="25" class="development"  /></dd>
                                <dt><label for="emotional">Emotioneel</label></dt> 
                                <dd><input type="range" min="0" max="100" name="emotional" id="emotionalslide" value="25" class="development"  /></dd>
                                <dt><label for="social">Sociaal</label></dt> 
                                <dd><input type="range" min="0" max="100" name="social" id="socialslide" value="25" class="development"  /></dd>
        
        
                            </dl>
                            <input type="submit" name="owaesadd" id="owaesadd" value="opslaan" />
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
