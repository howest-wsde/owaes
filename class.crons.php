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
					"refresh" => 1, // *60 // check elke 5 minuten 
				)
			); 
			shuffle($ar2DO); // wordt geshuffled voor moest er een fout of timeout gebeuren in één van bovenstaande
			
			foreach ($ar2DO as $arCron) {
				if (!isset($arCrons[$arCron["sleutel"]])) $arCrons[$arCron["sleutel"]] = 0; 
				if (owaesTime() - $arCrons[$arCron["sleutel"]] > $arCron["refresh"]) { // ) { // check elke 30 minuten 
					$this->indicators();  
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
					}
					json("settings/crons.json", $arCrons);
				}  
			}  
		}
		 
		
		private function indicators() {
			$oDB = new database(); 
			$oInsertDB = new database();  
			$iRefreshTijd = settings("crons", "indicators"); 
			
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
			echo $oDB->table(); 
			while ($oDB->nextRecord()) {
				// echo $oDB->get("user") . "<br>"; 
				$iNewTime = (is_null($oDB->get("datum"))) ? owaesTime() : ($oDB->get("datum") + $iRefreshTijd);
				switch($oDB->get("reason")) { // vorige reason
					case TIMEOUT_CLICKED:  // wordt geset in class.subscription: wanneer een user zich inschrijft wordt er meteen een record toegevoegd met reason = TIMEOUT_CLICKED
						$oOwaesItem = owaesitem($oDB->get("link"));
						$arChange["physical"] = ($oOwaesItem->physical()>0) ? 0 : -1; 
						$arChange["emotional"] = ($oOwaesItem->emotional()>0) ? 0 : -1; 
						$arChange["mental"] = ($oOwaesItem->mental()>0) ? 0 : -1; 
						$arChange["social"] = ($oOwaesItem->social()>0) ? 0 : -1; 
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
						$arChange["physical"] = -1; 
						$arChange["emotional"] = -1; 
						$arChange["mental"] = -1; 
						$arChange["social"] = -1; 
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
			$oDB->execute("select distinct user as user from tblMailalerts where deadline <= " . owaestime() . " and sent is null; "); 
			while ($oDB->nextRecord()) {
				$iUser = $oDB->get("user");
				$arMail = array(); 
				$oDB2->execute("select * from tblMailalerts where user = $iUser and sent is null; "); 
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
				
				$oDB2->execute("update tblMailalerts set sent = " . owaestime() . " where user = $iUser and sent is null; "); 
			}
			echo ("<li>cron mails done ($iSent mails)</li>"); 
		}
	}
	