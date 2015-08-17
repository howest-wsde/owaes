<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$oPage->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true");
	$oPage->addJS("script/masonry.js"); 
	
	
	$oPage->addJS("script/flot/jquery.flot.js"); 
	$oPage->addJS("script/flot/jquery.flot.time.js"); 
    $oPage->addJS("script/flot/jquery.flot.symbol.js");
    
    $oPage->tab("home");
	
	$oMe = user(me());  
	
	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload");     
	$oExperience->add(1);  
	 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
        <script>
		
					
			$(document).ready(function(e) {
				$('div.masonry').masonry({
				  itemSelector: '.masonrybox'
				});
            });

			// GOOGLE MAP
			var map; 
			<?php
				list($iLatMe, $iLongMe) = user(me())->LatLong();  
				if ($iLatMe * $iLongMe != 0) {
					echo ("var startpos = new google.maps.LatLng($iLatMe, $iLongMe);"); 	
				} else {
					echo ("var startpos = new google.maps.LatLng(" . settings("geo", "latitude") . ", " . settings("geo", "longitude") . ");"); 	
				}
			?> 
			
			var marker, arMapBullets = Array(), iPrevZoom = 12; 
			function initialize() {
				var mapOptions = {
					zoom: 12,
					center: startpos,
					disableDefaultUI: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				
				google.maps.event.addDomListener(map,'zoom_changed', function() { 
					iZoom = map.getZoom(); 
					if ((iZoom > 13) != (iPrevZoom > 13)){
						for (i=0; i<arMapBullets.length; i++){
							arMapBullets[i].setMap((iZoom > 13) ? null : map);
						}
					}
					iPrevZoom = iZoom; 
				}); 

				<?php  
					
					$arLocations = json("cache/locations.json"); 
					if (!isset($arLocations["date"]) || ($arLocations["date"]<owaestime()-12*60*60)) {
						$arLocations["date"] = owaestime();  
						$arLocations["coords"] = array(); 
						$oUsers = new userlist();  
						foreach ($oUsers->getList() as $oUser) {
							$arLL = $oUser->LatLong(); 
							if ($arLL[0] * $arLL[1] != 0) $arLocations["coords"][] = $arLL; 
						} 
						
						json("cache/locations.json", $arLocations); 
						
					}
					echo "arBullets = " . json_encode($arLocations["coords"]) . "; ";   
					 
				?>
				
				for (i=0; i<arBullets.length; i++){ 
					arMapBullets[arMapBullets.length] = new google.maps.Marker({
						map:map, 
						position: new google.maps.LatLng(arBullets[i][0], arBullets[i][1]),  
						icon: 'img/bullet.png', 
					});  
				}	
				<?
					
					$oOwaesList = new owaeslist(); 
					$oOwaesList->filterByState(STATE_RECRUTE);  
					$oOwaesList->filterPassedDate(owaesTime()); 
					$oOwaesList->enkalkuli("social", $oMe->social());
					$oOwaesList->enkalkuli("physical", $oMe->physical());
					$oOwaesList->enkalkuli("mental", $oMe->mental());
					$oOwaesList->enkalkuli("emotional", $oMe->emotional());
					// $oOwaesList->enkalkuli("location", $oMe->emotional());

					foreach ($oOwaesList->getList() as $oItem) {  
						list($iLat, $iLong) = $oItem->LatLong();  
						if ($iLat * $iLong != 0) {
							echo ("addMarker(new google.maps.LatLng($iLat, $iLong), '" . $oItem->type()->key() . "', '" . javascriptSafe(popup($oItem)) . "', " . $oItem->id() . "); \n"); 
						} 
					}
                    
                    function popup($item){
                        $title = "<h2><a href=".$item->getLink().">".$item->title()."</a></h2>";
                        $indicatoren = '<div class="development">'.$item->developmentBoxes().'</div>';
                       $img = '<img src="'.$item->author()->getImage("85", FALSE).'" />';
                       
                       $popup = '<div class="mapPopup"><div class="row"><div class="col-md-4"> '.$img.'</div>';
                       $popup .='<div class="col-md-8"><div class="row">'.$title.' </div> <div class="row">'.$indicatoren.'</div></div>';
                       
                       $popup .="</div></div>";
                       
                        return $popup;
                    }
					
					
 
				?>	
 
			}
			google.maps.event.addDomListener(window, 'load', initialize);
			
			function addMarker(oPos, strType, strInfo, iID) { 
				marker = new google.maps.Marker({
					map:map,
					draggable: false, 
					position: oPos, 
					icon: 'img/marker-' + strType + '.png', 
				});  
				google.maps.event.addListener(map, 'click', function() {
					$("#map-item").hide(); 
				});
				google.maps.event.addListener(marker, 'click', function() {
					showMarket(iID);   
				});

			}
			
			function addBullet(oPos) { 
				marker = new google.maps.Marker({
					map:map,
					draggable: false, 
					position: oPos, 
					icon: 'img/marker-sicijijs.png', 
				});  
			}
            	
			function showMarket(iID) {
				//$("#map-item").html("<div class='info'>Bezig met laden...</div>").show().load("get/htm/owaes/owaes-map.html?id=" + iID);
				$("#map-item").html("<div class='info'>Bezig met laden...</div>").show().load("get.owaes.php?format=htm&file=owaes-map.html&id=" + iID);
			} 
			
		</script>
    </head>
    <body id="index">
        <?php echo $oPage->startTabs(); ?> 
    	<div class="body content content-home container">
        	
            	<div class="row">
					<?php /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                <div class="sidecenterright home">
       <!-- HomeUser row border -->         
       <div class="HomeUser row border layoutBlocks" style="z-index: 1000;">
                <div class="panel-group" id="accordionGraphs">
                   <div class="row">
                    <div class="col-md-1">
                        <div rel="<?php echo $oMe->alias(); ?>" class="">
                            <a href="<?php echo $oMe->getURL(); ?>"><?php echo $oMe->getImage("90x90"); ?></a>
                        </div>
                    </div>
                    
                    <div class="col-md-5 detailUser">
                    	<?php 
                        	$iExp1 = $oMe->experience()->total(); 
                        	$iExp2 = $oMe->experience()->total(TRUE);
							$iPrevTreshold = $oMe->experience()->leveltreshold(FALSE);  
							$iNextTreshold = $oMe->experience()->leveltreshold(TRUE); 
							$iPercent1 = round(($iExp1-$iPrevTreshold)/($iNextTreshold-$iPrevTreshold)*100); 
							$iPercent2 = round(($iExp2-$iPrevTreshold)/($iNextTreshold-$iPrevTreshold)*100); 
						?>
                        <p class="gebruikers-naam"><a href="<?php echo $oMe->getURL(); ?>"><?php echo $oMe->getName(); ?></a></p>
                        <p class="level">Level <span class="levelvalue"><?php echo $oMe->level(); ?></span></p>
                        <div class="progress progress-experience" title="Vooruitgang: <?php echo $iPercent1; ?>%" >
                            <div class="progress-bar progress-bar-experience" role="progressbar" aria-valuenow="<?php echo $oMe->experience()->total(); ?>" aria-valuemin="0" aria-valuemax="<?php echo $oMe->experience()->leveltreshold(); ?>" style="width: <?php echo $iPercent1; ?>%;">
                                <span class="sr-only"><?php echo $iPercent1; ?>% Complete</span>
                            </div>
                        </div>
                        <!--<p class="pull-right"><a href="#">Tips</a></p>-->
                    </div>
                   
                    <div class="col-md-4">
                        <div class="indicatoren">
                            <div class="indicator">
                                <img src="img/userSocialIcon.png" alt="Sociaal" title="Sociaal: <?php echo $oMe->social(); ?>%" />
                                <div class="progress progress-sociaal" title="Sociaal: <?php echo $oMe->social(); ?>%">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $oMe->social(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $oMe->social(); ?>%;">
                                        <span class="sr-only"><?php echo $oMe->social(); ?>% Sociaal</span>
                                    </div>
                                    <span class="progressIndicator"><!--+2%--></span>
                                </div>
                            </div>
                            
                            <div class="indicator">
                                <img src="img/userFysiekIcon.png" alt="Fysiek" title="Fysiek: <?php echo $oMe->physical(); ?>%" />
                                <div class="progress progress-fysiek" title="Fysiek: <?php echo $oMe->physical(); ?>%">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $oMe->physical(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $oMe->physical(); ?>%;">
                                        <span class="sr-only"><?php echo $oMe->physical(); ?>% Fysiek</span>
                                    </div>
                                     <span class="progressIndicator"><!--+4%--></span>
                                </div>
                            </div>
                            
                            <div class="indicator">
                             <img src="img/userMentalIcon.png" alt="Kennis" title="Kennis: <?php echo $oMe->mental(); ?>%" />
                                <div class="progress progress-mentaal" title="Kennis: <?php echo $oMe->mental(); ?>%" >
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $oMe->mental(); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $oMe->mental(); ?>%;">
                                        <span class="sr-only"><?php echo $oMe->mental(); ?>% Kennis</span>
                                    </div>
                                     <span class="progressIndicator"><!--+5%--></span>
                                </div>
                            </div>
                            
                            <div class="indicator">
                             <img src="img/userEmotionalIcon.png" alt="Welzijn" title="Welzijn: <?php echo $oMe->emotional(); ?>%" />
                                <div class="progress progress-welzijn" title="Welzijn: <?php echo $oMe->emotional(); ?>%">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $oMe->emotional(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $oMe->emotional(); ?>%;"> 
                                        <span class="sr-only"><?php echo $oMe->emotional(); ?>% Welzijn</span>
                                    </div>
                                     <span class="progressIndicator"><!--+1%--></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <div class="col-md-2 containerCredits">
                        <p class="credits"><?php echo $oMe->credits(); ?></p><span class="icon icon-credits icon-credits-green"></span>
                    </div>
                    
                  
                         <a data-toggle="collapse" data-parent="#accordionGraphs" href="#collapseGraph" class="iconExpand">
                            <span class="icon icon-collapsed"></span>
                         </a>
                  
                    
                 </div>
                    <div id="collapseGraph" class="panel-collapse collapse">
                         <div class="panel-body">
                            <div class="row grafiekenHome">
                            <h2>Vooruitgang:</h2>
                                <div class="col-md-4">
                                    <h3>Ervaring</h3>
                                    <?php 
										$oExp = $oMe->experience(); 
										$arExp = $oExp->timeline(); // / SLOW 
										foreach ($arExp as $i=>$arV) $arExp[$i][0]*=1000;  
										$arLevels = array();  
										foreach (settings("levels") as $iLevel=>$arSettings) { 
											if ($iLevel <= $oExp->level()+1) $arLevels[] = $arSettings["threshold"]; 
										}  
										$arLevelBounds = array(
											"from"=> $arLevels[count($arLevels)-2], 
											"to"=> $arLevels[count($arLevels)-1],  
											"color"=> "#fceeb4",  
										);  
										$arTicks = array(); 
										$arTicks[] = array($arLevels[count($arLevels)-2], "level " . (count($arLevels)-2)); 
										$arTicks[] = array($arLevels[count($arLevels)-1], "level " . (count($arLevels)-1));  
									?> 
                                    <script>
	$(function() {
		
		var dataExp = <?php echo json_encode($arExp); ?>; 
		var optionsExp = {
			xaxis: {
				mode: "time",
				tickLength: 5, 
			},
			series: {
                lines: { show: true, lineWidth: 3 },
                shadowSize: 0
            }, 
			grid: {
				backgroundColor: "#ffffff", 
				markings:  [{ yaxis: <?php echo json_encode($arLevelBounds); ?> } ],  
			},
			yaxis: {
				min: 0,
				max: <?php echo round($arLevels[count($arLevels)-1]*1.1);  ?>,
				color:"#e3e3e3",  
				ticks: <?php echo json_encode($arTicks); ?>, 
			},
		};
		
		$.plot("#expMeter", [ dataExp ], optionsExp);  
		
		var optionsIndi = {
			xaxis: {
				mode: "time",
				tickLength: 5
			},
			yaxis: {
				min: 0,
				max: 100,
				color:"#e3e3e3",   
			}, 
			series: {
                lines: { show: true, lineWidth: 3 },
                shadowSize: 0
            },
			grid: {
				backgroundColor: "#ffffff",  
			},
		};
		
		<?php
			$arIndicatoren = $oMe->indicatorenTimeline(); 
			$arShow = array(
						array(
							"label" => "&nbsp;Sociaal",
							"data" => $arIndicatoren["social"]["data"], 
							"color" => "#8dc63f",
						), 
						array(
							"label" => "&nbsp;Fysiek",
							"data" => $arIndicatoren["physical"]["data"], 
							"color" => "#ff3131",
						),  
						array(
							"label" => "&nbsp;Kennis",
							"data" => $arIndicatoren["mental"]["data"], 
							"color" => "#0072bc",
						), 
						array(
							"label" => "&nbsp;Welzijn",
							"data" => $arIndicatoren["emotional"]["data"], 
							"color" => "#ffcc00",
						), 
					); 
			foreach ($arShow as $strKey=>$arData) foreach ($arData["data"] as $i=>$arVal) $arShow[$strKey]["data"][$i][0]*=1000; 
		?>
		
		$.plot("#indicatorenMeter", <?php echo json_encode($arShow); ?>, optionsIndi);  
	});
									</script> 
                                    <div id="expMeter" style="width: 350px; height: 205px;display: block; "></div> 
                                </div>
                                <div class="col-md-4">
                                    <h3>Indicatoren</h3>
                                    <div id="indicatorenMeter" style="width: 350px; height: 205px;display: block; "></div>  
                                </div>
                                <div class="col-md-4 creditmeter">
                                    <h3><?php echo ucfirst(settings("credits", "name", "x")); ?></h3>
                                    <!-- <img class="size" src="img/creditMeter.png"/> -->
                                    <img class="creditmetermeter size" src="img/creditmetermeter.png" alt="" />
                                    <img class="creditmeterpointer size" src="img/creditmeterpointer.png" alt="" />
                                </div>
                            </div>
                       </div>
                    </div>
                    
            </div>
       </div>
       
       
<div class="col-md-4">
    <h3>Indicatoren</h3>
    <div id="indicatorenRef" style="width: 350px; height: 205px;display: block; "></div> 
    <script>
	
	</script> 
</div>       
       
       <!-- Map -->  
        <div class="homepage map col-md-12 border layoutBlocks" style="z-index: 990;">
             <div id="map-info">
                <ul class="legendHome">
                    <li><img class="mapPin" src="img/greenPin.png" alt="Werkervaring" />Werkervaring</li>
                    <li><img class="mapPin" src="img/redPin.png" alt="Delen" />Delen</li>
                    <li><img class="mapPin" src="img/orangePin.png" alt="Opleiding" />Opleiding</li>
                    <li><img class="mapPin" src="img/bullet.png" alt="Deelnemers" style="height: auto; margin: 7px; " />Deelnemers</li>
                </ul>
            </div>
            <div id="map-item" style="display: none; "></div>
            <div id="map-canvas" style="height: 350px; "></div>
        </div> 

                <div class="row masonry">
                
				<?php 
					$oNotification = new notification(me()); 
					$arMessages = $oNotification->getList(5);  
					if (count($arMessages)>0) {
						?>
						<!-- Berichten -->  
							<div class="col-md-6 clearfix masonrybox" style="z-index: 980;">
								<div class="layoutBlocks border">
									<h2>Recente berichten</h2>
									 
									<?php
										foreach ($arMessages as $arMessage) {
											?><div class="notific">
												<a href="<?php echo isset($arMessage["link"]) ? $arMessage["link"] : "#"; ?>">
									   
															<h4 class="not-heading"><?php echo $arMessage["title"]; ?></h4> 
															<div class="not-time"><?php echo str_date($arMessage["time"]); ?></div> 
															<div class="not-msg"><?php echo $arMessage["message"]; ?></div> 
													  
												</a>
											</div><?php
										}
									?>
								</div>
						   </div>
						<?php
                	}
				?>
                <?php /*
                <!-- Quests -->  
                		<div class="col-md-6 clearfix masonrybox" style="z-index: 980;">
                        	<div class="layoutBlocks border">
                                <h2>Uitdagingen</h2>
                                 <div class="panel-group" id="accordion">
                                 
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                             <span class="icon icon-plus"></span><span class='hoverQuestLink'>Doe deze quest vandaag!</span>
                                            </a>
                                          </h4>
                                   
                                        <div id="collapseOne" class="panel-collapse collapse">
                                          <div class="panel-body">
                                              <p>Schrijf je vandaag in op een opdracht en ontvang een beloning!</p>
                                          </div>
                                        </div>
                                     
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                             <span class="icon icon-plus"></span><span class='hoverQuestLink'>Quest van de week! (deadline 27/04)</span>
                                            </a>
                                          </h4>
                                   
                                        <div id="collapseTwo" class="panel-collapse collapse">
                                          <div class="panel-body">
                                              <p>Verdien deze week minstens 360 credits en ontvang een beloning!</p>
                                          </div>
                                        </div>
                            
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Mijn Quest van de maand April</span>
                                            </a>
                                          </h4>
     
                                        <div id="collapseThree" class="panel-collapse collapse">
                                          <div class="panel-body">
                                              <p>Werk deze maand 6x aan Fysiek en ontvang een grote beloning!</p>
                                          </div>
                                        </div>
                                        
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Quest voor volgende week!</span>
                                            </a>
                                          </h4>
     
                                        <div id="collapseFour" class="panel-collapse collapse">
                                          <div class="panel-body">
                                            <p>Volg volgende week een opleiding met minstens 2 verschillende indicatoren.</p>
                                          </div>
                                        </div>
                                        
                                    </div>
                            </div>
                        </div>
                        
                <!-- Tutorial quests -->  
                        <div class="col-md-6 clearfix masonrybox" style="z-index: 970;">
                            <div class="layoutBlocks border">
                                <h2>Platform-uitdagingen</h2>
                                 <div class="panel-group" id="accordion">
                                        
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Profiel</span>
                                            </a>
                                          </h4>
                                        
                                        <div id="collapseSix" class="panel-collapse collapse">
                                          <div class="panel-body">
                                           <p>Deze tutorial zal je begeleiden in het invullen van je profiel en hoe je de privacy-instellingen kan aanpassen.</p><input type="button" class="btn btn-default btn-sm pull-right" value="Start"/>
                                          </div>
                                        </div>
                                     
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">
                                             <span class="icon icon-plus"></span><span class='hoverQuestLink'>Vrienden</span>
                                            </a>
                                          </h4>
                                   
                                        <div id="collapseSeven" class="panel-collapse collapse">
                                          <div class="panel-body">
                                            <p>Mauris at turpis at eros molestie sagittis. Vestibulum at commodo lectus, non fermentum metus. Donec quis faucibus lacus, lobortis tincidunt leo. Cras tincidunt aliquam neque, fermentum blandit leo tempus a.</p><input type="button" class="btn btn-default btn-sm pull-right" value="Start"/>
                                          </div>
                                        </div>
                            
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseEight">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Volgers</span>
                                            </a>
                                          </h4>
     
                                        <div id="collapseEight" class="panel-collapse collapse">
                                          <div class="panel-body">
                                            <p>Quisque placerat magna a nisl euismod lobortis. Praesent enim metus.</p><input type="button" class="btn btn-default btn-sm pull-right" value="Start"/>
                                          </div>
                                        </div>
                                        
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseNine">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Berichten</span>
                                            </a>
                                          </h4>
     
                                        <div id="collapseNine" class="panel-collapse collapse">
                                          <div class="panel-body">
                                            <p>Cras semper odio eget ipsum ullamcorper.</p><input type="button" class="btn btn-default btn-sm pull-right" value="Start"/>
                                          </div>
                                        </div>
                                        
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTen">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Opdrachten/Opleiding/Delen toevoegen</span>
                                            </a>
                                          </h4>
     
                                        <div id="collapseTen" class="panel-collapse collapse">
                                          <div class="panel-body">
                                            <p>Quisque placerat magna a nisl euismod lobortis. Praesent enim metus.</p><input type="button" class="btn btn-default btn-sm pull-right" value="Start"/>
                                          </div>
                                        </div>
                                        
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseEleven">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Voer een Opdracht uit of volg een Opleiding</span>
                                            </a>
                                          </h4>
     
                                        <div id="collapseEleven" class="panel-collapse collapse">
                                          <div class="panel-body">
                                           <p>Vestibulum vestibulum mi id erat auctor rutrum. Praesent mauris nisi, vehicula nec erat at.</p><input type="button" class="btn btn-default btn-sm pull-right" value="Start"/>
                                          </div>
                                        </div>
                                        
                                          <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwelve">
                                              <span class="icon icon-plus"></span><span class='hoverQuestLink'>Betaling</span>
                                            </a>
                                          </h4>
     
                                        <div id="collapseTwelve" class="panel-collapse collapse">
                                          <div class="panel-body">
                                            <p>Etiam euismod lacus eu nisi mollis, a convallis est iaculis.</p><input type="button" class="btn btn-default btn-sm pull-right" value="Start"/>
                                          </div>
                                        </div>

                                    </div>
                            </div>
                         </div>
                         */ ?>
 
                        <?php
						
						
						
                            $oOwaesList = new owaeslist(); 
                            $oOwaesList->filterByState(STATE_RECRUTE);  
                            $oOwaesList->filterPassedDate(owaesTime());  
                            $oOwaesList->filterByUser(me(), FALSE); 
							$oOwaesList->involved(me(), FALSE); 
                            $oOwaesList->optiOrder();  
                            $oOwaesList->limit(2);  
                            if (count($oOwaesList->getList()) > 0) { 
                                ?>
                                    <!-- Zijn deze activiteiten iets voor jou? --> 
                                 <div class="col-md-6 clearfix masonrybox" style="z-index: 960;">
                                    <div class="layoutBlocks border">
                                        <h2>Zijn deze items iets voor jou?</h2>
        
                                        <div class="list-group">
                                        <?		
                                        foreach ($oOwaesList->getList() as $oItem) {  
                                            echo $oItem->HTML("owaes.main-full.html");  
                                        } 
                                        ?>

                                        </div>
                                        <!-- <p class="meer"><a href="#">meer...</a></p> -->
                                    </div>
                                 </div>											
                                <?php
                            }
                        ?> 
                         
             
                        <?php
                            $oOwaesList = new owaeslist(); 
                            $oOwaesList->filterByState(STATE_RECRUTE);  
                            $oOwaesList->filterPassedDate(owaesTime());  
                            $oOwaesList->limit(2);  
                            $oOwaesList->order("lastupdate desc");  
                            if (count($oOwaesList->getList()) > 0) { 
                                ?>
                                    <!-- Recent aangemaakte activiteiten --> 
                                     <div class="col-md-6 clearfix masonrybox" style="z-index: 950;">
                                        <div class="layoutBlocks border">
                                            <h2>Recent aangemaakte activiteiten</h2>
            
                                            <div class="list-group">
                                                
                                                <?				
                                                foreach ($oOwaesList->getList() as $oItem) {  
                                                    echo $oItem->HTML("owaes.main-full.html");  
                                                } 
                                                ?> 
                                            </div>
            
                                            <!-- <p class="meer"><a href="#">meer...</a></p> -->
                                        </div>
                                     </div>
                                <?php
                            }
                        ?>  
                         
                         
						<?php
                            $oOwaesList = new owaeslist(); 
							$oOwaesList->filterByCreator(me());
							$oOwaesList->open(TRUE); 
														
                            if (count($oOwaesList->getList()) > 0) { 
                                ?>
                                    <!-- Recent aangemaakte activiteiten --> 
                                     <div class="col-md-6 clearfix masonrybox" style="z-index: 950;">
                                        <div class="layoutBlocks border">
                                            <h2>Openstaande items</h2>
            
                                            <div class="list-group">
                                                
                                                <?				
                                                foreach ($oOwaesList->getList() as $oItem) {  
                                                    echo $oItem->HTML("owaes.main-full.html");  
                                                } 
                                                ?> 
                                            </div>
            
                                            <!-- <p class="meer"><a href="#">meer...</a></p> -->
                                        </div>
                                     </div>
                                <?php
                            }
                        ?>   
                         
						<?php
                            $oOwaesList = new owaeslist();   
							$oOwaesList->payment(me(), "tobepayed"); 
                            if (count($oOwaesList->getList()) > 0) { 
                                ?>
                                    <!-- Recent aangemaakte activiteiten --> 
                                     <div class="col-md-6 clearfix masonrybox" style="z-index: 950;">
                                        <div class="layoutBlocks border">
                                            <h2>Nog te ontvangen</h2>
            
                                            <div class="list-group">
                                                
                                                <?				
                                                foreach ($oOwaesList->getList() as $oItem) {  
                                                    echo $oItem->HTML("owaes.main-full.html");  
                                                } 
                                                ?> 
                                            </div> 
                                        </div>
                                     </div>
                                <?php
                            }
							
                            $oOwaesList = new owaeslist();    
							$oOwaesList->payment(me(), "topay"); 
                            if (count($oOwaesList->getList()) > 0) { 
                                ?>
                                    <!-- Recent aangemaakte activiteiten --> 
                                     <div class="col-md-6 clearfix masonrybox" style="z-index: 950;">
                                        <div class="layoutBlocks border">
                                            <h2>Nog te betalen</h2>
            
                                            <div class="list-group">
                                                
                                                <?				
                                                foreach ($oOwaesList->getList() as $oItem) {  
                                                    echo $oItem->HTML("owaes.main-full.html");  
                                                } 
                                                ?> 
                                            </div> 
                                        </div>
                                     </div>
                                <?php
                            }
                        ?>  
                          
                          
						<?php
							$oSubscriptions = new subscriptions(); 
							$oSubscriptions->filter("user", me()); 
							$oSubscriptions->filter("state", array(SUBSCRIBE_SUBSCRIBE));  
                            if (count($oSubscriptions->result()) > 0) { 
                                ?>
                                    <!-- Recent aangemaakte activiteiten --> 
                                     <div class="col-md-6 clearfix masonrybox" style="z-index: 950;">
                                        <div class="layoutBlocks border">
                                            <h2>Wachtend op bevestiging:</h2>
            
                                            <div class="list-group">
                                                
                                                <?				
                                                foreach ($oSubscriptions->result() as $oItem) { 
                                                    echo $oItem->market()->HTML("owaes.main-full.html"); 
                                                } 
                                                ?> 
                                            </div>
            
                                            <!-- <p class="meer"><a href="#">meer...</a></p> -->
                                        </div>
                                     </div>
                                <?php
                            }
                        ?>  
                        
                    <!-- </div> -->
                </div> 
        	<?php echo $oPage->endTabs(); ?>
        </div>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?> 
        </div> 
        <script>
            $(document).ready(function () {
                initCreditmeter(<?php echo intval(($oMe->credits()-settings("credits", "min"))/(settings("credits", "max")-settings("credits", "min"))*100) ; ?>);
				<?php 
					$oActions = new actions(me());  
				?> 
				loadModals(<?php echo json_encode($oActions->modals()); ?>);

                var driehoek = document.getElementsByClassName("iconExpand")[0];
                var spanCollapsed = document.getElementsByClassName("icon icon-collapsed")[0];

                driehoek.addEventListener("click", function() { toggleTriangle(driehoek, spanCollapsed); }, false);
            });

            function toggleTriangle(driehoek, spanCollapsed) {
                if (!driehoek.classList.contains("collapsed")) {
                    spanCollapsed.style.display = "block";
                    driehoek.classList.remove("triangleDown");
                }
                else {
                    spanCollapsed.style.display = "none";
                    driehoek.classList.add("triangleDown");
                }
            }
        </script> 
    </body>
</html>
