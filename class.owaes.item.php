<?  
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
		private $strIMG = NULL; 
		private $oSubscriptions = NULL; 
		private $arSubscriptions = array(); 
		private $arTransactions = NULL; 
		private $iCredits = NULL; 
		private $bTask = NULL; 
		private $iState = NULL;  
		private $arTags = NULL; 
		private $iType = NULL; 
		 
		public function owaesitem($iID = 0) { 
			$this->iID = $iID;  
			$this->iDate = owaesTime(); 
			if ($iID == 0) { 
				$this->arMomenten = array();  
				$this->state(STATE_RECRUTE);
			}
		}
		 
		public function getTags() {
			if (is_null($this->arTags)) $this->loadTags(); ;
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
			$oDB = new database("select * from tblMarketDates where market in (" . implode(",", $arLoadedItems) . ");", TRUE); 
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
			if ($this->author()->id() == me()) {
				return "owaes-selecteer.php?owaes=" . $this->iID; 
			} else {
				return "owaes.php?owaes=" . $this->iID; 
			} 
		}
		public function link($strHTML) { // maakt een link naar de detailspagina van $strHTML (bv. "test" wordt "<a href='link.html'>test</a>"
			return "<a href=\"" . $this->getLink() . "\" class=\"owaes\">" . $strHTML . "</a>"; 
		}
		
		public function getImage($oPreset = "thumbnail") {  // doet nu niets. TODO: mag waarschijnlijk weg 
			switch($oPreset) {
				case "thumbnail": 
				default: 
					
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
				}
			}
			return $arResult; 
		}
		 
		
		public function subscriptionDiv() { // returns de html met de "schrijf in / onderhandel"-knop (of status-tekst)
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
								case SUBSCRIBE_NEGOTIATE: 
									$strSubscription .= "<p>Inschrijven niet meer mogelijk, uw inschrijving werd nog niet beoordeeld</p>";
									break; 
								case SUBSCRIBE_CONFIRMED: 
									$strSubscription .= "<p>Inschrijven niet meer mogelijk, uw inschrijving werd bevestigd</p>";
									break; 
								case SUBSCRIBE_DECLINED:
									$strSubscription .= "<p>Inschrijven niet meer mogelijk, uw inschrijving werd afgezen</p>";
									break; 
								default: 
									$strSubscription .= "<p>Inschrijven niet meer mogelijk</p>"; 
							} 
							break;
						default: // STATE_RECRUTE
							$strSubscription .= $strCount; 
							switch($iMyValue) {
								case SUBSCRIBE_SUBSCRIBE: 
									$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_CANCEL . "\" class=\"btn btn-default btn-sm pull-right\"><span class=\"icon icon-close\"></span>uitschrijven</a> ";
									//$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_NEGOTIATE . "\" class=\"subscribe\">onderhandel</a> ";
									break; 
								case SUBSCRIBE_NEGOTIATE: 
									$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_SUBSCRIBE . "\" class=\"btn-sm btn btn-default pull-right\"><span class=\"icon icon-inschrijven\"></span>schrijf in</a> ";
									//$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_CANCEL . "\" class=\"active subscribe\">onderhandelend</a> ";
									break; 
								case SUBSCRIBE_CONFIRMED: 
									$strSubscription .= "<p>Uw inschrijving werd bevestigd</p>";
									// geen break want inschrijven opnieuw mogelijk
									break; 
								case SUBSCRIBE_DECLINED:
									$strSubscription .= "<p>Uw inschrijving werd afgezen</p>";
								default: 
									$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_SUBSCRIBE . "\" class=\"btn btn-default btn-sm pull-right\"><span class=\"icon icon-inschrijven\"></span>schrijf in</a> ";
									//$strSubscription .= "<a href=\"subscribe.php?m=" . $this->iID . "&t=" . SUBSCRIBE_NEGOTIATE . "\" class=\"subscribe\">onderhandel</a> ";
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
				case STATE_RECRUTE: 
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
					$oNotification->key("subscription." .  $this->id()); 
					$oNotification->link(fixPath($this->getLink())); 
					$oNotification->send(); 
					break; 
			}
			/*
			$oDB = new database("update tblMarketSubscriptions set active = 0 where market = " . $this->iID . " and user = " . $iUser . ";", TRUE); 
			$oDB = new database("insert into tblMarketSubscriptions (market, user, mtype, doneby, clickdate) values (" . $this->iID . ", " . $iUser . ", " . $iType . ", " . $iDoneBy . ", " . owaesTime() . "); ", TRUE); 
			/*
			if ($iType == SUBSCRIBE_CONFIRMED) {
				$oTransaction = new transaction($this->id(), $iUser); 
				$oTransaction->update(); 
			}
*/			
		}
		/*
		public function transactions($iSpecificUser = NULL) { /* 
		returns array van class.transaction's
		als $iSpecificUser gedefinieerd: returns FALSE / class.transaction
		indien niet gedefinieerd: returns array[met user-ID's als keys] van class.transaction's
		*/ /*
			if (is_null($this->arTransactions))	{
				$this->arTransactions = array(); 
				foreach($this->subscriptions() as $iUser=>$oSubscription) {
					if ($oSubscription->state() == SUBSCRIBE_CONFIRMED) {
						$oTransaction = new transaction($this->id(), $iUser); 
						$this->arTransactions[$iUser] = $oTransaction; 
					}
				}
			} 
			if (!is_null($iSpecificUser)) return (isset($this->arTransactions[$iSpecificUser])?$this->arTransactions[$iSpecificUser]:FALSE); 
			return $this->arTransactions; 
		}*/
		 
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
			return $arClasses; 
		}
		
		public function HTML($strTemplate, $bFile = TRUE) { // vraagt pad van template en returns de html met replaced [tags]  
			$strHTML = $bFile ? content($strTemplate) : $strTemplate;  
			
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
					if ($oSubscription->state() == SUBSCRIBE_CONFIRMED) { // SUBSCRIBE_CANCEL, SUBSCRIBE_SUBSCRIBE, SUBSCRIBE_NEGOTIATE, SUBSCRIBE_CONFIRMED, SUBSCRIBE_DECLINED 
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
									"to" => $oPayment->receiver(), 
									"credits" => 0,   
								); 
							} else { // ik moet nog ontvangen
								$arActions[] = array(
									"type" => "remind", 
									"reason" => "transaction",  
									"to" => $oPayment->sender(), 
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
		
		private function HTMLvalue($strTag) {
			switch($strTag) { 
				case "id": 
					return $this->id();  
				case "classes": 
					return implode(" ", $this->classes());  
				case "title": 
					return $this->title();  
				case "body": 
					return nl2br($this->body());  
				case "body:short": 
					return nl2br(shorten($this->body(), 250, TRUE));  
				case "link": 
					return $this->getLink(); 
				case "iconclass": 
					return $this->type()->iconclass(); 
				case "soortIcon": 
					return "<span class='" . $this->type()->iconclass() . "'></span>"; 
				case "author:type": 
					return ($this->group()) ? "group" : "user"; 
				case "author": 
					return ($this->group()) ? $this->group()->getLink() : $this->author()->getLink(); 
				case "author:url": 
					return ($this->group()) ? $this->group()->getURL() : $this->author()->getURL(); 
				case "author:key": 
					if ($this->group()) {
						return (($this->group()->alias() == "")?$this->group()->id():$this->group()->alias());  // of id als er geen username is
					} else {
						return (($this->author()->alias() == "")?$this->author()->iID:$this->author()->alias());  // of id als er geen username is
					} 
				case "author:adress": 
					return $this->location(); 
				case "data": 
					if (count($this->data()) > 0) {
						$strSub = "<ul class=\"data\">"; 
						foreach ($this->data() as $iDate) {
							$oMoment = $this->getMoment($iDate);  
							if ($iDate == 0) { // om het even welke dag
								if ($oMoment["tijd"] == 0) {
									if ($oMoment["start"] == 0) {
										$strSub .= "<li>willekeurige datum</li>"; 
									} else {
										$strSub .= "<li>willekeurige datum, om " . minutesTOhhmm($oMoment["start"]) . "</li>"; 
									}
								} else {
									if ($oMoment["start"] == 0) {
										$strSub .= "<li>willekeurige datum, gedurende " . minutesTOhhmm($oMoment["tijd"]) . "</li>"; 
									} else {
										if ($oMoment["start"]+$oMoment["tijd"] > 60*24) {
											$strSub .= "<li>willekeurige datum, vanaf " . minutesTOhhmm($oMoment["start"]) . 
															"  gedurende " . minutesTOhh($oMoment["tijd"]) . "</li>"; 
										} else {
											$strSub .= "<li>willekeurige datum, van " . minutesTOhhmm($oMoment["start"]) . 
															" tot " . minutesTOhhmm($oMoment["start"]+$oMoment["tijd"]) . "</li>"; 
										}
									} 
								} 
							} else {
								if ($oMoment["tijd"] == 0) {
									if ($oMoment["start"] == 0) {
										$strSub .= "<li>" . str_date($iDate, "datum") . "</li>"; 
									} else {
										$strSub .= "<li>" . str_date($iDate, "datum") . " om " . minutesTOhhmm($oMoment["start"]) . "</li>"; 
									}
								} else {
									if ($oMoment["start"] == 0) {
										$strSub .= "<li>" . str_date($iDate, "datum") . " gedurende " . minutesTOhh($oMoment["tijd"]) . "</li>"; 
									} else {
										if ($oMoment["start"]+$oMoment["tijd"] > 60*24) {
											$strSub .= "<li>" . str_date($iDate, "datum") . " vanaf " . minutesTOhhmm($oMoment["start"]) . 
														" gedurende " . minutesTOhh($oMoment["tijd"]) . "</li>"; 
										} else {
											$strSub .= "<li>" . str_date($iDate, "datum") . " van " . minutesTOhhmm($oMoment["start"]) . 
														" tot " . minutesTOhhmm($oMoment["start"]+$oMoment["tijd"]) . "</li>"; 
										}
									} 
								} 
							} 
						}
						$strSub .= "</ul>"; 
						return $strSub; 
					} else {
						return "willekeurige datum"; 
					}
					return "vrij te kiezen"; 
				case "timing": 
					return $this->timing();
				case "locationimg:100x100": 
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
					return ($this->iCredits==0) ? "aantal credits n.o.t.k." : $this->iCredits . " credits"; 
				case "subscribe":
					return $this->subscriptionDiv();
				case "author:box":
					return $this->author()->userBox(); 
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
				case "aantalInschrijvingen":  
					$arSubscriptions = $this->subscriptions(); 
					return (count($arSubscriptions)==1) ? "1 inschrijving " : count($arSubscriptions) . " inschrijvingen ";
				case "actions":  
					$arActions = array(); 
					$arSubscriptions = $this->subscriptions();  
					if ($this->iAuthor != me()) {
						$iMyValue = (isset($arSubscriptions[me()])) ? $arSubscriptions[me()]->state() : SUBSCRIBE_CANCEL;
						if ($iMyValue == SUBSCRIBE_CONFIRMED) { 
							$oPayment = $arSubscriptions[me()]->payment();   
							if (!$oPayment->signed()) { 
								if ($oPayment->sender() == me()) {
									$arActions[] = "<a href=\"" . fixPath("owaes-transactie.ajax.php?owaes=" . $this->id()) . "\" class=\"transactie\"><img src=\"" . fixPath("img/handshake.png") . "\" alt=\"start transactie\" align=\"right\" /></a>"; 
								} else {
									
									if (filename(FALSE) != "owaes.php") $arActions[] = "<a href=\"" . $this->getLink() . "\"><img src=\"" . fixPath("img/contact.png") . "\" alt=\"neem contact op\" align=\"right\" /></a>"; 
								}
								$arActions[] = $oPayment->html(); 
							} else {
								$arActions[] = "credits-overdracht OK"; 
								$oRating = $arSubscriptions[me()]->rating(me()); 
								if ($oRating->stars()) {
									$arActions[] =  ("<div>" . $oRating->html() . "</div>"); 
								} else {
									$arActions[] =  ("<div>" . $oRating->html() . "</div>"); 	
								}
								
							}   
						} 
						if (admin()) $arActions[] = "<a href=\"" . fixPath("owaesadd.php?edit=" . $this->id()) . "\"><img src=\"" . fixPath("img/edit.png") . "\" alt=\"aanpassen\" class=\"btn btn-default btn-sm pull-right edit\" align=\"right\" /></a>"; 
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
							if ($this->task()) { // ik moet betalen
								if ($iConfirmed > $iPayed) {
									$arActions[] = "<a href=\"" . fixPath("owaes-transactie.ajax.php?owaes=" . $this->id()) . "\" class=\"transactie\"><img src=\"" . fixPath("img/handshake.png") . "\" alt=\"start transactie\" align=\"right\" /></a>"; 
								} else { 
									if ($iConfirmed > 0) $arActions[] = "credits-overdracht allemaal OK"; 
								}
							} else { // ik moet ontvangen
								if ($iConfirmed > $iPayed) {
									$arActions[] = "TODO: ik moet nog van sommige betaling krijgen";  
								} else {
									$arActions[] = "credits-overdracht allemaal OK"; 
								}
							} 	
							foreach ($arSubscriptions as $iUser=>$oSubscription) {
								if ($oSubscription->state() == SUBSCRIBE_CONFIRMED) { 
									if ($oSubscription->payment()->signed()) {
										$oRating = $oSubscription->rating(me()); 
										if ($oRating->stars()) {
											$arActions[] =  ("<li>" . $oRating->html() . "</li>"); 
										} else {
											$arActions[] =  ("<li>" . $oRating->html() . "</li>"); 	
										} 
									} else {
										$arActions[] = $oSubscription->payment()->html(); 
									}
								} 
							}
						}
						$arActions[] = "<a href=\"" . fixPath("owaesadd.php?edit=" . $this->id()) . "\"><img class=\"btn btn-default btn-sm pull-right\" src=\"" . fixPath("img/edit.png") . "\" alt=\"aanpassen\" align=\"right\" /></a>"; 
					}
					return implode("", $arActions);
 
 
				default: 
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
			return $this->strTitle; 
		}
		public function body($strBody = NULL) { // get / set description 
			if (!is_null($strBody)) $this->strBody = $strBody; 
			if (is_null($this->strBody)) $this->load();
			return $this->strBody; 
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
		
		public function locationIMG($iWidth=270, $iHeight=300) {  // returns HTML (div) met Google-map (TODO: nu staat er geen check op al dan niet ingesteld zijn van locatie) 
			// 	https://developers.google.com/maps/documentation/staticmaps/?hl=nl&csw=1 
			//$strURL = "http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=13&size=600x300&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false"; // 600 x 300
			if ($this->location() == "fixed") { // deprecated
				$strURL = "http://maps.googleapis.com/maps/api/staticmap?center=" . ($this->iLocationLat) . "," . $this->iLocationLong . "&zoom=13&size=" . $iWidth . "x" . $iHeight . "&maptype=roadmap&markers=color:blue%7C" . $this->iLocationLat . "," . $this->iLocationLong . "&sensor=false"; 
				//return "<div class=\"locationbox\" style=\"background: url('" . cache($strURL, "png") . "'); \"></div>";  
                return "<img class=\"locationbox\" src=\"" . cache($strURL, "png") . "\" ></img>";  
			} else {
				$strURL = "http://maps.googleapis.com/maps/api/staticmap?center=" . ($this->iLocationLat+.005) . "," . $this->iLocationLong . "&zoom=13&size=" . $iWidth . "x" . $iHeight . "&maptype=roadmap&markers=color:blue%7C" . $this->iLocationLat . "," . $this->iLocationLong . "&sensor=false"; 
				//return "<div class=\"locationbox\" style=\"background: url('" . cache($strURL, "png") . "'); \"><span>" . $this->location() . "</span></div>";  
                return "<img class=\"locationbox\" src=\"" . cache($strURL, "png") . "\" ><span>" . $this->location() . "</span></img>";  
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
				$strSQL = "insert into tblMarket (author, groep, mtype, title, body, date, lastupdate, img, location, location_lat, location_long, timingtype, timing, physical, mental, emotional, social, credits, details, state) values(" . $this->iAuthor . ", " . $this->iGroup . ", '" . ($this->type()->id()) . "', '" . $oDB->escape($this->strTitle) . "', '" . $oDB->escape($this->strBody) . "', '" . $this->iDate . "', '" . owaesTime() . "' , 'img', '" . $oDB->escape($this->strLocation) . "', '" . $oDB->escape($this->iLocationLat) . "', '" . $oDB->escape($this->iLocationLong) . "', '" . $this->strTiming . "', '" . $this->iTiming . "', '" . $this->physical() . "', '" . $this->mental() . "', '" . $this->emotional() . "', '" . $this->social() . "', '" . $this->iCredits . "', 'details', '" . ($this->state()) . "'); "; 
				$oDB->execute($strSQL); 
				$this->iID = $oDB->lastInsertID();  
			} else { 
				$strSQL = "update tblMarket set lastupdate = '" . owaesTime() . "', author = " . $this->author()->id() . ", groep = " . $this->iGroup . ", mtype = '" . ($this->type()->id()) . "', title = '" . $oDB->escape($this->title()) . "', body = '" . $oDB->escape($this->body()) . "', img = 'img', location = '" . $oDB->escape($this->strLocation) . "', location_lat = '" . $this->iLocationLat . "', location_long = '" . $this->iLocationLong . "', timing = '" . $this->timing() . "', timingtype = '" . $this->timingtype() . "', physical = '" . $this->physical() . "', mental = '" . $this->mental() . "', emotional = '" . $this->emotional() . "', social = '" . $this->social() . "', credits = '" . $this->credits() . "', details = 'details', state = '" . ($this->state()) . "' where id = " . $this->iID . ";";  
				$oDB->execute($strSQL); 
			} 
			foreach ($this->arTags as $strTag=>$arDetails) {
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
			foreach ($this->arMomenten as $iDate=>$arDetails) {
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
					default: 
						echo ("VERKEERDE DATUMSTATUS !! ");  
						vardump($this);  
				}	
			}
			
		}
		
	}
	
