<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(FALSE);   
       
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
       
    </head>
    <body id="index">
		<?php echo $oPage->startTabs(); ?> 
			<?php if ((settings("debugging", "demo") == TRUE) && (isset($_POST["reset"]) && isset($_POST["demo"]))||(isset($_GET["kdieIIdd88s7"]))) { ?>
				<?php 
					function demodone($strTekst) {
						echo ("<div>done: $strTekst</div>"); 	
					}
					
                	$oDB = new database();  
					$oDB->execute("delete from tblMarket where createdby not in (
									select id from tblUsers where admin = 1 
								); ");  
					demodone("marktitems die niet door admin gemaakt zijn verwijderen"); 

					$oDB->execute("delete from tblMarketDates where market not in (select id from tblMarket); "); 
					$oDB->execute("delete from tblMarketTags where market not in (select id from tblMarket); "); 
					demodone ("gerelateerde records (data en tags) verwijderen"); 
					
					$oDB->execute("update tblUsers set demo = 1 where id in (select author from tblMarket); ");  
					demodone("eventueel nieuwe gebruikers die author zijn van een door-admin-aangemaakte marktitems vastzetten als demo-user"); 
					 
					$oDB->execute("delete from tblMarketSubscriptions where market not in (select id from tblMarket); "); 
					demodone("inschrijvingen op niet meer bestaande items verwijderen"); 

					$oDB->execute("delete from tblPayments where market > 0 and market not in (select id from tblMarket); "); 
					$oDB->execute("delete from tblStars where market not in (select id from tblMarket); "); 
					demodone("betalingen en feedback op niet meer bestaande items verwijderen"); 
					
					$oDB->execute("delete from tblUsers where demo = 0; "); 
					demodone("nieuw toegevoegde gebruikers verwijderen"); 
					
					$oDB->execute("delete from tblMarketSubscriptions where user not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblActions where user not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblConversations where sender not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblConversations where receiver not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblExperience where user not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblFeedback where `from` not in (select id from tblUsers) or `to` not in (select id from tblUsers); ");  
					$oDB->execute("delete from tblGroupUsers where user not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblIndicators where user not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblNotifications where 'author' not in (select id from tblUsers); ");  
					$oDB->execute("delete from tblPayments where 'sender' not in (select id from tblUsers) or 'receiver' not in (select id from tblUsers); "); 
					$oDB->execute("delete from tblStars where 'sender' not in (select id from tblUsers) or 'receiver' not in (select id from tblUsers); ");  
					$oDB->execute("delete from tblUserBadges where user not in (select id from tblUsers); ");  
					$oDB->execute("delete from tblUserCertificates where user not in (select id from tblUsers); ");  
					$oDB->execute("delete from tblUserRecover where user not in (select id from tblUsers); ");   
					$oDB->execute("delete from tblUserSessions where user not in (select id from tblUsers); ");  
					
					if (settings("debugging", "demouser")) $oDB->execute("update tblUsers set login = 'demo', pass = '" . md5("demo") . "' where id = " . settings("debugging", "demouser") . "; ");   
					
					demodone("gerelateerde records van verwijderede gebruikers verwijderen (inschrijvingen, acties, conversaties, experience, feedback, groeplidschap, indicatoren, notificaties, transacties, badges, certificaten, logins en sessies)"); 
					
					
					// TODO: check stars deprecated ? 
					
					$oDB->execute("update tblMarket set lastupdate = '" . owaestime() . "';"); 
					demodone("laatst aangepast-datum van items updaten"); 
					$oDB->execute("select min(datum) as datum from tblMarketDates where datum > 0; "); 
					$iMinDate = $oDB->get("datum"); 
					$iAddToAll = (owaestime()+10*24*60*60) - $iMinDate; // tijd toe te voegen om op "binnen 10 dagen te komen"
					 
					$iAddToAll -= $iAddToAll%(7*24*60*60); // zorgen dat tijdstip gelijk blijft
					$oDB->execute("update tblMarketDates set datum = datum + $iAddToAll where datum > 0; "); 
					demodone("alle data " . ($iAddToAll/24/60/60) . " dagen opschuiven "); 
							
					//$oDB->execute("select count(*) as aantal from tblUsers ;"); 
					//$iTiende = round($oDB->get("aantal") / 10); 
								
					$oDBusers = new database(); 
					$oDBusers->execute("select id from tblUsers order by rand(); "); //  limit $iTiende; "); 
					while ($oDBusers->nextRecord()) {
							$iUser = $oDBusers->get("id"); 
							$iMarket = 0; 
							switch(rand(0, 20)) {
								case 0:
								case 1:
								case 2:
								case 3:
									$oDB->execute("insert into tblIndicators (user, datum, physical, mental, emotional, social, reason, link) values (" . $iUser . ", " . owaesTime() . ", 0, 0, 0, 0, " . TIMEOUT_CLICKED . ", $iMarket); "); // inschrijving 
									break; 
								case 4:
								case 5:
								case 6:
								case 7:
								case 8:
									$arValues = array(0,0,0,0); 
									for ($i=0;$i<4;$i++) $arValues[rand(0,3)]++; 
									$oDB->execute("insert into tblIndicators 
										(user, datum, physical, mental, emotional, social, reason, link)
										values (" . $iUser . ", " . owaesTime() . ", " . $arValues[0] . ", 
										" . $arValues[1] . ", " . $arValues[2] . ", 
										" . $arValues[3] . ", " . TIMEOUT_CONFIRMED . ", " . $iMarket . "); ");   // bevestiging 
									break; 

								case 9: 
									$arValues = array(0,0,0,0); 
									$iMax = rand(1, 10);
									for ($i=0; $i<= rand(1, 10); $i++) {
										$arValues[rand(0,3)]++; 
									} 
									$oDB->execute("insert into tblIndicators 
										(user, datum, physical, mental, emotional, social, reason, link)
										values (" . $iUser . ", " . owaesTime() . ", " . $arValues[0] . ", 
										" . $arValues[1] . ", " . $arValues[2] . ", 
										" . $arValues[3] . ", " . TIMEOUT_DEFAULT . ", " . $iMarket . "); ");   // cadeautje 
									// geen break; 
									 
									
							}
							
			 
					}
					
					
					
					
					
					
					
					
					
					
					
					
					echo "all completed"; 
				?>            
            <?php } else { ?>
            	<form method="post">
                	<input type="checkbox" name="reset" id="reset" /> <label for="reset">Reset ja/neen</label>
                    <br />
                    <input type="submit" name="demo" value="Start" />
                </form>
            <?php } ?>
        <?php echo $oPage->endTabs(); ?> 
    </body>
</html>
