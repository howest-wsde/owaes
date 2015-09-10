<?php
	include "inc.default.php"; // should be included in EVERY file 

	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$strSearch = $_GET["q"]; 

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
        <script> 
			$(document).ready(function(e) {
				$(":input.showfilter").each(function(){
					iCount = $("div.result." + $(this).val()).length; 
					$(this).parent().html($(this).parent().html() + " (" + iCount + ")");  
				})
				$(":input.showfilter,#afstand,#indicatoren").change(function(){ 
					filter(); 
				}) 
				
		  		$("#locatie").change(function(){
					strZoek = $(this).val(); 
					if ($(this).val() == "") {
						$("#latitude").val(0); 
						$("#longitude").val(0); 
						filter(); 	
					} else {
						getLatLong(
							strZoek, 
							function(iLat, iLong) { 
								$("#latitude").val(iLat); 
								$("#longitude").val(iLong); 
								filter(); 	
							}, 
							function(iLat, iLong) { 
								$("#latitude").val(0); 
								$("#longitude").val(0); 
								filter(); 	
							}  
						); 
					}
				})
            });
			
			function filter() {
					
				$("div.result").hide(); 
				$(":input.showfilter:checked").each(function(){ 
					$("div.result." + $(this).val()).show(); 
				})
				$(".zoekgroep").show().each(function(){
					if ($(this).find(".result:visible").length == 0) $(this).hide();  
				})
				strIndicator = $("#indicatoren").val(); 
				if (strIndicator != "") {
					$("div.result.owaes").each(function(){
						if (!$(this).hasClass(strIndicator)) $(this).hide(); 
					})
				}
				iLat = $("#latitude").val(); 
				iLong = $("#longitude").val(); 
				iMax = $("#afstand").val(); 
				if (iLat*iLong > 0){
					$("div.result.owaes").each(function(){ 
						iItemLat = $(this).attr("lat")*1; 
						iItemLong = $(this).attr("long")*1; 
						if (iItemLat * iItemLong > 0) {
							iAfstand = distance(iLat, iLong, iItemLat, iItemLong); 
							if (iAfstand > iMax) $(this).hide(); 
						}
					})
					$("div.result.user").each(function(){ 
						iItemLat = $(this).attr("lat")*1; 
						iItemLong = $(this).attr("long")*1; 
						if (iItemLat * iItemLong > 0) {
							iAfstand = distance(iLat, iLong, iItemLat, iItemLong); 
							if (iAfstand > iMax) $(this).hide(); 
						} else $(this).hide();
					})
				}
			}
		</script>
    </head>
    <body id="index">
        <?php echo $oPage->startTabs(); ?> 
    	<div class="body content-market search">
            
                <div class="container sidecenterright">
                    <div class="row">
					    <?php 
                            echo $oSecurity->me()->html("user.html"); 
                        ?>
                    </div>
                    <div class="main market"> 
                        <div id="results">
                        	<div class="row">
                       			<form method="get" action="search.php" onsubmit="return false; ">
                                	<input type="hidden" name="q" value="<?php ($_GET["q"]); ?>" />
                                    <div class="col-md-3 border sidebar">  
                                        <div class="weergevenPersonen">
                                            <h2>Aanbod</h2> 
                                                <label for="Werkervaring"><input type="checkbox" name="in" class="showfilter" value="ervaring" id="Werkervaring" checked="checked" />Werkervaring</label>
                                                <label for="Opleiding"><input type="checkbox" name="in" class="showfilter" value="opleiding" id="Opleiding" checked="checked" />Opleiding</label>
                                                <label for="Delen"><input type="checkbox" name="in" class="showfilter" value="infra" id="Delen" checked="checked" />Delen</label>
                                                  
                                                    <select name="indicatoren" id="indicatoren" style="width: 100%; margin: 10px 0;  ">
                                                        <option value="" style="font-weight: bold; ">Indicatoren:</option>
                                                        <option value="social">Sociaal</option>
                                                        <option value="physical">Fysiek</option>
                                                        <option value="mental">Kennis</option>
                                                        <option value="emotional">Welzijn</option>
                                                    </select>  
                                            <h2>Gebruikers</h2>
                                                <label for="Individuen"><input type="checkbox" name="in" class="showfilter" value="user" id="Individuen" checked="checked" />Individuen</label>
                                                <label for="Bedrijven"><input type="checkbox" name="in" class="showfilter" value="group" id="Bedrijven" checked="checked" />Groepen</label> 
                                         </div>
                                        <div class="activiteiten"> 
                                            <div class="locatie">
                                            	<label for="locatie"><h2>Locatie: </h2></label> 
                                                <input type="text" id="locatie" name="locatie" />
                                                <input type="hidden" id="latitude" name="latitude" value="0" />
                                                <input type="hidden" id="longitude" name="longitude" value="0" />
                                                <select name="afstand" id="afstand">
                                                	<option value="10">&plusmn; 10 km</option>
                                                	<option value="25">&plusmn; 25 km</option>
                                                	<option value="50">&plusmn; 50 km</option>
                                                </select>
                                            </div>
                                       
                                        
                                        </div> 
                                    </div>
                                </form>
                                
                                
                                <div class="col-md-9">
                                    <?php  
                                            // $oOwaesList->setUser($oUser); 
											echo "<div class=\"col-md-12 zoekgroep\">"; 
												$oOwaesList = new owaeslist(); 
												$oOwaesList->filterByState(STATE_RECRUTE); 
												$oOwaesList->filterPassedDate(owaesTime());
												$oOwaesList->search($strSearch);   
										
												if (count($oOwaesList->getList())>0) echo ("<h1>Aanbod</h1>");  
												foreach ($oOwaesList->getList() as $oItem) { 
													echo $oItem->HTML("search.owaes.html"); 
												}
											echo "</div>"; 
											
											
											echo "<div class=\"col-md-12 zoekgroep\">"; 
												$oUserList = new userlist();   
												$oUserList->filter("visible"); 
												$oUserList->search($strSearch);  
												if (count($oUserList->getList())>0) echo ("<h1>Gebruikers</h1>");  
												foreach ($oUserList->getList() as $oUser) { 
													echo $oUser->HTML("search.user.html"); 
												}
											echo "</div>"; 
											
											
											echo "<div class=\"col-md-12 zoekgroep\">"; 
												$oGroupList = new grouplist();   
												$oGroupList->search($strSearch);  
												if (count($oGroupList->getList())>0) echo ("<h1>Groepen</h1>");  
												foreach ($oGroupList->getList() as $oGroep) { 
													echo $oGroep->HTML("search.group.html"); 
												}
											echo "</div>"; 
                                            
                                            if((count($oOwaesList->getList())==0) && (count($oUserList->getList())==0) && (count($oGroupList->getList())==0)){
                                                echo("<h2> Er zijn geen resultaten gevonden. Wil je het nog eens proberen?</h2>");
                                            }
                                    ?>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
