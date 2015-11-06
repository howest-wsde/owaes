<?php
	define ("TIMEOUT_DEFAULT", 0); 
	define ("TIMEOUT_CLICKED", 2); 
	define ("TIMEOUT_WAITING", 1); 
	define ("TIMEOUT_BUSY", 3); 
	define ("TIMEOUT_CONFIRMED", 4); 
	define ("TIMEOUT_ADDEDNEW", 5); 
	class crons {
		public function crons() { // instellingen voor automatische gebeurtenissen: bv. automatische aftrek indicatoren
			$arCrons = json("settings/crons.json");  
			$bChanged = FALSE;  
			$ar2DO = array(
				array(
					"sleutel" => "indicators", 
					"refresh" => 30*60 // check elke 30 minuten 
				), 
				array(
					"sleutel" => "status", 
					"refresh" => 3*60 // check elke 3 minuten 
				),
				array(
					"sleutel" => "mailalert", 
					"refresh" => 5*60, // check elke 5 minuten 
				),
				array(
					"sleutel" => "experience-stats", 
					"refresh" => 24*60*60 // check elke dag
				), 
				/*array(
					"sleutel" => "reminder", 
					"refresh" => 1, //120*60, // check elke 2 uur
				),*/
			); 
			shuffle($ar2DO); // wordt geshuffled voor moest er een fout of timeout gebeuren in één van bovenstaande
			
			foreach ($ar2DO as $arCron) {
				if (!isset($arCrons[$arCron["sleutel"]])) $arCrons[$arCron["sleutel"]] = 0; 
				if (owaesTime() - $arCrons[$arCron["sleutel"]] > $arCron["refresh"]) { // ) { // check elke 30 minuten 
					//$this->indicators();  
					$arCrons[$arCron["sleutel"]] = owaesTime(); 
					switch($arCron["sleutel"]) {
						case "indicators": 
							$this->indicators(); 
							break; 
						case "status": 
							$this->checkStatus();
							break; 
						case "mailalert": 
							$this->checkMails();
							break; 
						case "experience-stats": 
							$this->experienceStats();
							break; 
						//case "reminder": 
						//	$this->checkReminders();
						//	break; 
					}
					json("settings/crons.json", $arCrons);
				}  
			}  
		}
		 
		
		private function indicators() {
			$oDB = new database(); 
			$oInsertDB = new database();  
			$iRefreshTijd = settings("crons", "indicators"); 
			$iLevelFactor = settings("crons", "levelfactor"); 
			
			$strLastUserrecordsSQL = "select user, max(datum) as datum, reason, link from tblIndicators where actief = 1 group by user";
			/*$strSQL = "select u.id as user, i.datum, i.reason, i.link 
				from tblUsers u 
				left join ( $strLastUserrecordsSQL ) i2 on u.id = i2.user 
				left join tblIndicators i on i.user = i2.user and i.datum = i2.datum and i.actief = 1 
				where u.actief = 1 and u.algemenevoorwaarden = 1 
				having (i.datum < " . (owaesTime()-$iRefreshTijd) . " or i.datum is null)
			"; */
			$strSQL = "select u.id as user, i.datum, i.reason, i.link 
				from tblUsers u 
				left join ( $strLastUserrecordsSQL ) i on u.id = i.user 
				where u.actief = 1 and u.algemenevoorwaarden = 1 and u.admin = 0  
				having (i.datum < " . (owaesTime()-$iRefreshTijd) . " or i.datum is null)
			"; 
			  
			$oDB->sql($strSQL);  
			$oDB->execute(); 
			//echo $oDB->table(); 
			while ($oDB->nextRecord()) {
				// echo $oDB->get("user") . "<br>"; 
				$iNewTime = (is_null($oDB->get("datum"))) ? owaesTime() : ($oDB->get("datum") + $iRefreshTijd);
				$oUser = user($oDB->get("user"));  
		
				if (($oUser->level()^$iLevelFactor) == 0) {
 					$iMin = -1;
				} else $iMin = 0 - (1 / ($oUser->level()^$iLevelFactor));  
				switch($oDB->get("reason")) { // vorige reason
					case TIMEOUT_CLICKED:  // wordt geset in class.subscription: wanneer een user zich inschrijft wordt er meteen een record toegevoegd met reason = TIMEOUT_CLICKED
						$oOwaesItem = owaesitem($oDB->get("link"));
						$arChange["physical"] = ($oOwaesItem->physical()>0) ? 0 : $iMin; 
						$arChange["emotional"] = ($oOwaesItem->emotional()>0) ? 0 : $iMin; 
						$arChange["mental"] = ($oOwaesItem->mental()>0) ? 0 : $iMin; 
						$arChange["social"] = ($oOwaesItem->social()>0) ? 0 : $iMin; 
						$arChange["reason"] = TIMEOUT_WAITING;  
						$arChange["link"] = $oDB->get("link");  
						// enkel 0 voor indicatoren waar op gewerkt wordt
						break; 
					case TIMEOUT_ADDEDNEW: // wordt geset in owaeasadd wanneer een nieuw item toegevoegd wordt  
						$arChange["physical"] = 0; 
						$arChange["emotional"] = 0;
						$arChange["mental"] = 0;
						$arChange["social"] = 0;
						$arChange["reason"] = TIMEOUT_WAITING;  
						$arChange["link"] = $oDB->get("link");  
						
						break; 
					case TIMEOUT_BUSY:   // wordt geset in class.subscription wanneer de state verandert naar SUBSCRIBED  

						$arChange["physical"] = 0; 
						$arChange["emotional"] = 0; 
						$arChange["mental"] = 0; 
						$arChange["social"] = 0;  
						
						$oMarket = new owaesitem($oDB->get("link"));  
						$oCount = new database("select count(id) as aantal from tblIndicators where actief = 1 and user = " . $oDB->get("user") . " and link = " . $oDB->get("link") . " and reason =  " . TIMEOUT_BUSY . ";", TRUE); 
						if ($oCount->get("aantal") > ($oMarket->timing() / settings("crons", "hourstoworkfordelay"))) { 
							$arChange["reason"] = TIMEOUT_WAITING;   // terug naar gewoon 
						} else {
							$arChange["reason"] = TIMEOUT_BUSY;   // blijft busy? 
						} 
						$arChange["link"] = $oDB->get("link");  
						break;  
					case TIMEOUT_CONFIRMED: 
						$arChange["physical"] = 0; 
						$arChange["emotional"] = 0; 
						$arChange["mental"] = 0; 
						$arChange["social"] = 0;  
						$arChange["reason"] = TIMEOUT_BUSY;  
						$arChange["link"] = $oDB->get("link");  
						break; 
					case TIMEOUT_WAITING:  
					default:  
						$arChange["physical"] = $iMin; 
						$arChange["emotional"] = $iMin; 
						$arChange["mental"] = $iMin; 
						$arChange["social"] = $iMin; 
						$arChange["reason"] = TIMEOUT_DEFAULT;  
						$arChange["link"] = 0;  
				}
				$oInsertDB->execute("insert into tblIndicators (user, datum, physical, mental, emotional, social, reason, link, tmp) values ('" . $oDB->get("user") . "', '" . $iNewTime . "', '" . $arChange["physical"] . "', '" . $arChange["mental"] . "', '" . $arChange["emotional"] . "', '" . $arChange["social"] . "', '" . $arChange["reason"] . "', '" . $arChange["link"] . "', '" . json_encode($oDB->record()) . "'); ");
			}
			echo ("<li>cron indicators done</li>"); 
			// echo $oDB->table(TRUE); 
		}
		
		public function checkStatus() {
			$oDB = new database(); 
			$iTimeOut = 60*60*24; //  // 24 uur
			$oDB->sql("select id from tblUsers where statusdate < " . (owaestime()-$iTimeOut) . " order by statusdate; "); 
			$oDB->execute(); 
			while ($oDB->nextRecord()) {
				user($oDB->get("id"))->status(TRUE); 
			}
			echo ("<li>cron status done</li>"); 
		}
		
		
		public function checkMails() {
			$oDB = new database(); 
			$oDB2 = new database();
			$iSent = 0;  
			$oDB->execute("select distinct user as user from tblMailalerts where deadline <= " . owaestime() . " and (sent is null or sent = 0); "); 
			while ($oDB->nextRecord()) {
				$iUser = $oDB->get("user");
				$arMail = array(); 
				$oDB2->execute("select * from tblMailalerts where user = $iUser and deadline <= " . (owaestime()+24*60*60) . " and (sent is null or sent = 0); "); 
				while ($oDB2->nextRecord()) {
					$arLink = json_decode($oDB2->get("link"), TRUE);
					switch($arLink["type"]) {
						case "market": 
							$arMail[] = '<a href="' . fixpath("owaes.php?owaes=" . $arLink["id"], TRUE) . '">' . $oDB2->get("message") . "</a>"; 
							break; 
						case "conversation": 
							$arMail[] = '<a href="' . fixpath("conversation.php?u=" . $arLink["id"], TRUE) . '">' . $oDB2->get("message") . "</a>"; 
							break; 
						default: 	
							$arMail[] = $oDB2->get("message"); 
					} 
				}
				$oUser = user($iUser); 
				$oUser->unlocked(TRUE); // als e-mailadres hidden staat kan deze anders niet gezien worden
				$oMail = new email(); 
					$oMail->setTo($oUser->email(), $oUser->getName());
					$oMail->template("mailtemplate.html");  
					$oMail->setBody(implode("<hr />", $arMail));  
					$oMail->setSubject("OWAES melding"); 
				$oMail->send();  
				$iSent ++; 
				
				$oDB2->execute("update tblMailalerts set sent = " . owaestime() . " where user = $iUser and (sent is null or sent = 0); "); 
			}
			echo ("<li>cron mails done ($iSent mails)</li>"); 
		}
		
		/*
		public function checkReminders() {
			$oDB = new database();  
			$oDB->execute("select m.id, count(m.id) as aantal, m.author, s.clickdate
								from tblMarketSubscriptions s inner join tblMarket m on s.market = m.id 
								where s.overruled = 0 and s.status = " . SUBSCRIBE_SUBSCRIBE . " group by m.id; "); 
			while ($oDB->nextRecord()) {
				$oUser = user($oDB->get("author")); 
				if ($oDB->get("clickdate") <= owaestime() - $oUser->mailalert("remindersubscription")) { 
					$oOwaes = owaesitem($oDB->get("id")); 
					$oAlert = new mailalert(); 
					$oAlert->user($oUser->id()); 
					$oAlert->link("market", $oDB->get("id")); 
					$oAlert->deadline(0);  
					$oAlert->sleutel("market.reminder." . $oDB->get("id"));  
					
					$oAlert->message("Herinnering: U heeft nog " . $oDB->get("aantal") . " openstaande inschrijving" . (($oDB->get("aantal")==1)?"":"en") . " voor de opdracht \"" . $oOwaes->title() . "\"");   
					$oAlert->update();  
				}
			}
		}
		*/
		
		public function experienceStats() {
			$oDB = new database(); 
			$oDB->sql("select "); 
		}
	}
	