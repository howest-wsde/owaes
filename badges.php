<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
 
	//$oBadgesList = new badseslist();   
	//$oBadgesList->filter("visible"); 
    
    $oPage->tab("lijsten");
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="badges">
        <? echo $oPage->startTabs(); ?> 
    	<div class="body content content-lists content-lists-badges container">
        	
            	<div class="row">
					<? /*echo $oSecurity->me()->html("templates/leftuserprofile.html"); */
                    echo $oSecurity->me()->html("templates/user.html");
                    ?>
                </div>
                
                <h2>Badges</h2>
                <p>Hier zie je de verzameling badges die je hebt verdient, alsook de badges die je nog kan verdienen. Zijn er geen resterende badges meer? Dan zijn er nog steeds de verborgen badges...</p>
                <h3>Behaalde badges</h3>
                <div class="row sidecenterright behaaldebadges">
                        <? 
                            //foreach ($oBadgesList->getList() as $oBadge) { 
                            //    echo $oBadge->HTML("templates/badgesfromlist.html");  
                            //}
                            
                            //for ($i = 1; $i <= 10; $i++) {
                            //    readfile("templates/badgesfromlist.html");
                            //}
                        ?>
                        
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/photo.png" alt="Profielfoto" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Profielfoto</h2>
                                        <p class="badgedescription">Deze gebruiker heeft een profielfoto ingesteld. Way to go! </p>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/cupcake.png" alt="Eten" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Eten</h2>
                                        <p class="badgedescription">Werk in de categorie "eten". </p>
                                    </div>
                                </div>
                                
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/earlybird.png" alt="Earlybird" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Earlybird</h2>
                                        <p class="badgedescription">U was als een van de eerste bij OWAES! </p>
                                    </div>
                                </div>
                                
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/power.png" alt="power" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Power</h2>
                                        <p class="badgedescription">Verkrijg 5x een indicator in fysiek.</p>
                                    </div>
                                </div>
                                
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/trans1.png" alt="trans1" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Transactie x1</h2>
                                        <p class="badgedescription">Voer uw eerste transactie uit.</p>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/trans10.png" alt="trans10" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Transactie x10</h2>
                                        <p class="badgedescription">Voer 10 transacties uit.</p>
                                    </div>
                                </div>
                        </div>
                        
                </div>
                
                <h3>Resterende badges</h3>
                <div class="row sidecenterright resterendebadges">
                        <? 
                            //foreach ($oBadgesList->getList() as $oBadge) { 
                            //    echo $oBadge->HTML("templates/badgesfromlist.html");  
                            //}
                            
                            //for ($i = 1; $i <= 20; $i++) {
                            //    readfile("templates/badgesfromlist.html");
                            //}
                            
                            
                        ?>
                        
                        
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/car.png" alt="car" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Reiziger</h2>
                                        <p class="badgedescription">Leg in totaal 100km af onderweg naar een actviteit. </p>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/trans25.png" alt="trans25" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Transactie x25</h2>
                                        <p class="badgedescription">Voer 25 transacties uit.</p>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/trans50.png" alt="trans50" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Transactie x50</h2>
                                        <p class="badgedescription">Voer 50 transacties uit.</p>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/tie.png" alt="wergever" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Werkgever</h2>
                                        <p class="badgedescription">Bied 10 werkaanbiedingen aan.</p>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6 badgeslistitem">
                                <div class="row">
                                    <div class="col-xs-2">
                                        <img src="img/badges/play.png" alt="play" width="57" height="57">
                                    </div>
                                    <div class="col-xs-10">
                                        <h2>Tutorial</h2>
                                        <p class="badgedescription">Overloop succesvol de tutorial.</p>
                                    </div>
                                </div>
                        </div>
                </div>
                
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div> 
    </body>
</html>
