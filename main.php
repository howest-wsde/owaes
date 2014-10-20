<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$oPage->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true");
	$oPage->addJS("script/masonry.js"); 
    
    $oPage->tab("home");
	
	$oMe = user(me()); 
	
	$arModals = array(); 
 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
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
			<?
				list($iLatMe, $iLongMe) = user(me())->LatLong();  
				if ($iLatMe * $iLongMe != 0) {
					echo ("var startpos = new google.maps.LatLng($iLatMe, $iLongMe);"); 	
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
					/*
					var infowindow = new google.maps.InfoWindow({
						content: strInfo
					}); 
					infowindow.open(map, this);
					*/
				});

			}
            	
			function showMarket(iID) {
				$("#map-item").html("<div class='info'>Bezig met laden...</div>").show().load("get/htm/owaes/owaes-map.html?id=" + iID);
			} 
			
		</script></script>
    </head>
    <body id="index">          
    <?
    /*
		$oActions = new actions(me()); 
		vardump($oActions->getList()); 
		*/
	
	?>   
        <? echo $oPage->startTabs(); ?> 
    	<div class="body content content-home container">
        	
            	<div class="row">
					<? /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                <div class="sidecenterright home">
       <!-- HomeUser row border -->         
       <div class="HomeUser row border layoutBlocks" style="z-index: 1000;">
                <div class="panel-group" id="accordionGraphs">
                   <div class="row">
                    <div class="col-md-1">
                        <div rel="<? echo $oMe->alias(); ?>" class="">
                            <a href="<? echo $oMe->getURL(); ?>"><? echo $oMe->getImage("90x90"); ?></a>
                        </div>
                    </div>
                    
                    <div class="col-md-5 detailUser">
                    	<?
                        	$iExp1 = $oMe->experience()->total(); 
                        	$iExp2 = $oMe->experience()->total(TRUE); 
							$iPrevTreshold = $oMe->experience()->leveltreshold(FALSE);  
							$iNextTreshold = $oMe->experience()->leveltreshold(TRUE);
							$iPercent1 = round(($iExp1-$iPrevTreshold)/($iNextTreshold-$iPrevTreshold)*100); 
							$iPercent2 = round(($iExp2-$iPrevTreshold)/($iNextTreshold-$iPrevTreshold)*100); 
							
							if (!isset($_GET["start"])) {
								$iExp2 = $iExp1; 
								$iPercent2 = $iPercent1;  
							}
						?>
                        <p class="gebruikers-naam"><a href="<? echo $oMe->getURL(); ?>"><? echo $oMe->getName(); ?></a></p>
                        <p class="level">Level <span class="levelvalue"><? echo $oMe->level(); ?></span></p>
                        <div class="progress progress-experience" title="Vooruitgang: <? echo $iPercent2; ?>%" >
                            <div class="progress-bar progress-bar-experience" role="progressbar" aria-valuenow="<? echo $oMe->experience()->total(); ?>" aria-valuemin="0" aria-valuemax="<? echo $oMe->experience()->leveltreshold(); ?>" style="width: <? echo $iPercent1; ?>%;">
                                <span class="sr-only"><? echo $iPercent1; ?>% Complete</span>
                            </div>
                        </div>
                        <p class="pull-right"><a href="#">Tips</a></p>
                    </div>
                   
                    <div class="col-md-4">
                        <div class="indicatoren">
                            <div class="indicator">
                                <img src="img/userEmotionalIcon.png" alt="Sociaal" title="Sociaal: <? echo $oMe->social(); ?>%" />
                                <div class="progress progress-sociaal" title="Sociaal: <? echo $oMe->social(); ?>%">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<? echo $oMe->social(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <? echo $oMe->social(); ?>%;">
                                        <span class="sr-only"><? echo $oMe->social(); ?>% Sociaal</span>
                                    </div>
                                    <span class="progressIndicator">+2%</span>
                                </div>
                            </div>
                            
                            <div class="indicator">
                                <img src="img/userFysiekIcon.png" alt="Fysiek" title="Fysiek: <? echo $oMe->physical(); ?>%" />
                                <div class="progress progress-fysiek" title="Fysiek: <? echo $oMe->physical(); ?>%">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<? echo $oMe->physical(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <? echo $oMe->physical(); ?>%;">
                                        <span class="sr-only"><? echo $oMe->physical(); ?>% Fysiek</span>
                                    </div>
                                     <span class="progressIndicator">+4%</span>
                                </div>
                            </div>
                            
                            <div class="indicator">
                             <img src="img/userMentalIcon.png" alt="Kennis" title="Kennis: <? echo $oMe->mental(); ?>%" />
                                <div class="progress progress-mentaal" title="Kennis: <? echo $oMe->mental(); ?>%" >
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<? echo $oMe->mental(); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<? echo $oMe->mental(); ?>%;">
                                        <span class="sr-only"><? echo $oMe->mental(); ?>% Kennis</span>
                                    </div>
                                     <span class="progressIndicator">+5%</span>
                                </div>
                            </div>
                            
                            <div class="indicator">
                             <img src="img/userSocialIcon.png" alt="Welzijn" title="Welzijn: <? echo $oMe->emotional(); ?>%" />
                                <div class="progress progress-welzijn" title="Welzijn: <? echo $oMe->emotional(); ?>%">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="<? echo $oMe->emotional(); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <? echo $oMe->emotional(); ?>%;">
                                        <span class="sr-only"><? echo $oMe->emotional(); ?>% Welzijn</span>
                                    </div>
                                     <span class="progressIndicator">+1%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                     <div class="col-md-2 containerCredits">
                        <p class="credits"><? echo $oMe->credits(); ?></p><span class="icon icon-credits icon-credits-green"></span>
                    </div>
                    
                  
                         <a data-toggle="collapse" data-parent="#accordionGraphs" href="#collapseGraph" class="iconExpand triangleDown">
                            <!-- <span class="icon icon-plus"></span> -->
                         </a>
                  
                    
                 </div>
                    
                    <div id="collapseGraph" class="panel-collapse collapse">
                         <div class="panel-body">
                            <div class="row grafiekenHome">
                            <h2>Vooruitgang:</h2>
                                <div class="col-md-4">
                                    <h3>Punten</h3>
                                    <img class="size" src="img/expMeter.png" alt="" />
                                </div>
                                <div class="col-md-4">
                                    <h3>Indicatoren</h3>
                                    <img class="size" src="img/graphIndicatoren.png" alt="" />
                                </div>
                                <div class="col-md-4 creditmeter">
                                    <h3>Credits</h3>
                                    <!-- <img class="size" src="img/creditMeter.png"/> -->
                                    <img class="creditmetermeter size" src="img/creditmetermeter.png" alt="" /><img class="creditmeterpointer size" src="img/creditmeterpointer.png" alt="" />
                                </div>
                            </div>
                       </div>
                    </div>
                    
            </div>
       </div>
       <!-- Map -->  
        <div class="homepage map col-md-12 border layoutBlocks" style="z-index: 990;">
             <div id="map-info">
                <ul class="legendHome">
                    <li><img class="mapPin" src="img/greenPin.png" alt="Werkervaring" />Werkervaring</li>
                    <li><img class="mapPin" src="img/redPin.png" alt="Delen" />Delen</li>
                    <li><img class="mapPin" src="img/orangePin.png" alt="Opleiding" />Opleiding</li>
                    <!--<li><img class="mapPin" src="img/bluePin.png" alt="Personen" />Personen</li>-->
                </ul>
            </div>
            <div id="map-item" style="display: none; "></div>
            <div id="map-canvas" style="height: 350px; "></div>
        </div>
               
                <div class="row masonry">
                
				<? 
					$oNotification = new notification(me()); 
					$arMessages = $oNotification->getList(5);  
					if (count($arMessages)>0) {
						?>
						<!-- Berichten -->  
							<div class="col-md-6 clearfix masonrybox" style="z-index: 980;">
								<div class="layoutBlocks border">
									<h2>Recente berichten</h2>
									 
									<?
										foreach ($arMessages as $arMessage) {
											?><div class="notific">
												<a href="<? echo isset($arMessage["link"]) ? $arMessage["link"] : "#"; ?>">
									   
															<h4 class="not-heading"><? echo $arMessage["title"]; ?></h4> 
															<div class="not-time"><? echo str_date($arMessage["time"]); ?></div> 
															<div class="not-msg"><? echo $arMessage["message"]; ?></div> 
													  
												</a>
											</div><?
										}
									?>
								</div>
						   </div>
						<?
                	}
				?>
                
                <? /*
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

                        <?
						
						
						
                            $oOwaesList = new owaeslist(); 
                            $oOwaesList->filterByState(STATE_RECRUTE);  
                            $oOwaesList->filterPassedDate(owaesTime());  
                            $oOwaesList->filterByUser(me(), FALSE); 
                            $oOwaesList->enkalkuli("social", $oMe->social());
                            $oOwaesList->enkalkuli("physical", $oMe->physical());
                            $oOwaesList->enkalkuli("mental", $oMe->mental());
                            $oOwaesList->enkalkuli("emotional", $oMe->emotional());
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
                                <?
                            }
                        ?> 
                         
             
                        <?
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
                                <?
                            }
                        ?>  
                         
                         
						<?
                            $oOwaesList = new owaeslist(); 
							$oOwaesList->filterByCreator(me());
							$oOwaesList->filterByState(array(STATE_RECRUTE, STATE_SELECTED));
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
                                <?
                            }
                        ?>  
                         
						<? /*
                            $oOwaesList = new owaeslist(); 
							$oOwaesList->payment(me(), "yes");
							$oOwaesList->rated(me(), "no"); 
                            if (count($oOwaesList->getList()) > 0) { 
                                ?>
                                    <!-- Recent aangemaakte activiteiten --> 
                                     <div class="col-md-6 clearfix masonrybox" style="z-index: 950;">
                                        <div class="layoutBlocks border">
                                            <h2>Vergeet niet feedback te geven</h2>
            
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
                                <?
                            }
                        */ ?>  
                         
						<?
                            $oOwaesList = new owaeslist(); 
							$oOwaesList->subscribed(me(), "confirmed");
							$oOwaesList->payment(me(), "no");
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
            
                                            <!-- <p class="meer"><a href="#">meer...</a></p> -->
                                        </div>
                                     </div>
                                <?
                            }
                        ?>  
                          
                          
						<?
							$oSubscriptions = new subscriptions(); 
							$oSubscriptions->filter("user", me()); 
							$oSubscriptions->filter("state", array(SUBSCRIBE_NEGOTIATE, SUBSCRIBE_SUBSCRIBE));  
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
                                <?
                            }
                        ?>  
                        
                    <!-- </div> -->
                </div> 
        	<? echo $oPage->endTabs(); ?>
        </div>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?> 
        </div>
        <? if ($iExp2 > $iExp1) { 
			$arModals[] = "expModal"; 
			?>
            <!-- MODALS -->
            <div class="modal fade" id="expModal">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Gefeliciteerd!</h4>
                  </div>
                  <div class="modal-body">
                    <p>U hebt sinds uw laatste aanmelding <? echo ($iExp2-$iExp1); ?> ervaringspunt<? echo (($iExp2-$iExp1)==1) ? "" : "en"; ?> verdiend!</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default next-modal" id="btn-credits" data-dismiss="modal">Ok</button>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        <? }?>
        
        <? if ( $oMe->experience()->level(FALSE) != $oMe->experience()->level(TRUE)) { 
			$arModals[] = "lvlModal"; 
			?>
            <div class="modal fade" id="lvlModal">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Gefeliciteerd!</h4>
                  </div>
                  <div class="modal-body">
                    <p>U bent nu level <? echo ($oMe->experience()->level(TRUE)); ?>!</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default next-modal" data-dismiss="modal">Ok</button>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
       	<? } ?>
        
        <? 
			$oOwaesList = new owaeslist(); 
			$oOwaesList->payment(me(), "yes");
			$oOwaesList->rated(me(), "no"); 
			if (count($oOwaesList->getList()) > 0) {  			
				foreach ($oOwaesList->getList() as $oItem) {  
					foreach ($oItem->actions() as $arAction) {
						switch($arAction["type"]) {
							case "rating": 	
								$strModalID = "modalFeedback" . $oItem->id() . "k" . rand(0, 99999); 
								$arModals[] = $strModalID;  
								echo '<div class="modal fade" id="' . $strModalID . '">
									  <div class="modal-dialog">
									  	<form>
											<input type="hidden" name="score" class="stars" value="5" />
											<input type="hidden" name="market" value="' . $oItem->id() . '" />
											<input type="hidden" name="receiver" value="' . $arAction["to"] . '" />
											<div class="modal-content">
											  <div class="modal-header">
												<h4 class="modal-title">Feedback</h4>
											  </div>
											  <div class="modal-body">
												<div>' . $oItem->HTML("owaes.main-full.html") . '</div>
												<p>Hoe tevreden ben je over de samenwerking met ' . user($arAction["to"])->getName() . '</p>
												<ol class="stars">
													<li><a href="#1" rel="1" class="next-modal stars">Helemaal niet tevreden</a></li>
													<li><a href="#2" rel="2" class="next-modal stars">Onvoldoende tevreden</a</li>
													<li><a href="#3" rel="3" class="next-modal stars">Matig tevreden</a</li>
													<li><a href="#4" rel="4" class="next-modal stars">Tevreden</a</li> 
													<li><a href="#5" rel="5" class="next-modal stars">Uiterst tevreden</a></li>
												</ol>
												<span class="stars">Uiterst tevreden</span>
											  </div>
											  <div class="modal-body">  
													<label for="description" class="control-label col-lg-12">Jouw feedback:</label>
													<div class="col-lg-12">
														<textarea name="comment" class="form-control" placeholder="Geef je feedback voor ' . user($arAction["to"])->getName() . '"></textarea>
													</div>  
											  </div>
											  <div class="modal-footer">
												<button type="button" class="btn btn-success next-modal save-feedback-stars" data-dismiss="modal">versturen</button>
												<button type="button" class="btn btn-cancel next-modal" data-dismiss="modal">niet nu</button>
											  </div>
											</div><!-- /.modal-content -->
										</form>
									  </div><!-- /.modal-dialog -->
									</div><!-- /.modal -->'; 
						}
					} 

				}  
			} 
		?>
        <script>
            $(document).ready(function () {
                initCreditmeter();
				<? if (count($arModals)>0) { ?>
					arModals = ["<? echo implode('", "', $arModals); ?>"];
					nextModal(); 
				<? } ?>
				  
				$(".next-modal").click(function(){
					nextModal(); 	
				})
				
				$(".save-feedback-stars").click(function(){ 
					$.post("<? echo fixPath("rate.php") ?>", $(this).parentsUntil("form").parent().serialize());  
				})
                
                $("#btn-credits").click( function (){
					$.ajax("<? echo fixPath("experience-confirm.ajax.php") ?>");  
                    setTimeout(function() {
                        raiseExp(<? echo $iExp2-$iExp1; ?>);
                    }, 700); 
                });
            });
			function nextModal() {
				if (arModals.length > 0) {
					strModal = arModals.shift(); 
					$("#" + strModal).modal({
						show: true,
						backdrop: "static",
						keyboard: false
					});
				}	
			}
        </script> 
    </body>
</html>
