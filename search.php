<?
	include "inc.default.php"; // should be included in EVERY file 

	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$strSearch = $_GET["q"]; 

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="index">
        <? echo $oPage->startTabs(); ?> 
    	<div class="body content-market search">
            
                <div class="container sidecenterright">
                    <div class="row">
					    <? 
                            echo $oSecurity->me()->html("templates/user.html");
                        ?>
                    </div>
                    <div class="main market"> 
                        <div id="results">
                        <div class="row">
                            <div class="col-md-3 border sidebar">
                                <div class="weergevenPersonen">
                                    <h2>Weergeven</h2>
                                        <label for="Personen"><input type="checkbox" name="weergeven" value="Personen" id="Personen"/>Personen</label>
                                        <label for="Werkervaring"><input type="checkbox" name="weergeven" value="Werkervaring" id="Werkervaring"/>Werkervaring</label>
                                        <label for="Opleiding"><input type="checkbox" name="weergeven" value="Opleiding" id="Opleiding"/>Opleiding</label>
                                        <label for="Delen"><input type="checkbox" name="weergeven" value="Delen" id="Delen"/>Delen</label>
                                    <h2>Personen</h2>
                                        <label for="Individuen"><input type="checkbox" name="weergeven" value="Individuen" id="Individuen"/>Individuen</label>
                                        <label for="Bedrijven"><input type="checkbox" name="weergeven" value="Bedrijven" id="Bedrijven"/>Bedrijven</label>
                                        <label for="Beheerders"><input type="checkbox" name="weergeven" value="Beheerders" id="Beheerders"/>Beheerders</label>
                                        <label for="Dienstverleners"><input type="checkbox" name="weergeven" value="Dienstverleners" id="Dienstverleners"/>Dienstverleners</label>
                                 </div>
                                <div class="activiteiten">
                                    <h2>Activiteiten</h2>
                                    <div class="tijd">
                                        <span>Tussen</span> 
                                                    <select>
                                                      <option value="00:00">00:00</option><option value="01:00">01:00</option><option value="02:00">02:00</option><option value="03:00">03:00</option><option value="04:00">04:00</option>
                                                      <option value="05:00">05:00</option><option value="06:00">06:00</option><option value="07:00">07:00</option><option value="08:00">08:00</option><option value="09:00">04:00</option>
                                                      <option value="10:00">10:00</option><option value="11:00">11:00</option><option value="12:00">02:00</option><option value="13:00">03:00</option><option value="14:00">04:00</option>
                                                      <option value="15:00">15:00</option><option value="16:00">16:00</option><option value="17:00">07:00</option><option value="18:00">08:00</option><option value="19:00">04:00</option>
                                                      <option value="20:00">20:00</option><option value="21:00">21:00</option><option value="22:00">22:00</option><option value="23:00">23:00</option>
                                                    </select>
                                       <span>En</span> 
                                                    <select>
                                                      <option value="00:00">00:00</option><option value="01:00">01:00</option><option value="02:00">02:00</option><option value="03:00">03:00</option><option value="04:00">04:00</option>
                                                      <option value="05:00">05:00</option><option value="06:00">06:00</option><option value="07:00">07:00</option><option value="08:00">08:00</option><option value="09:00">04:00</option>
                                                      <option value="10:00">10:00</option><option value="11:00">11:00</option><option value="12:00">12:00</option><option value="13:00">13:00</option><option value="14:00">04:00</option>
                                                      <option value="15:00">15:00</option><option value="16:00">16:00</option><option value="17:00">17:00</option><option value="18:00">18:00</option><option value="19:00">04:00</option>
                                                      <option value="20:00">20:00</option><option value="21:00">21:00</option><option value="22:00">22:00</option><option value="23:00">23:00</option>
                                                    </select>
                                     </div>
                                     
                                     
                                    <div class="locatie"><label for="Locatie">Locatie: <input type="text" id="Locatie"/></label></div>
                               
                                    <div class="indicatoren">
                                         <div class="col-md-6">
                                            <label for="Sociaal"><input type="checkbox" name="weergeven" value="Sociaal" id="Sociaal"/>Sociaal</label>
                                            <label for="Fysiek"><input type="checkbox" name="weergeven" value="Fysiek" id="Fysiek"/>Fysiek</label>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Kennis"><input type="checkbox" name="weergeven" value="Kennis" id="Kennis"/>Kennis</label>
                                            <label for="Welzijn"><input type="checkbox" name="weergeven" value="Welzijn" id="Welzijn"/>Welzijn</label>
                                        </div>
                                    </div>
                                   
                                    
                                     <div class="tags"><label for="Tags">Tags: <input type="text" id="Tags"/></label></div>
                                </div>
                               
                            
                            </div>
                            
                            
                            <div class="col-md-9">
                                <? 
								        $oOwaesList = new owaeslist(); 
								        $oOwaesList->filterByState(STATE_RECRUTE); 
								        $oOwaesList->filterPassedDate(owaesTime());
								        $oOwaesList->search($strSearch);   
								
								        // $oOwaesList->setUser($oUser); 
								        if (count($oOwaesList->getList())>0) echo ("<h1>Owaes</h1>");  
								        foreach ($oOwaesList->getList() as $oItem) { 
                                        echo $oItem->HTML("templates/owaeskortsearch.html"); 
								        }
								        $oUserList = new userlist();   
								        $oUserList->filter("visible"); 
								        $oUserList->search($strSearch);  
								        if (count($oUserList->getList())>0) echo ("<h1>Gebruikers</h1>");  
								        foreach ($oUserList->getList() as $oUser) { 
									        echo $oUser->HTML("templates/userfromlist.html"); 
								        }
                                        
                                        if((count($oOwaesList->getList())==0) && (count($oUserList->getList())==0)){
                                            echo("<h2> Er zijn geen resultaten gevonden. Wil je het nog is proberen?</h2>");
                                        }
                                ?>
                            </div>
                        
                        
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
