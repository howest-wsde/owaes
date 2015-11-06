<?php  
	define ("STATE_RECRUTE", 0); 
	define ("STATE_SELECTED", 1); 
	define ("STATE_FINISHED", 2); 
	define ("STATE_DELETED", -1);  
	
 	$ar_GLOBAL_owaesitems= array(); 
	
	function owaesitem($iID = NULL) { // FUNCTION owaesitem(5) == CLASS new owaesitem(5)  , maar met call by ref
		global $ar_GLOBAL_owaesitems; 
		if (!isset($ar_GLOBAL_owaesitems[$iID])) {
			$oOwaesItem = new owaesitem($iID); 
			$ar_GLOBAL_owaesitems[$iID] = &$oOwaesItem; 
		}
		return $ar_GLOBAL_owaesitems[$iID]; 
	} 
	function loadedOwaesItems(){
		global $ar_GLOBAL_owaesitems; 
		$arItems = array(); 
		foreach ($ar_GLOBAL_owaesitems as $iID=>$oItem) $arItems[] = $iID; 
		return $arItems; 
	}
	
	class owaesitem {   // een item
		private $iID = 0; 
		private $strTitle = NULL;
		private $strBody = NULL;
		private $strLocation = NULL;
		private $iLocationLong = NULL;
		private $iLocationLat = NULL;
		private $iDate = NULL;
		private $iLastupdate = NULL;
		private $iTiming = NULL;
		private $strTiming = NULL; 
		private $arMomenten = NULL; 
		private $iPhysical = NULL;
		private $iMental = NULL;
		private $iEmotional = NULL;
		private $iSocial = NULL;
		private $iAuthor = NULL;  
		private $iGroup = NULL;   
		private $oSubscriptions = NULL; 
		private $arSubscriptions = array(); 
		private $arTransactions = NULL; 
		private $iCredits = NULL; 
		private $bTask = NULL; 
		private $iState = NULL;  
		private $arTags = NULL; 
		private $iType = NULL;  
		private $arDetails = NULL; 
		private $arFiles = NULL; 
		
		public function snap() {
			$arSnap = array(
				"iID" => $this->iID, 
				"strTitle" => $this->strTitle, 
				"strBody" => $this->strBody, 
				"strLocation" => $this->strLocation, 
				"iLocationLong" => $this->iLocationLong, 
				"iLocationLat" => $this->iLocationLat, 
				"iDate" => $this->iDate, 
				"iLastupdate" => $this->iLastupdate, 
				"iTiming" => $this->iTiming, 
				"strTiming" => $this->strTiming, 
				"arMomenten" => $this->arMomenten, 
				"iPhysical" => $this->iPhysical, 
				"iMental" => $this->iMental, 
				"iEmotional" => $this->iEmotional, 
				"iSocial" => $this->iSocial, 
				"iAuthor" => $this->iAuthor, 
				"iGroup" => $this->iGroup, 
				//"oSubscriptions" => $this->oSubscriptions, 
				//"arSubscriptions" => $this->arSubscriptions, 
				//"arTransactions" => $this->arTransactions, 
				"iCredits" => $this->iCredits, 
				"bTask" => $this->bTask, 
				"iState" => $this->iState, 
				"arTags" => $this->arTags, 
				"iType" => $this->iType,  
				"arDetails" => $this->arDetails,  
				"arFiles" => $this->arFiles,  
			); 
			return $arSnap; 	
		}
		 
		public function owaesitem($iID = 0) { 
			$this->iID = $iID;  
			$this->iDate = owaesTime(); 
			if ($iID == 0) { 
				$this->arMomenten = array();  
				$this->state(STATE_RECRUTE);
			}
		}
		
		public function editable() { // editable for me() ? => returns TRUE or string error-code 
			// TODO: GROEPEN MOETEN KUNNEN ZONDER LEVEL 
			$oMe = user(me()); 
			if (!$oMe->mailVerified()) return("emailverify"); 
			if (!$oMe->algemenevoorwaarden()) return("voorwaarden"); 
			if (!$oMe->admin()) { 
				if ($this->group()) {
					$oRechten = $this->group()->userrights(me());
					if (!$oRechten->owaesedit()) return "rechten"; 
				} else { 
					if ($this->author()->id() != me()) return "rechten"; 
					if ($this->id() == 0) { 
						$bLevelError = FALSE; 
						if ($oMe->level() < $this->type()->minimumlevel()) $bLevelError = TRUE; 
						if ($bLevelError) {
							foreach ($oMe->groups() as $oGroup) {
								if ($oGroup->userrights(me())->owaesadd()) $bLevelError = FALSE;
							}
							if ($bLevelError) return "level"; 
						}
					}
				}	
			}  
			return TRUE; 
		}
		 
		public function getTags() {
			if (is_null($this->arTags)) $this->loadTags(); 
			$arTags = array(); 
			foreach ($this->arTags as $strTag => $arDetails) {
				switch($arDetails["state"]) {
					case "DELETE": 
						break; 
					default: 
						$arTags[] = $strTag; 
				}		
			}
			return $arTags;  
		}
		public function addTag($strTag, $strType="NEW") {
			if (is_null($this->arTags)) $this->loadTags(); 	
			if ($strTag != "") {
				if (!isset($this->arTags[$strTag])) $this->arTags[$strTag] = array("original" => $strType); 
				$this->arTags[$strTag]["state"] = $strType; 
			}
		}
		public function removeTag($strTag) { // tag verwijderen 
			if (is_null($this->arTags)) $this->loadTags(); 
			if (isset($this->arTags[$strTag])) $this->arTags[$strTag]["state"] = "DELETE"; 
		}
		
		private function loadValue($strKey, $strValue) {
			switch($strKey) {
				case "id": 
					$this->iID = $strValue;
					break; 	
				case "date":  
					$this->iDate = $strValue;  
					break; 	 
				case "lastupdate": 
					$this->iLastupdate = $strValue; 
					break; 	
				case "title": 
					if (is_null($this->strTitle)) $this->title($strValue); 
					break; 	
				case "body": 
					if (is_null($this->strBody)) $this->body($strValue); 
					break; 	
				case "author": 
					if (is_null($this->iAuthor)) $this->author($strValue); 
					break; 	
				case "groep": 
					if (is_null($this->iGroup)) $this->group($strValue); 
					break; 	
				case "credits": 
					if (is_null($this->iCredits)) $this->credits($strValue);   
					break; 	
				case "physical": 
					if (is_null($this->iPhysical)) $this->physical($strValue);   
					break; 	 
				case "mental": 
					if (is_null($this->iMental)) $this->mental($strValue);   
					break; 	
				case "emotional": 
					if (is_null($this->iEmotional)) $this->emotional($strValue); 
					break; 	
				case "social": 
					if (is_null($this->iSocial)) $this->social($strValue);  
					break; 	
				case "mtype": 
					if (is_null($this->iType)) $this->type($strValue);       
					break; 	
				case "state": 
					if (is_null($this->iState)) $this->state($strValue);  
					break; 	
				case "timing": 
					if (is_null($this->iTiming)) $this->timing($strValue);  
					break; 	 
				case "timingtype": 
					if (is_null($this->strTiming)) $this->timingtype($strValue);  
					break; 	  
				case "details": 
					if (is_null($this->arDetails)) $this->arDetails = array(); 
					$arDetails = json_decode($strValue, TRUE); 
					if (is_array($arDetails)) foreach ($arDetails as $strKey=>$oVal) {
						if (!isset($this->arDetails[$strKey])) $this->details($strKey, $oVal);  
					}
					break; 
				case "files": 
					if (is_null($this->arFiles)) $this->arFiles = array(); 
					$arFiles = json_decode($strValue, TRUE); 
					if (is_array($arFiles)) foreach ($arFiles as $strFile) $this->arFiles[] = $strFile; 
					break;  
			}	
		}
		
		public function load($oRecord = NULL) {
			if (!is_null($oRecord)) {
				foreach ($oRecord as $strVeld=>$strValue) $this->loadValue($strVeld, $strValue);
			} else {
				$oDB = new database("select * from tblMarket where id = " . intval($this->iID) . ";", TRUE); 
	
				if ($oDB->length() == 1) {
					$oDBrecord = $oDB->record(); 
					foreach ($oDBrecord as $strVeld=>$strValue) $this->loadValue($strVeld, $strValue);
 					if (is_null($this->strLocation)) $this->location($oDBrecord["location"], $oDBrecord["location_lat"], $oDBrecord["location_long"]); 
					// if (is_null($this->arMomenten)) $this->loadMomenten();  
				}  else {
					$this->iLastupdate = 0; 
					$this->iDate = owaesTime();
					$this->iID = 0;
					if (is_null($this->strTitle)) $this->title(""); 
					if (is_null($this->strBody)) $this->body(""); 
					if (is_null($this->iAuthor)) $this->author(me());  
					if (is_null($this->iGroup)) $this->group(0); 
					if (is_null($this->iCredits)) $this->credits(0);   
					if (is_null($this->iPhysical)) $this->physical(25);   
					if (is_null($this->iMental)) $this->mental(25);   
					if (is_null($this->iEmotional)) $this->emotional(25); 
					if (is_null($this->iSocial)) $this->social(25);   
					if (is_null($this->iType)) $this->type(0);    
					if (is_null($this->iState)) $this->state(STATE_RECRUTE);  
					if (is_null($this->iTiming)) $this->timing(0);   
					if (is_null($this->strTiming)) $this->timingtype("free");  
					if (is_null($this->strLocation)) $this->location("", 0, 0);  
					if (is_null($this->arTags)) $this->arTags = array();   
					if (is_null($this->arMomenten)) $this->arMomenten = array();   
					if (is_null($this->arDetails)) $this->arDetails = array();   
					if (is_null($this->arFiles)) $this->arFiles = array();   
				} 	 
			}
		}
		
		private function loadTags() {  
			$this->arTags = array();
			$oTags = new database("select tag from tblMarketTags where market = " . intval($this->iID) . ";", TRUE); 
			while ($oTags->nextRecord()){
				$this->addTag($oTags->get("tag"), "DB"); 
			}  
		}  
		private function loadMomenten() {
			$this->arMomenten = array(); 
			$arLoadedItems = loadedOwaesItems(); 
			if (!in_array($this->id(), $arLoadedItems)) $arLoadedItems[] = $this->id();  
			$oDB = new database("select * from tblMarketDates where market in (" . implode(",", $arLoadedItems) . ") order by datum;", TRUE); 
			while ($oDB->nextRecord()) { 
				owaesitem($oDB->get("market"))->addMoment($oDB->get("datum"), $oDB->get("start"), $oDB->get("tijd"), "DB");  
			}  
		}  
		 
		public function id() { // get ID
			return $this->iID;	
		}
		public function getLink() { // deprecated > use url()
			return $this->url(); 
		}
		public function url() { // link to article details-URL (filename)
			//if ($this->author()->id() == me()) {
			if ($this->userrights("select", me())) {
				return "owaes-selecteer.php?owaes=" . $this->iID; 
			} else {
				return "owaes.php?owaes=" . $this->iID; 
			} 
		}
		public function link($strHTML = NULL) { // maakt een link naar de detailspagina van $strHTML (bv. "test" wordt "<a href='link.html'>test</a>"
			if (is_null($strHTML)) $strHTML = $this->title(); 
			return "<a href=\"" . $this->getLink() . "\" class=\"owaes\">" . $strHTML . "</a>"; 
		}
		
		public function getImage($oPreset = "thumbnail") {  // doet nu niets. TODO: mag waarschijnlijk weg 
			switch($oPreset) {
				case "thumbnail": 
				default: 
					
			}
		}
		
		public function userrights($strWat, $iUser = NULL) { // $strWat = "edit", "del", "select", "pay" , als iUser==0: me
			if (is_null($iUser)) $iUser = me(); 
			if (user($iUser)->admin()) return TRUE; 
			if (!$this->group()) {
				return $this->author()->id() == me(); 
			} else { 
				$oRechten = $this->group()->userrights($iUser);
				switch($strWat) {
					case "edit": 
						return $oRechten->owaesedit(); 
						break; 	
					case "del": 
					case "delete": 
						return $oRechten->owaesedit(); 
						break; 	
					case "select": 
						return $oRechten->owaesselect(); 
						break; 	
					case "pay": 
						return $oRechten->owaespay(); 
						break; 	
					default: 
						error ($strWat . " is een ongeldige waarde (class.owaes.item, line __LINE__"); 
				}
			}	
		}
		
		public function group($iGroup = NULL) { /* get or set groupID (als item gelinkt is aan groep in plaats van gebruiker)
		Returns FALSE indien er geen groep ingesteld is 
		set 0 als group verwijderd moet worden
		*/
			if (!is_null($iGroup)) $this->iGroup = $iGroup;  
			if (is_null($this->iGroup)) $this->load();
			return ($this->iGroup == 0) ? FALSE : group($this->iGroup); 
		}
		
		public function subscriptions($arFilter = array()) { // returns array van class.subscription's
			if (is_null($this->oSubscriptions)) {
				$oSubscriptions = new subscriptions(); 
				$oSubscriptions->filter("market", $this->iID); 
				$this->oSubscriptions = $oSubscriptions; 
				$this->arSubscriptions = array(); 
				foreach ($this->oSubscriptions->result() as $oSubscription) {
					$this->arSubscriptions[$oSubscription->user()->id()] = $oSubscription; 
				}
			} 
			$arResult = $this->arSubscriptions;
			foreach ($arFilter as $strKey=>$oValue) {
				switch(strtolower($strKey)) {
					case "state": 
						foreach ($arResult as $iUser=>$oSubscription) {
							if ($oSubscription->state() != $oValue) unset($arResult[$iUser]); 	
						}
						break; 	
					case "notstate": 
						foreach ($arResult as $iUser=>$oSubscription) {
							if ($oSubscription->state() == $oValue) unset($arResult[$iUser]); 	
						}
						break; 	
				}
			}
			return $arResult; 
		}
		 
		
		public function subscriptionDiv() { // returns de html met de "schrijf in / onderhandel"-knop (of status-tekst)
			// TODO: depreacted
			return ""; 
			$arSubscriptions = $this->subscriptions(); 
			$strSubscription = "<div id=\"subsc" . $this->iID . "\" class=\"sub\">";
			//$strCount = (count($arSubscriptions)==1) ? "1 inschrijving " : count($arSubscriptions) . " inschrijvingen "; 
            $strCount ="";
			switch(me()) {
				case 0: 
					$strSubscription .= $strCount;
					$strSubscription .= " log in om in te schrijven ";
					break;
				case $this->iAuthor: 
					$strSubscription .= $strCount;  
					break; 
				default: 	
					
					$iMyValue = (isset($arSubscriptions[me()])) ? $arSubscriptions[me()]->state() : SUBSCRIBE_CANCEL;
					 
					switch($this->state()) {
						case STATE_SELECTED: 
						case STATE_FINISHED: 
							switch($iMyValue) {
								case SUBSCRIBE_SUBSCRIBE:   
									//$strSubscription .= "<p>Inschrijven niet meer mogelijk, uw inschrijving werd nog niet beoordeeld</p>";
									break; 
								case SUBSCRIBE_CONFIRMED: 
									//$strSubscription .= "<p>Inschrijven niet meer mogelijk, uw inschrijving werd bevestigd</p>";
									break; 
								case SUBSCRIBE_DECLINED:
									//$strSubscription .= "<p>Inschrijven niet meer mogelijk, uw inschrijving werd afgezen</p>";
									break; 
								default: 
									//$strSubscription .= "<p>Inschrijven niet meer mogelijk</p>"; 
							} 
							break;
						default: // STATE_RECRUTE
							$strSubscription .= $strCount; 
							switch($iMyValue) {
								case SUBSCRIBE_SUBSCRIBE: 
									$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_CANCEL . "\" class=\"btn btn-default btn-sm pull-right\"><span class=\"icon icon-close\"></span>uitschrijven</a> "; 
									break;  
								case SUBSCRIBE_CONFIRMED:  
									
									$strSubscription .= "<a href=\"#\" class=\"btn-sm btn btn-success pull-right\"><span class=\"icon icon-inschrijven\"></span>Inschrijving bevestigd</a> ";  
									break; 
								case SUBSCRIBE_DECLINED:
									$strSubscription .= "<p>Uw inschrijving werd afgezen</p>";
								default: 
									if (user(me())->algemenevoorwaarden()) {
										$bCredits = ($this->task()) ? TRUE : (user(me())->credits() >= $this->credits()); 
										if ($bCredits) { 
											$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_SUBSCRIBE . "\" class=\"btn btn-default btn-sm pull-right\"><span class=\"icon icon-inschrijven\"></span>schrijf in</a> ";  
										} else {
											$strSubscription .= "<a href=\"modal.alert.php?t=" . urlencode("Onvoldoende credits") . "&a=" . urlencode("U heeft niet voldoende credits om in te schrijven voor dit item. ") . "\" class=\"btn btn-default btn-sm pull-right domodal\"><span class=\"icon icon-inschrijven\"></span>schrijf in</a> ";  
										}
									} else { 
										$strSubscription .= "<a href=\"modal.algemenevoorwaarden.php\" class=\"btn btn-default btn-sm pull-right domodal\"><span class=\"icon icon-inschrijven\"></span>schrijf in</a> "; 
									}
							} 
							break; 	
					}
		

			} 
			$strSubscription .= "</div>";  
			return $strSubscription; 
		}
		 
		public function addSubscription ($iUser, $iType) { /* 
		wijzig status inschrijving voor user $iUser naar $iType 
$iTypes: STATE_RECRUTE / STATE_SELECTED / STATE_FINISHED / STATE_DELETED
		*/
			$arSubscriptions = $this->subscriptions();
			$arSubscriptions[$iUser] = new subscription(); 
			$arSubscriptions[$iUser]->user($iUser); 
			$arSubscriptions[$iUser]->market($this->id());  
			$arSubscriptions[$iUser]->state($iType);
			$arSubscriptions[$iUser]->save(); 
			$this->arSubscriptions = $arSubscriptions;
			
			switch ($iType) {
				case SUBSCRIBE_SUBSCRIBE: 
					$oNotification = new notification($this->author()->id(), "owaes." . $this->id()); 
					switch (count($arSubscriptions)) { 
						case 1: 
							$oNotification->message(user($iUser)->getName() . " schreef zich in voor de opdracht");
							$oNotification->sender($iUser); 
							break;  
						case 2: 	
							$oNotification->message(user($iUser)->getName() . " en 1 andere persoon schreven zich in voor de opdracht");
							$oNotification->sender($iUser); 
							break; 
						default: 	
							$oNotification->message(user($iUser)->getName() . " en " . (count($arSubscriptions)-1) . " andere personen schreven zich in voor de opdracht");
							$oNotification->sender($iUser); 
							break; 
					} 
					
					if ($this->author()->mailalert("newsubscription")) {
						$oAlert = new mailalert(); 
						$oAlert->user($this->author()->id()); 
						$oAlert->link("market", $this->id()); 
						$oAlert->deadline($this->author()->mailalert("newsubscription"));  
						$oAlert->sleutel("market." . $this->id()); 
						$oAlert->message(user($iUser)->getName() . " schreef zich in voor de opdracht \"" . $this->title() . "\"");  
						$oAlert->update();  
					} 
					if ($this->author()->mailalert("remindersubscription")) { 
						$oAlert = new mailalert(); 
						$oAlert->user($this->author()->id()); 
						$oAlert->link("market", $this->id()); 
						$oAlert->deadline($this->author()->mailalert("remindersubscription"));  
						$oAlert->sleutel("market." . $this->id()); 
						$oAlert->message("Herinnering: U reageerde nog niet op de inschrijving van " . user($iUser)->getName() . " voor de opdracht \"" . $this->title() . "\"");  
						$oAlert->update();  
					} 
					
					$oNotification->key("subscription." .  $this->id()); 
					$oNotification->link(fixPath($this->getLink())); 
					$oNotification->send(); 

					$oExperience = new experience($iUser);  
					$oExperience->detail("reason", "inschrijven_item");     
					$oExperience->add(20);  
					
					break; 

				case SUBSCRIBE_CONFIRMED: 
					$oAction = new action( $this->task() ? $this->author()->id() : $iUser );  
					$iReceiver = $this->task() ? $iUser : $this->author()->id();
					$oAction->type("transaction"); 
					$oAction->data("market", $this->id()); 
					$oAction->data("user", $iReceiver); 
					$iDate = owaestime() + (settings("payment", "timing", "fixeddate")*24*60*60); // default: vandaag + 7 dagen
					foreach ($this->data() as $iSubDate) if ($iSubDate>0 && $iSubDate>$iDate) $iDate = $iSubDate + (settings("payment", "timing", "nodate")*24*60*60); // laatste uitvoerdatum + 2 dagen
					$oAction->tododate($iDate); 
					$oAction->update(); 
					
					$oExperience = new experience(me());  
					$oExperience->detail("reason", "inschrijving bevestigen");     
					$oExperience->add(10);  
					break;  
					
				case SUBSCRIBE_ANNULATION:  
					$iReceiver = $this->task() ? $iUser : $this->author()->id();
					$oAction = new action( $this->task() ? $this->author()->id() : $iUser );  
					$oAction->type("transaction"); 
					$oAction->data("market", $this->id()); 
					$oAction->data("user", $iReceiver); 
					$oAction->checkID(); 
					$oAction->done(owaestime()); 
					$oAction->update(); 
					
					$oExperience = new experience(me());  
					$oExperience->detail("reason", "inschrijving weigeren");     
					$oExperience->add(1);  
					
					break;  
					
				case SUBSCRIBE_CANCEL: 
					
					$oExperience = new experience($iUser);  
					$oExperience->detail("reason", "uitschrijven");     
					$oExperience->add(-22);  
					break; 
			} 
		} 
		 
		public function developmentBoxes() { // returns HTML met code van 4 indicatoren voor dit item
			$strHTML = "<ul>";
			for ($i=0; $i<($this->physical()/25); $i++) $strHTML .= "<li class=\"physical\"><img src=\"img/physical.png\" Title=".'Fysiek'." alt=\"Fysiek: " . $this->physical() . "%\" /></li>"; 
			for ($i=0; $i<($this->mental()/25); $i++) $strHTML .= "<li class=\"mental\"><img src=\"img/mental.png\" Title=".'Kennis'." alt=\"Kennis: " . $this->mental() . "%\" /></li>"; 
			for ($i=0; $i<($this->emotional()/25); $i++) $strHTML .= "<li class=\"emotional\"><img src=\"img/emotional.png\" Title=".'Emotioneel'." alt=\"Emotioneel: " . $this->emotional() . "%\" /></li>"; 
			for ($i=0; $i<($this->social()/25); $i++) $strHTML .= "<li class=\"social\"><img src=\"img/social.png\" Title=".'Sociaal'." alt=\"Sociaal: " . $this->social() . "%\" /></li>"; 
			 
			$strHTML .= "</ul>"; 
			return $strHTML;  
		}
		
		public function uptodate() {
			$bActive = FALSE;  
			if (count($this->data()) > 0) { 
				foreach ($this->data() as $iDate) {
					if ($iDate > owaesTime()) $bActive = TRUE; 
				} 
			} else {
				$bActive = ($this->iLastupdate > (owaesTime()-(30*24*60*60))); 
			} 
			return $bActive; 	
		}
		
		public function classes() { 
			$arClasses = array(); 
			$arClasses[] = $this->task() ? "opdracht" : "markt";  
			$arClasses[] = $this->type()->key();  
			switch($this->state()) { 
				case STATE_FINISHED:
					$arClasses[] = "closed"; 
					break; 
			} 			
			$arClasses[] = ($this->uptodate() && ($this->state()!=STATE_FINISHED)) ? "listed" : "unlisted"; 
			if ($this->physical()>0) $arClasses[] = "physical"; 
			if ($this->mental()>0) $arClasses[] = "mental"; 
			if ($this->emotional()>0) $arClasses[] = "emotional"; 
			if ($this->social()>0) $arClasses[] = "social"; 
			 	
			return $arClasses; 
		}
		
		public function html($strTemplate) { // vraagt pad van template en returns de html met replaced [tags]  
			$oHTML = template($strTemplate); 
			 
			foreach ($oHTML->loops() as $strTag=>$arLoops) {
				switch($strTag) {
			
					case "files":  
						$arList = $this->files();  
						$arResults = array();  
						foreach ($arLoops as $strSubHTML) $arResults[$strSubHTML] = array(); 
						foreach ($arList as $strFile) {   
							foreach ($arResults as $strSubHTML=>$arDummy) {
								$strResult = $strSubHTML; 
								$arFile = explode(".", $strFile, 2); 
								$strResult = str_replace("[file:name]", $arFile[1], $strResult); 
								$strResult = str_replace("[file:url]", fixPath("download.php?m=" . $this->id() . "&f=" . $strFile), $strResult);  
								$arResults[$strSubHTML][] = $strResult;
							} 
						}
						foreach ($arResults as $strSubHTML=>$strResult) {	
							$oHTML->setLoop("files", $strSubHTML, $strResult); 
						}
						break; 
				}
			}
			
			
			foreach ($oHTML->tags() as $strTag) {
				$strResult = $this->HTMLvalue($strTag);  
				if (!is_null($strResult)) $oHTML->tag($strTag, $strResult); 
			}
			
			$strHTML = $oHTML->html(); 
			/*
 			preg_match_all("/\[if:([a-zA-Z0-9-_:#]+)\]([\s\S]*?)\[\/if:\\1\]/", $strHTML, $arResult);   // bv. [if:firstname]firstname ingevuld en zichtbaar[/if:firstname]  
			for ($i=0;$i<count($arResult[0]);$i++) {
				$strResult = $this->HTMLvalue($arResult[1][$i]);
				if (!is_null($strResult)) $strHTML = str_replace($arResult[0][$i], (($strResult == "") ? "" : $arResult[2][$i]), $strHTML); 	
			} 
			preg_match_all("/\[([a-zA-Z0-9-_:#]+)\]/", $strHTML, $arResult);   // alle tags (zonder whitespace)
			if (isset($arResult[1])) foreach ($arResult[1] as $strTag){ 
				$strResult = $this->HTMLvalue($strTag);  
				if (!is_null($strResult)) $strHTML = str_replace("[$strTag]", $strResult, $strHTML); 
			} 
              */
 			if ($this->group()){   
				$strHTML = preg_replace_callback('/\[author\:img\:([0-9]*x[0-9]*)\]/', array(&$this, "imagegroupregreplace"), $strHTML); 
			} else {   
				$strHTML = preg_replace_callback('/\[author\:img\:([0-9]*x[0-9]*)\]/', array(&$this, "imageauthorregreplace"), $strHTML); 
			}  
   
 		
			return $strHTML; 
		} 
		private function imageauthorregreplace(&$matches) { 
			return $this->author()->getImage($matches[1], FALSE);  
		} 
		private function imagegroupregreplace(&$matches) { 
			return $this->group()->getImage($matches[1], FALSE);  
		} 
		
		public function actions() { 
			$arActions = array(); 
			$arSubscriptions = $this->subscriptions();  
			if ($this->iAuthor != me()) {
				if (isset($arSubscriptions[me()])) {
					$oSubscription = $arSubscriptions[me()];  
					if ($oSubscription->state() == SUBSCRIBE_CONFIRMED) { // SUBSCRIBE_CANCEL, SUBSCRIBE_SUBSCRIBE, SUBSCRIBE_CONFIRMED, SUBSCRIBE_DECLINED 
						$oPayment = $oSubscription->payment();   
						if (!$oPayment->signed()) { 
							if ($oPayment->sender() == me()) {
								$arActions[] = array(
									"type" => "transaction",  
									"to" => $oPayment->receiver(), 
									"credits" => 0,   
								); 
							} else {
								$arActions[] = array(
									"type" => "remind", 
									"reason" => "transaction",  
									"to" => $oPayment->sender(), 
									"credits" => 0,   
								); 
							} 
						}  else {
							 if (!$oSubscription->rating(me())->rated())  $arActions[] = array(
										"type" => "rating",  
										"to" => $this->iAuthor,  
									);  
						}
					} 
				}
				if (admin()) $arActions[] = array(
								"type" => "edit",  
							); 
			} else { // ik == author 
				foreach ($arSubscriptions as $iUser=>$oSubscription) {
					if ($oSubscription->state() == SUBSCRIBE_CONFIRMED) {
						if ($oSubscription->payment()->signed()) { // is betaald 
							$oRating = $oSubscription->rating(me()); 
							if (!$oRating->rated()) $arActions[] = array(
									"type" => "rating",  
									"to" => $oRating->receiver(),  
								); 
						} else {
							if ($this->task()) { // ik moet betalen 
								$arActions[] = array(
									"type" => "transaction",  
									"to" => $oSubscription->payment()->receiver(), 
									"credits" => 0,   
								); 
							} else { // ik moet nog ontvangen
								$arActions[] = array(
									"type" => "remind", 
									"reason" => "transaction",  
									"to" => $oSubscription->payment()->sender(), 
									"credits" => 0,   
								); 
							}
						}
					} 
				}  
				if (admin()) $arActions[] = array(
								"type" => "edit",  
							); 
			} 
			return $arActions; 	
		}
		
	
		public function subscriptionLink() { 
			$arSubScriptions = $this->subscriptions(); 
			$iState = (isset($arSubScriptions[me()])) ? $arSubScriptions[me()]->state() : SUBSCRIBE_CANCEL;
			switch($iState) { 
				case SUBSCRIBE_SUBSCRIBE: 
					return "subscribe.php?m=" . $this->id() . "&t=" . SUBSCRIBE_CANCEL; 
					break;  
				case SUBSCRIBE_CONFIRMED:  
				case SUBSCRIBE_DECLINED:
					return FALSE; 
					break; 
				default: 
					return "subscribe.php?m=" . $this->id() . "&t=" . SUBSCRIBE_SUBSCRIBE;   
					break; 
			} 
		}
		
		public function flow() {
			$arFlow = array(
				//10 => array(
				//	"title" => $this->type()->title(), // "Werkervaring / Opleiding / Delen", 
				//	"href" => "index.php?t=" . $this->type()->key(), 
				//	"class" => array("done"), 
				//), 
				20 => array(
					"title" => "Inschrijvingen",  
					"count" => 0,  
					"href" => $this->getLink(),  
					"class" => array(), 
				), 
				30 => array(
					"title" => "Bevestiging", 
					"count" => 0,  
					"class" => array(), 
				), 
				40 => array(
					"title" => settings("credits", "name", "overdracht"), 
					"count" => 0,  
					"class" => array(),  
				), 
				50 => array(
					"title" => "Feedback",  
					"count" => 0,  
					"class" => array("last"), 
				), 
			);  
	
			$arSubScriptions = $this->subscriptions();  
			// vardump($arSubScriptions); 
			foreach ($arSubScriptions as $iUser=>$oSubscription) {
				switch($oSubscription->state()) {
					case SUBSCRIBE_SUBSCRIBE: 
						$arFlow[20]["count"] ++; 
						break; 
					case SUBSCRIBE_CONFIRMED: 
					case SUBSCRIBE_FINISHED: 
						$arFlow[30]["count"] ++;  
						break; 
					default:  
				}
			}
			
			if ($this->author()->id() == me()) { // MIJN item
				if (count($arSubScriptions) > 0) { 
					$arFlow[20]["class"][] = "done"; 
					//$arFlow[30]["href"] = $this->getLink(); 
					if (count($this->subscriptions(array("state"=>SUBSCRIBE_SUBSCRIBE))) == 0) { // niemand "gewoon ingeschreven" (excl. bevestigd / geweigerd)
						$arConfirmedUsers = $this->subscriptions(array("state"=>SUBSCRIBE_CONFIRMED));  
						if (count($arConfirmedUsers) > 0) { // minimum 1 persoon confirmed
							$arFlow[30]["class"][] = "done"; 
							if ($this->task()) { // ik moet betalen  
								$arNotPayed = array();
								$arNotRated = array();
								foreach ($arConfirmedUsers as $iUser=>$oSubscription) { 
									if (!$oSubscription->payment()->signed()) $arNotPayed[] = $iUser;  
									if (!$oSubscription->rating()->rated()) $arNotRated[] = $iUser;  
								}
								switch(count($arNotPayed)) {
									case 0: // iedereen is betaald 
										$arFlow[40]["class"][] = "done"; 
										switch(count($arNotRated)) {
											case 0:  // alle ratings zijn gebeurd
												$arFlow[50]["class"][] = "done"; 
												break; 
											case 1: 
												$oRating = new rating(array(
													"market" => $this->id(), 
													"sender" => me(), 
													"receiver" => $arNotRated[0],
												));   
												if (!$oRating->rated()) {
													$arFlow[50]["href"] = "modal.feedback.php?m=" . $this->id() . "&u=" . $arNotRated[0] . "&refresh=1"; 
													$arFlow[50]["class"][] = "domodal";   
													$arFlow[50]["class"][] = "current"; 
												} 
												break; 
											default: 
												$arFlow[50]["href"] = $this->getLink(); 
												$arFlow[50]["class"][] = "current";  
										} 
										break;  
									case 1:  // ik moet (nog) precies één iemand betalen
									
										$arFlow[40]["href"] = "modal.transaction.php?m=" . $this->id() . "&u=" . $arNotPayed[0] . "&refresh=1"; 
										$arFlow[40]["class"][] = "domodal";   
										$arFlow[40]["class"][] = "current"; 
										break; 
									default: // nog verschillende personen moeten betaald worden
										$arFlow[40]["href"] = $this->getLink(); 
										$arFlow[40]["class"][] = "current";  
										break; 
								}  
							} else { // ik moet betaald worden 
								$arNotPayed = array();
								$arNotRated = array();
								foreach ($arConfirmedUsers as $iUser=>$oSubscription) { 
									if (!$oSubscription->payment()->signed()) $arNotPayed[] = $iUser;  
									if (!$oSubscription->rating()->rated()) $arNotRated[] = $iUser;  
								}
								switch(count($arNotPayed)) {
									case 0: // iedereen heeft betaald 
										$arFlow[40]["class"][] = "done"; 
										switch(count($arNotRated)) {
											case 0:  // alle ratings zijn gebeurd
												$arFlow[50]["class"][] = "done"; 
												break; 
											case 1: 
												$oRating = new rating(array(
													"market" => $this->id(), 
													"sender" => me(), 
													"receiver" => $arNotRated[0],
												));  
												if (!$oRating->rated()) {
													$arFlow[50]["href"] = "modal.feedback.php?m=" . $this->id() . "&u=" . $arNotRated[0] . "&refresh=1"; 
													$arFlow[50]["class"][] = "domodal";   
													$arFlow[50]["class"][] = "current"; 
												}
												break; 
											default: 
												$arFlow[50]["href"] = $this->getLink(); 
												$arFlow[50]["class"][] = "current"; 
												
										} 
										break;  
									default: // nog verschillende personen moeten betalen
										$arFlow[40]["href"] = $this->getLink(); 
										$arFlow[40]["class"][] = "current";  
										break; 
								}  
							}
						}  
					} else { // minimum één persoon staat nog gewoon "ingeschreven" zonder bevesigd of afgewezen te zijn
						$arFlow[30]["class"][] = "current";  
						$arFlow[30]["href"] = $this->getLink();  
					}
				} else $arFlow[20]["class"][] = "current";  // nog geen reactie's op item (geen inschrijvingen)
			} else {   // item van iemand anders
				$iMyValue = (isset($arSubScriptions[me()])) ? $arSubScriptions[me()]->state() : SUBSCRIBE_CANCEL; 
				switch ($iMyValue) {
					case SUBSCRIBE_SUBSCRIBE: 
						$arFlow[20]["class"][] = "notconfirmed";  
						$arFlow[20]["href"] = $this->subscriptionLink();  
					case SUBSCRIBE_CONFIRMED: 
					case SUBSCRIBE_DECLINED: 
						$arFlow[20]["title"] = "Ingeschreven"; 
						$arFlow[20]["class"][] = "done"; 
						break; 
					default: 
						$arFlow[20]["title"] = "Inschrijven";  
						switch($this->state()) {
							case STATE_SELECTED: 
							case STATE_FINISHED: 
								// inschrijven niet meer mogelijk
								unset($arFlow[20]["href"]); 
								break;
							default: // STATE_RECRUTE
								$arFlow[20]["class"][] = "current";  
								if (!user(me())->mailVerified()) { 
									$arFlow[20]["href"] = "modal.mailnotverified.php"; 
									$arFlow[20]["class"][] = "domodal";  
								} else if (!user(me())->algemenevoorwaarden()) { 
									$arFlow[20]["href"] = "modal.voorwaarden.php"; 
									$arFlow[20]["class"][] = "domodal";  
								} else {
									if ($this->subscriptionLink()) {
										//$arFlow[20]["href"] = $this->subscriptionLink();  
										$bCredits = ($this->task()) ? TRUE : (user(me())->credits() >= $this->credits()); 
										if ($bCredits) {
											$arFlow[20]["href"] = "modal.subscribe.php?m=" . $this->id();  
											$arFlow[20]["class"][] = "domodal"; 
										} else {
											$arFlow[20]["href"] = "modal.alert.php?t=" . urlencode("Onvoldoende credits") . "&a=" . urlencode("U heeft niet voldoende credits om in te schrijven voor dit item. ");  
											$arFlow[20]["class"][] = "domodal";  
										}
									}
								}
						}
					
						break;  
				}
				
				switch ($iMyValue) {
					case SUBSCRIBE_CONFIRMED: 
						$arFlow[30]["title"] = "Inschrijving bevestigd"; 
						$arFlow[30]["class"][] = "done";  
						$arFlow[30]["href"] = $this->getLink();
						if (!$this->task()) { // ik moet betalen 
							$oPayment = new payment(array("sender"=>me(), "receiver"=>$this->author()->id(), "market"=>$this->id())); 
							if ($oPayment->signed()) { // is al betaald
								$arFlow[40]["href"] = $this->getLink(); 
								$arFlow[40]["class"][] = "done"; 
								$arFlow[50]["href"] = "modal.feedback.php?m=" . $this->id() . "&u=" . $this->author()->id() . "&refresh=1"; 
								$arFlow[50]["class"][] = "domodal";   
								$arFlow[50]["class"][] = "current";   
							} else { // nog niet betaald
								$arFlow[40]["href"] = "modal.transaction.php?m=" . $this->id() . "&u=" . $this->author()->id() . "&refresh=1"; 
								$arFlow[40]["class"][] = "domodal";   
								$arFlow[40]["class"][] = "current";   
							}
						} else { // ik moet betaald worden 
							$arFlow[40]["href"] = $this->getLink(); 
							$oPayment = new payment(array("receiver"=>me(), "sender"=>$this->author()->id(), "market"=>$this->id()));  
							if ($oPayment->signed()) { // is al betaald
								$arFlow[40]["class"][] = "done"; 
								$arFlow[50]["href"] = "modal.feedback.php?m=" . $this->id() . "&u=" . $this->author()->id() . "&refresh=1"; 
								$arFlow[50]["class"][] = "domodal";   
								$arFlow[50]["class"][] = "current";   
							} else { // nog niet betaald
								$arFlow[40]["class"][] = "current";   
							}
						}
						break;  
					case SUBSCRIBE_SUBSCRIBE:  
						$arFlow[30]["title"] = "Wachten op bevestiging"; 
						$arFlow[30]["class"][] = "current";   
						break;  
					case SUBSCRIBE_DECLINED: 
						$arFlow[30]["title"] = "Afgewezen"; 
						$arFlow[30]["class"][] = "stop";  
						$arFlow[30]["href"] = $this->getLink();   
						break; 
					default:  
						break;  
				}
			} 
			$arFlow[20]["title"] .= " (" . $arFlow[20]["count"] . ")"; 
			$arFlow[30]["title"] .= " (" . $arFlow[30]["count"] . ")"; 
			return $arFlow;  
		}
		
		public function reportLink() {
			return "modal.report.php?m=" . $this->id(); 	
		}
		
		private function HTMLvalue($strTag) { 
			$arTag = explode(":", $strTag, 2);  
			switch(strtolower($arTag[0])) { 
				case "flow": 
					$strFlow = "<ol class=\"flow\">"; 
					foreach ($this->flow()  as $iID=>$arFlowDetails) { 
						$strFlow .= "<li class=\"" . implode(" ", $arFlowDetails["class"]) . "\">"; 
						if (isset($arFlowDetails["href"])) { 
							$strFlow .= "<a href=\"" . $arFlowDetails["href"] . "\" class=\"" . implode(" ", $arFlowDetails["class"]) . "\">" . $arFlowDetails["title"] . "</a>"; 
						} else {
							$strFlow .= $arFlowDetails["title"]; 
						}
						$strFlow .= "</li>"; 
					}
					$strFlow .= "</ol>"; 
					return $strFlow; 
				case "id": 
					return $this->id();  
				case "report": 
					return $this->reportLink(); 
				case "classes": 
					return implode(" ", $this->classes());  
				case "title": 
					return html($this->title());  
				case "body":  
					//return html($this->body(), array("p", "a", "strong", "em", "br"));
					if (isset($arTag[1])) {
						switch(strtolower($arTag[1])) { 
							case "short": 
								return nl2br(shorten(html($this->body(), array("p", "a", "strong", "em", "br")), 250, TRUE));  
								break;  
							default: 
								return nl2br(html($this->body(), array("p", "a", "strong", "em", "br")));  
						}
					} else return nl2br(html($this->body(), array("p", "a", "strong", "em", "br")));  
				case "link": 
				case "url": 
					return $this->getLink(); 
				case "iconclass": 
					return $this->type()->iconclass(); 
				case "soorticon": 
					return "<span class='" . $this->type()->iconclass() . "'></span>"; 
				case "user": 
					return $this->author()->HTMLvalue($arTag[1]); 
				case "author": 
					if (isset($arTag[1])) {
						$arSub = explode(":", $arTag[1], 2); 
						switch(strtolower($arSub[0])) { 
							case "type": 
								return ($this->group()) ? "group" : "user";  
							case "url": 
								return ($this->group()) ? $this->group()->getURL() : $this->author()->getURL(); 
							case "key": 
								if ($this->group()) {
									return (($this->group()->alias() == "")?$this->group()->id():$this->group()->alias());  // of id als er geen username is
								} else {
									return (($this->author()->alias() == "")?$this->author()->iID:$this->author()->alias());  // of id als er geen username is
								} 	
							case "box":
								return $this->author()->userBox(); 
							case "img": // [author:img:90x90]
								if ($this->group()) {
 									//return $this->group()->userImage($arSub[1], $this->author()->id(), FALSE);
 									return $this->group()->getImage($arSub[1], FALSE, $this->author()->id()); 
								} else {
									return $this->author()->HTMLvalue($arTag[1]);
								}
							default: 
								return ($this->group()) ? $this->group()->HTMLvalue($arTag[1]) : $this->author()->HTMLvalue($arTag[1]);  
						}
					} else return ($this->group()) ? $this->group()->getLink() : $this->author()->getLink();  
					break; 
				case "location": 
					$strLocation = $this->location(); 
					return ($strLocation == "") ? "geen locatie opgegeven" : $strLocation; 
				case "verzekeringen": 
					$arVerzekeringen = $this->details("verzekeringen"); 
					$arSettingVerzekeringen = settings("verzekeringen"); 
					if (is_array($arVerzekeringen)) {
						foreach ($arVerzekeringen as $iDummy=>$iVal) $arVerzekeringen[$iDummy] = $arSettingVerzekeringen[$iVal];  
						return (count($arVerzekeringen)==0) ? "geen verzekeringen opgegeven" : implode("<br />", $arVerzekeringen);  
					} else return "geen verzekeringen opgegeven";  
				case "latitude": 
					return $this->latitude(); 
				case "longitude": 
					return $this->longitude(); 
				case "data": // case "data:short": 
					if (count($this->data()) > 0) {
						$arSub = array();  
						foreach ($this->data() as $iDate) {
							$oMoment = $this->getMoment($iDate);  
							if ($iDate == 0) { // om het even welke dag
								if ($oMoment["tijd"] == 0) {
									if ($oMoment["start"] == 0) {
										$arSub[] = "<li>willekeurige datum</li>"; 
									} else {
										$arSub[] = "<li>willekeurige datum, om " . minutesTOhhmm($oMoment["start"]) . "</li>"; 
									}
								} else {
									if ($oMoment["start"] == 0) {
										$arSub[] = "<li>willekeurige datum</li>"; // , gedurende " . minutesTOhhmm($oMoment["tijd"]) . "</li>"; 
									} else {
										if ($oMoment["start"]+$oMoment["tijd"] > 60*24) {
											$arSub[] = "<li>willekeurige datum, vanaf " . minutesTOhhmm($oMoment["start"]) . "</li>"; 
													//		"  gedurende " . minutesTOhh($oMoment["tijd"]) . "</li>"; 
										} else {
											$arSub[] = "<li>willekeurige datum, van " . minutesTOhhmm($oMoment["start"]) . 
															" tot " . minutesTOhhmm($oMoment["start"]+$oMoment["tijd"]) . "</li>"; 
										}
									} 
								} 
							} else {
								if ($oMoment["tijd"] == 0) {
									if ($oMoment["start"] == 0) {
										$arSub[] = "<li>" . str_date($iDate, "datum") . "</li>"; 
									} else {
										$arSub[] = "<li>" . str_date($iDate, "datum") . " om " . minutesTOhhmm($oMoment["start"]) . "</li>"; 
									}
								} else {
									if ($oMoment["start"] == 0) {
										$arSub[] = "<li>" . str_date($iDate, "datum") . " gedurende " . minutesTOhh($oMoment["tijd"]) . "</li>"; 
									} else {
										if ($oMoment["start"]+$oMoment["tijd"] > 60*24) {
											$arSub[] = "<li>" . str_date($iDate, "datum") . " vanaf " . minutesTOhhmm($oMoment["start"]) . 
														" gedurende " . minutesTOhh($oMoment["tijd"]) . "</li>"; 
										} else {
											$arSub[] = "<li>" . str_date($iDate, "datum") . " van " . minutesTOhhmm($oMoment["start"]) . 
														" tot " . minutesTOhhmm($oMoment["start"]+$oMoment["tijd"]) . "</li>"; 
										}
									} 
								} 
							} 
						}
						if (($strTag == "data:short")&&(count($arSub)>5)) {
							$arSub[3] = "<li>... en " . (count($arSub)-3) . " andere data</li>"; 
							array_splice($arSub, 4);
						}
						return "<ul class=\"data\">" . implode("", $arSub) . "</ul>"; 
					} else {
						return "willekeurige datum"; 
					}
					return "vrij te kiezen"; 
				case "timing": 
					$iTiming = $this->timing(); 
					if ($iTiming == 0) {
						return "geen tijdsduur ingesteld"; 
					} else {
						
						$iUur = floor($iTiming); 
						$iMin = round($iTiming*60)%60; 
						return (($iMin==0) ? "$iUur uur" : ($iUur . "u " . $iMin)); 
					}
				case "createdate": 
					return str_date($this->iDate, "datum"); 
				case "locationimg":  // :100x100
					switch ($this->location()) {
						case "":  
						case "free": 
							return "";
							break; 
						default: 	
							return $this->locationIMG();
					}
				case "development":
					return $this->developmentBoxes(); 
				case "credits":
					return ($this->iCredits==0) ? "" : $this->iCredits;    
				case "subscribe":
					return ""; // $this->subscriptionDiv();
				case "state":
					switch($this->state()) {
						case STATE_SELECTED: 
							return "in uitvoering"; 
						case STATE_FINISHED:  
							return "afgesloten";  
						case STATE_RECRUTE: 
							return "open"; 
						default: 
							return ""; 
					}
				case "tags":  
					$arTags = array(); 
					foreach ($this->getTags() as $strTag) $arTags[] = "<span>" . htmlentities($strTag) . "</span>"; 
					return implode("", $arTags); 
				case "aantalinschrijvingen":  
					$arSubscriptions = $this->subscriptions(array("notstate"=>SUBSCRIBE_DECLINED)); 
					return (count($arSubscriptions)==1) ? "1 inschrijving " : count($arSubscriptions) . " inschrijvingen "; 
				case "actions":  
					$arActions = array(); 
					$arSubscriptions = $this->subscriptions();  
					if ($this->editable() === TRUE) $arActions[] = "<a href=\"" . fixPath("owaesadd.php?edit=" . $this->id()) . "\"><img src=\"" . fixPath("img/edit.png") . "\" alt=\"aanpassen\" class=\"btn btn-default btn-sm pull-right edit\" align=\"right\" style=\"margin-top: 10px; \" /></a>"; 
					/*
					if ($this->iAuthor != me()) { 
						$iMyValue = (isset($arSubscriptions[me()])) ? $arSubscriptions[me()]->state() : SUBSCRIBE_CANCEL; 
						if ($this->userrights("edit", me())) $arActions[] = "<a href=\"" . fixPath("owaesadd.php?edit=" . $this->id()) . "\"><img src=\"" . fixPath("img/edit.png") . "\" alt=\"aanpassen\" class=\"btn btn-default btn-sm pull-right edit\" align=\"right\" /></a>"; 
					} else { // ik == author
						$iCount = count($arSubscriptions); 
						if ($iCount > 0) { 
							$iConfirmed = 0; 
							$iPayed = 0;  
							foreach ($arSubscriptions as $iUser=>$oSubscription) {
								if ($oSubscription->state() == SUBSCRIBE_CONFIRMED) {
									$iConfirmed++; 
									if ($oSubscription->payment()->signed()) $iPayed ++; 
								} 
							} 
						}
						$arActions[] = "<a href=\"" . fixPath("owaesadd.php?edit=" . $this->id()) . "\"><img class=\"btn btn-default btn-sm pull-right\" src=\"" . fixPath("img/edit.png") . "\" alt=\"aanpassen\" align=\"right\" /></a>"; 
					}
					*/
					return implode("", $arActions);
 
 
				default: 
					$arSplit = explode(":", $strTag, 2);  
					if ($arSplit[0]=="market") return $this->HTMLvalue($arSplit[1]); 
					return NULL; 
			}
		}
		
		
		public function type($vType = NULL) {
			if (!is_null($vType)) $this->iType = owaestype($vType)->id(); 
			if (is_null($this->iType)) $this->load();
			//vardump(owaestype($this->iType)); 
			return owaestype($this->iType); 
		}
		
		public function title($strTitle = NULL) { // get / set titel
			if (!is_null($strTitle)) $this->strTitle = $strTitle; 
			if (is_null($this->strTitle)) $this->load();
			if (!user(me())->mailVerified()) return rubbish($this->strTitle); 
			return $this->strTitle; 
		}
		public function body($strBody = NULL) { // get / set description 
			if (!is_null($strBody)) $this->strBody = $strBody; 
			if (is_null($this->strBody)) $this->load();
			if (!user(me())->mailVerified()) return rubbish($this->strBody); 
			return $this->strBody; 
		}
		public function details($strItem, $oValue = NULL) { // get / set description 
			if (!is_null($oValue)) $this->arDetails[$strItem] = $oValue;  
			return isset($this->arDetails[$strItem]) ? $this->arDetails[$strItem] : NULL; 
		}
		public function location($strLocation = NULL, $iLocationLat = NULL, $iLocationLong = NULL) { /* get / set location
			set : strlocation en lat + long doorgeven
			get: returns strLocation (voor lat + long: LatLong())
		*/
			if (!is_null($strLocation)) $this->strLocation = $strLocation; 
			if (!is_null($iLocationLat)) $this->latitude($iLocationLat);
			if (!is_null($iLocationLong)) $this->longitude($iLocationLong); 
			if (is_null($this->strLocation)) {
				$this->load();
			} 
			if (($this->strLocation != "") && $this->longitude() == 0 && $this->longitude()==0) {
				$arLoc = getXY($this->strLocation); 
				if (isset($arLoc["latitude"])) { 
					$this->latitude($arLoc["latitude"]);
					$this->longitude($arLoc["longitude"]); 
				}
			 
			}
			if (!user(me())->mailVerified()) return rubbish(($this->strLocation == "free") ? "" : $this->strLocation); 
			return ($this->strLocation == "free") ? "" : $this->strLocation; 
		} 
		public function LatLong() { // returns arra(iLat, iLong)
			if (is_null($this->iLocationLat) || is_null($this->iLocationLong)) $this->load();
			return array($this->iLocationLat, $this->iLocationLong); 	
		}
		
		public function latitude($iLocationLat = NULL) { // get / set location latitude
			if (!is_null($iLocationLat)) $this->iLocationLat = $iLocationLat; 
			if (is_null($this->iLocationLat)) $this->load();
			return $this->iLocationLat; 	
		}
		public function longitude($iLocationLong = NULL) { // get / set location longitude
			if (!is_null($iLocationLong)) $this->iLocationLong = $iLocationLong; 
			if (is_null($this->iLocationLong)) $this->load();
			return $this->iLocationLong; 	
		} 
		
		
		public function addFile($strFile) {
			if (is_null($this->arFiles)) $this->load();
			$this->arFiles[] = $strFile; 
			return $this->arFiles; 
		}
		public function files($strNew = NULL, $bAdd = TRUE) {
			if (is_null($this->arFiles)) $this->load();
			if (!is_null($strNew)) {
				if ($bAdd) {
					$this->arFiles[] = $strFile; // add item
				} else {
					$this->arFiles = array_diff($this->arFiles, array($strNew)); // remove item 
				}
			}
			return $this->arFiles; 
		}
		
		public function locationIMG($iWidth=270, $iHeight=300) {  // returns HTML (div) met Google-map (TODO: nu staat er geen check op al dan niet ingesteld zijn van locatie) 
			// 	https://developers.google.com/maps/documentation/staticmaps/?hl=nl&csw=1 
			//$strURL = "http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=13&size=600x300&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false"; // 600 x 300
			if ($this->location() == "fixed") { // deprecated
				$strURL = "http://maps.googleapis.com/maps/api/staticmap?center=" . ($this->iLocationLat) . "," . $this->iLocationLong . "&zoom=13&size=" . $iWidth . "x" . $iHeight . "&maptype=roadmap&markers=color:blue%7C" . $this->iLocationLat . "," . $this->iLocationLong . "&sensor=false"; 
				//return "<div class=\"locationbox\" style=\"background: url('" . cache($strURL, "png") . "'); \"></div>";  
                return "<img class=\"locationbox\" src=\"" . cache($strURL, "png") . "\" ></img>";  
			} else {
				if ($this->iLocationLat != 0 || $this->iLocationLong != 0) {
					$strURL = "http://maps.googleapis.com/maps/api/staticmap?center=" . ($this->iLocationLat+.005) . "," . $this->iLocationLong . "&zoom=13&size=" . $iWidth . "x" . $iHeight . "&maptype=roadmap&markers=color:blue%7C" . $this->iLocationLat . "," . $this->iLocationLong . "&sensor=false"; 
					//return "<div class=\"locationbox\" style=\"background: url('" . cache($strURL, "png") . "'); \"><span>" . $this->location() . "</span></div>";  
					return "<img class=\"locationbox\" src=\"" . cache($strURL, "png") . "\" ><span>" . $this->location() . "</span></img>";  
				} else return ""; // "<span>" . $this->location() . "</span>"; 
			}
		}
		
		/*public function addTimingStart($iTiming = NULL, $strStatus = "NEW") { /* datum toevoegen ($iTiming = unix time)
		(TODO: strStatus moet er niet staan in public function)
		*//*
			if (is_null($this->arTiming)) $this->load(); 
			if (!is_null($iTiming)) { 
				$this->arTiming[intval($iTiming)] = $strStatus; 
			} 
			return $this->arTiming; 
		} */
		
		public function addMoment($iDatum = NULL, $iStart = NULL, $iTijd = NULL, $strStatus = "NEW") { /* datum toevoegen ($iDatum = unix time, iStart en $iTijd = minuten)
		(TODO: strStatus moet er niet staan in public function)
		*/
			if (is_null($this->arMomenten)) {
				if ($strStatus == "DB") {
					$this->arMomenten = array(); 
				} else {
					$this->loadMomenten(); 
				}
			}
			if (isset($this->arMomenten[intval($iDatum)])) $strStatus = "REPLACE"; 
			$this->arMomenten[intval($iDatum)] = array(
				"start" => $iStart,  
				"tijd" => $iTijd, 
				"status" => $strStatus
			);  
			return $this->arMomenten; 
		} 
		
		public function getMoment($iDate) {
			if (is_null($this->arMomenten)) $this->loadMomenten(); 
			if (!isset($this->arMomenten[$iDate])) return FALSE; 
			if ($this->arMomenten[$iDate]["status"] == "DELETE") return FALSE; 
			return $this->arMomenten[$iDate]; 
		}
		 
		public function removeMoment($iDatum) {  // timing verwijderen ($iTiming = unix time)
			if (is_null($this->arMomenten)) $this->loadMomenten();  
			if (isset($this->arMomenten[intval($iDatum)])) {
				switch($this->arMomenten[intval($iDatum)]){
					case "NEW": 
						unset($this->arMomenten[intval($iDatum)]);
						break;
					default: 
						$this->arMomenten[intval($iDatum)]["status"] = "DELETE"; 
				} 
				return TRUE; 
			} else return FALSE;  
		} 
		
		public function data() { // returns Array van unix-times
			$arData = array();
			if (is_null($this->arMomenten)) $this->loadMomenten();  
			foreach ($this->arMomenten as $iTiming => $strStatus) {
				switch($strStatus) {
					case "DELETE": 
						break; 
					default: 
						$arData[] = $iTiming; 
				}	
			}	
			return $arData; 
		}
		
		
		public function timing($iTiming = NULL) { // get / set tijdsduur (uur)
			if (!is_null($iTiming)) $this->iTiming = intval($iTiming); 
			if (is_null($this->arMomenten)) $this->loadMomenten(); 
			$iTiming = 0; 
			foreach ($this->arMomenten as $arMoment) {
				if ($arMoment["status"] != "DELETE") $iTiming += $arMoment["tijd"];
			}
			/*
			$this->arMomenten[intval($iDatum)] = array(
				"start" => $iStart,  
				"tijd" => $iTijd, 
				"status" => $strStatus
			);  
			*/
			return $iTiming/60; // $this->iTiming; 
		}
		public function timingtype($strTiming = NULL) { // get / set timing type (TODO: ?)
			if (!is_null($strTiming)) $this->strTiming = $strTiming; 
			if (is_null($this->strTiming)) $this->load(); 
			return $this->strTiming; 
		}
		public function physical($iPhysical = NULL) { // get / set indicator "physical" (0-100)
			if (!is_null($iPhysical)) $this->iPhysical = intval($iPhysical); 
			if (is_null($this->iPhysical)) $this->load(); 
			return $this->iPhysical; 
		}
		public function mental($iMental = NULL) { // get / set indicator "mental" (0-100)
			if (!is_null($iMental)) $this->iMental = intval($iMental); 
			if (is_null($this->iMental)) $this->load(); 
			return $this->iMental; 
		}
		public function emotional($iEmotional = NULL) { // get / set indicator "emotional" (0-100)
			if (!is_null($iEmotional)) $this->iEmotional = intval($iEmotional); 
			if (is_null($this->iEmotional)) $this->load(); 
			return $this->iEmotional; 
		}
		public function social($iSocial = NULL) { // get / set indicator "social" (0-100)
			if (!is_null($iSocial)) $this->iSocial = intval($iSocial); 
			if (is_null($this->iSocial)) $this->load(); 
			return $this->iSocial; 
		}
		public function credits($iCredits = NULL) { // get / set aantal credits
			if (!is_null($iCredits)) $this->iCredits = intval($iCredits); 
			if (is_null($this->iCredits)) $this->load(); 
			return $this->iCredits; 
		}
		public function author($iAuthor = NULL) { // sets author (by ID)  / retreives author as class.user
			if (!is_null($iAuthor)) $this->iAuthor = intval($iAuthor); 
			if (is_null($this->iAuthor)) $this->load(); 
			return user($this->iAuthor); 
		}
		public function task($bTask = NULL) { // get / set "opdrachten" (TRUE) of "marktplaats" (FALSE)
			//if (!is_null($bTask)) $this->bTask = $bTask; 
			//if (is_null($this->bTask)) $this->load(); 
			return $this->type()->task(); 
		}
		public function state($iState = NULL) { /* get / set state 
			$iState = STATE_RECRUTE / STATE_SELECTED / STATE_FINISHED / STATE_DELETED 
		*/
			if (!is_null($iState)) $this->iState = $iState; 
			if (is_null($this->iState)) $this->load(); 
			return $this->iState; 
		}
		
		public function update() { // save 
			$oDB = new database();   
			if ($this->iID == 0) {
				$strSQL = "insert into tblMarket (author, createdby, groep, mtype, title, body, date, lastupdate, location, location_lat, location_long, timingtype, timing, physical, mental, emotional, social, credits, details, state, files) values(" . $this->iAuthor . ", " . me() . ", " . $this->iGroup . ", '" . ($this->type()->id()) . "', '" . $oDB->escape($this->strTitle) . "', '" . $oDB->escape($this->strBody) . "', '" . $this->iDate . "', '" . owaesTime() . "' , '" . $oDB->escape($this->strLocation) . "', '" . $oDB->escape($this->iLocationLat) . "', '" . $oDB->escape($this->iLocationLong) . "', '" . $this->strTiming . "', '" . $this->iTiming . "', '" . $this->physical() . "', '" . $this->mental() . "', '" . $this->emotional() . "', '" . $this->social() . "', '" . $this->iCredits . "', '" . $oDB->escape(json_encode($this->arDetails)) . "', '" . ($this->state()) . "',  '" . $oDB->escape(json_encode($this->arFiles)) . "'); "; 
				$oDB->execute($strSQL); 
				$this->iID = $oDB->lastInsertID();  
			} else { 
				$strSQL = "update tblMarket set lastupdate = '" . owaesTime() . "', author = " . $this->author()->id() . ", groep = " . $this->iGroup . ", mtype = '" . ($this->type()->id()) . "', title = '" . $oDB->escape($this->title()) . "', body = '" . $oDB->escape($this->body()) . "', location = '" . $oDB->escape($this->strLocation) . "', location_lat = '" . $this->iLocationLat . "', location_long = '" . $this->iLocationLong . "', timing = '" . $this->timing() . "', timingtype = '" . $this->timingtype() . "', physical = '" . $this->physical() . "', mental = '" . $this->mental() . "', emotional = '" . $this->emotional() . "', social = '" . $this->social() . "', credits = '" . $this->credits() . "', details = '" . $oDB->escape(json_encode($this->arDetails)) . "', state = '" . ($this->state()) . "', files = '" . $oDB->escape(json_encode($this->arFiles)) . "' where id = " . $this->iID . ";";  
				$oDB->execute($strSQL); 
			} 
			if (!is_null($this->arTags)) foreach ($this->arTags as $strTag=>$arDetails) {
				switch($arDetails["state"]){
					case "DB": 
						// no change
						break; 
					case "DELETE": 
						if ($arDetails["original"] == "DB") $oDB->execute("delete from tblMarketTags where market = '" . $this->iID . "' and tag = '" . $oDB->escape($strTag) . "';"); 
						break; 
					case "NEW":  
						if ($arDetails["original"] != "DB") $oDB->execute("insert into tblMarketTags (market, tag) values ('" . $this->iID . "', '" . $oDB->escape($strTag) . "');"); 
						break; 
					default: 
						echo ("VERKEERDE TAG-STATUS !! ");  
						vardump($this);  
				}
			}
			if (!is_null($this->arMomenten)) foreach ($this->arMomenten as $iDate=>$arDetails) {
				switch($arDetails["status"]) {
					case "DB": 
						// no change
						break; 
					case "DELETE":  
						$oDB->execute("delete from tblMarketDates where market = '" . $this->iID . "' and datum = '$iDate';"); 
						break; 
					case "NEW":  
						$oDB->execute("insert into tblMarketDates (market, datum, start, tijd) values ('" . $this->iID . "', '" . $iDate . "', '" . $arDetails["start"] . "', '" . $arDetails["tijd"] . "');"); 
						break; 
					case "REPLACE":  
						$oDB->execute("delete from tblMarketDates where market = '" . $this->iID . "' and datum = '$iDate';"); 
						$oDB->execute("insert into tblMarketDates (market, datum, start, tijd) values ('" . $this->iID . "', '" . $iDate . "', '" . $arDetails["start"] . "', '" . $arDetails["tijd"] . "');"); 
						break; 
					default: 
						echo ("VERKEERDE DATUMSTATUS !! ");  
						vardump($this);  
				}	
			}
			
		}
		
	}
	
