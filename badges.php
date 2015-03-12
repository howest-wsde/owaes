<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
 
	//$oBadgesList = new badseslist();   
	//$oBadgesList->filter("visible"); 
	
	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload");     
	$oExperience->add(1);  
    
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
					<? /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                
                <h2>Badges</h2>
                <p>Hier zie je de verzameling badges die je hebt verdient, alsook de badges die je nog kan verdienen. Zijn er geen resterende badges meer? Dan zijn er nog steeds de verborgen badges...</p>
                        <? 
							$arMyBadges = user(me())->getBadges(); 
							
							$arBadges = array(); 
							$oDB = new database("select * from tblBadges where zichtbaar = 1;", TRUE); 
							while ($oDB->nextRecord()) {
								$arBadges[$oDB->get("mkey")] = array(
																	"img" => $oDB->get("img"), 
																	"title" => $oDB->get("title"), 
																	"info" => $oDB->get("info"), 
																); 	
							} 
							
							if (count($arMyBadges)>0) {
								echo ('<h3>Behaalde badges</h3>
               							<div class="row sidecenterright behaaldebadges">'); 
								foreach ($arMyBadges as $strKey=>$arBadge) {
									echo ('<div class="col-md-6 badgeslistitem">
												<div class="row">
													<div class="col-xs-2">
														<img src="img/badges/' . $arBadge["img"] . '" alt="' . $arBadge["title"] . '" width="57" height="57">
													</div>
													<div class="col-xs-10">
														<h2>' . $arBadge["title"] . '</h2>
														<p class="badgedescription">' . $arBadge["info"] . '</p>
													</div>
												</div>
										</div>'); 
									if (isset($arBadges[$strKey])) unset($arBadges[$strKey]);  
								}
								echo ('</div>'); 
							}
							
							if (count($arBadges)>0) {
								echo ('<h3>Nog niet behaalde badges</h3>
                					<div class="row sidecenterright resterendebadges">'); 
								
								foreach ($arBadges as $strKey=>$arBadge) {
										echo ('
										<div class="col-md-6 badgeslistitem">
												<div class="row">
													<div class="col-xs-2">
														<img src="img/badges/' . $arBadge["img"] . '" alt="' . $arBadge["title"] . '" width="57" height="57">
													</div>
													<div class="col-xs-10">
														<h2>' . $arBadge["title"] . '</h2>
														<p class="badgedescription">' . $arBadge["info"] . '</p>
													</div>
												</div>
										</div>'); 
								} 
								echo ('</div>'); 
							}
                        ?>
                      
                 
                
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div> 
    </body>
</html>
