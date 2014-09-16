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
			if (is_null($this->arTags)) $this->load();
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
			if (is_null($this->arTags)) $this->load(); 	
			if ($strTag != "") {
				if (!isset($this->arTags[$strTag])) $this->arTags[$strTag] = array("original" => $strType); 
				$this->arTags[$strTag]["state"] = $strType; 
			}
		}
		public function removeTag($strTag) { // tag verwijderen 
			if (is_null($this->arTags)) $this->load(); 
			if (isset($this->arTags[$strTag])) $this->arTags[$strTag]["state"] = "DELETE"; 
		}
		
		
		public function load() { 
			$oDB = new database("select * from tblMarket where id = " . intval($this->iID) . ";", TRUE); 

			if ($oDB->length() == 1) {
				$oDBrecord = $oDB->record(); 
				$this->iDate = $oDBrecord["date"];  
				$this->iID = $oDBrecord["id"]; 
				$this->iLastupdate = $oDBrecord["lastupdate"]; 
				if (is_null($this->strTitle)) $this->title($oDBrecord["title"]); 
				if (is_null($this->strBody)) $this->body($oDBrecord["body"]); 
				if (is_null($this->iAuthor)) $this->author($oDBrecord["author"]); 
				if (is_null($this->iGroup)) $this->group( $oDBrecord["groep"]); 
	
				if (is_null($this->iCredits)) $this->credits($oDBrecord["credits"]);   
				if (is_null($this->iPhysical)) $this->physical($oDBrecord["physical"]);   
				if (is_null($this->iMental)) $this->mental($oDBrecord["mental"]);   
				if (is_null($this->iEmotional)) $this->emotional( $oDBrecord["emotional"]); 
				if (is_null($this->iSocial)) $this->social($oDBrecord["social"]);  
				if (is_null($this->iType)) $this->type($oDBrecord["mtype"]);     
//				if (is_null($this->bTask)) $this->task($oDBrecord["task"]==1);  
if (is_null($this->iState)) $this->state($oDBrecord["state"]);  
				if (is_null($this->iTiming)) $this->timing($oDBrecord["timing"]);  
				if (is_null($this->strTiming)) $this->timingtype($oDBrecord["timingtype"]);  
				if (is_null($this->strLocation)) $this->location($oDBrecord["location"], $oDBrecord["location_lat"], $oDBrecord["location_long"]); 
				if (is_null($this->arTags)) {
					$oTags = new database("select tag from tblMarketTags where market = " . intval($this->iID) . ";", TRUE); 
					$this->arTags = array();
					while ($oTags->nextRecord()){
						$this->addTag($oTags->get("tag"), "DB"); 
					} 
				} 
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
//				if (is_null($this->bTask)) $this->task(TRUE);   
				if (is_null($this->iState)) $this->state(STATE_RECRUTE);  
				if (is_null($this->iTiming)) $this->timing(0);   
				if (is_null($this->strTiming)) $this->timingtype("free");  
				if (is_null($this->strLocation)) $this->location("", 0, 0);  
				if (is_null($this->arTags)) $this->arTags = array();   
			} 	
			 
			$this->arMomenten = array(); 
			$oDB = new database("select * from tblMarketDates where market = " . intval($this->iID) . ";", TRUE);  
			while ($oDB->nextRecord()) { 
				 $this->addMoment($oDB->get("datum"), $oDB->get("start"), $oDB->get("tijd"), "DB");  
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
		
		/*public function getAuthor() {
			if (is_null($this->oAuthor)) $this->oAuthor = new user($this->iAuthor); 
			return $this->oAuthor; 
		}*/
		
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
			$arActions = array(); 
			$strHTML = $bFile ? content($strTemplate) : $strTemplate;  
			$strHTML = str_replace("[classes]", implode(" ", $this->classes()), $strHTML);
			$strHTML = str_replace("[title]", $this->title(), $strHTML);
			$strHTML = str_replace("[body]", nl2br($this->body()), $strHTML); 
			$strHTML = str_replace("[body:short]", nl2br(shorten($this->body(), 250, TRUE)), $strHTML); 
			$strHTML = str_replace("[link]", $this->getLink(), $strHTML);

			$strHTML = str_replace("[soortIcon]","<span class='" . $this->type()->iconclass() . "'></span>", $strHTML);
			$strHTML = str_replace("[iconclass]", $this->type()->iconclass(), $strHTML);
 
              
			if ($this->group()){ 
				$strHTML = str_replace("[author:type]", "group", $strHTML); 
				$strHTML = str_replace("[author]", $this->group()->getLink(), $strHTML);
				// $strHTML = str_replace("[author:img:60x60]", $this->group()->getImage("60x60", FALSE), $strHTML);
				//$strHTML = preg_replace('/\[author\:img\:([0-9]*x[0-9]*)\]/e', '$this->group()->getImage("$1", FALSE)', $strHTML);
				$strHTML = preg_replace_callback('/\[author\:img\:([0-9]*x[0-9]*)\]/', array(&$this, "imagegroupregreplace"), $strHTML); 


				$strHTML = str_replace("[author:url]", $this->group()->getURL(), $strHTML);
				$strHTML = str_replace("[author:key]", (($this->group()->alias() == "")?$this->group()->id():$this->group()->alias()), $strHTML); // of id als er geen username is
			} else {
				$strHTML = str_replace("[author:type]", "user", $strHTML); 
				$strHTML = str_replace("[author]", $this->author()->getLink(), $strHTML);
				//$strHTML = str_replace("[author:img:60x60]", $this->author()->getImage("60x60", FALSE), $strHTML);
				//$strHTML = preg_replace('/\[author\:img\:([0-9]*x[0-9]*)\]/e', '$this->author()->getImage("$1", FALSE)', $strHTML);
				$strHTML = preg_replace_callback('/\[author\:img\:([0-9]*x[0-9]*)\]/', array(&$this, "imageauthorregreplace"), $strHTML); 

				$strHTML = str_replace("[author:url]", $this->author()->getURL(), $strHTML);
				$strHTML = str_replace("[author:key]", (($this->author()->alias() == "")?$this->author()->iID:$this->author()->alias()), $strHTML); // of id als er geen username is
			}
            $strHTML = str_replace("[author:adress]",$this->location(),$strHTML);    
			      
			/*switch($this->timingtype()) {
				case "free": 
					$strHTML = str_replace("[data]", "vrij te kiezen", $strHTML);  
					break; 	
				case "tbc": 
					$strHTML = str_replace("[data]", "nog vast te leggen", $strHTML);  
					break; 	
				default: */
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
										$strSub .= "<li>willekeurige datum, gedurende " . ($oMoment["tijd"]/60) . "uur</li>"; 
									} else {
										$strSub .= "<li>willekeurige datum, van " . minutesTOhhmm($oMoment["start"]) . " tot " . minutesTOhhmm($oMoment["start"]+$oMoment["tijd"]) . "</li>"; 
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
										$strSub .= "<li>" . str_date($iDate, "datum") . " gedurende " . ($oMoment["tijd"]/60) . "uur</li>"; 
									} else {
										$strSub .= "<li>" . str_date($iDate, "datum") . " van " . minutesTOhhmm($oMoment["start"]) . " tot " . minutesTOhhmm($oMoment["start"]+$oMoment["tijd"]) . "</li>"; 
									} 
								} 
							}
							//$strSub .= "<li>" . str_date($iDate) . "</li>"; 
						}
						$strSub .= "</ul>"; 
						$strHTML = str_replace("[data]", $strSub, $strHTML);  
						// ************************************************************************
					} else {
						$strHTML = str_replace("[data]", "willekeurige datum", $strHTML);  
					}
					/*break; 	
			}*/
			$strHTML = str_replace("[data]", "vrij te kiezen", $strHTML); 
			$strHTML = str_replace("[timing]", $this->timing() . " uur", $strHTML);  
			switch ($this->location()) {
				case "":  
				case "free": 
					$strHTML = str_replace("[locationimg:100x100]", "", $strHTML); 
					break; 
				default: 	
					$strHTML = str_replace("[locationimg:100x100]", $this->locationIMG(), $strHTML);  
			}
			$strHTML = str_replace("[development]", $this->developmentBoxes(), $strHTML);  
			$strHTML = str_replace("[credits]", ($this->iCredits==0) ? "aantal credits n.o.t.k." : $this->iCredits . " credits", $strHTML); 
			if (instr("[subscribe]", $strHTML)) $strHTML = str_replace("[subscribe]", $this->subscriptionDiv(), $strHTML);  
			if (instr("[author:box]", $strHTML)) $strHTML = str_replace("[author:box]", $this->author()->userBox(), $strHTML);  
			switch($this->state()) {
				case STATE_SELECTED: 
					$strHTML = str_replace("[state]", "in uitvoering", $strHTML); 
					break; 
				case STATE_FINISHED:  
					$strHTML = str_replace("[state]", "afgesloten", $strHTML); 
					break; 
				case STATE_RECRUTE: 
					$strHTML = str_replace("[state]", "open", $strHTML);
					break;  
				default: 
					$strHTML = str_replace("[state]", "", $strHTML); 
					break; 
			}
			$arSubscriptions = $this->subscriptions();  
			if ($this->iAuthor != me()) {
				$iMyValue = (isset($arSubscriptions[me()])) ? $arSubscriptions[me()]->state() : SUBSCRIBE_CANCEL;
				if ($iMyValue == SUBSCRIBE_CONFIRMED) {
					//$arTransactions = $this->transactions(); 
					//if (isset($arTransactions[me()]))  {
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
					//}
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
			if (instr("[tags]", $strHTML)) {
				$arTags = array(); 
				foreach ($this->getTags() as $strTag) $arTags[] = "<span>" . htmlentities($strTag) . "</span>"; 
				$strHTML = str_replace("[tags]", implode("", $arTags), $strHTML);  
			}
            if (instr("[aantalInschrijvingen]",$strHTML)){
                $arSubscriptions = $this->subscriptions(); 
			    $strCount = (count($arSubscriptions)==1) ? "1 inschrijving " : count($arSubscriptions) . " inschrijvingen ";
                $strHTML= str_replace("[aantalInschrijvingen]",$strCount,$strHTML);
			  
            }
                
			$strHTML = str_replace("[actions]", implode("", $arActions), $strHTML);
						
			return $strHTML; 
		} 
		private function imageauthorregreplace(&$matches) { 
			return $this->author()->getImage($matches[1], FALSE);  
		} 
		private function imagegroupregreplace(&$matches) { 
			return $this->group()->getImage($matches[1], FALSE);  
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
			if (is_null($this->arMomenten)) $this->load(); 
			$this->arMomenten[intval($iDatum)] = array(
				"start" => $iStart,  
				"tijd" => $iTijd, 
				"status" => $strStatus
			);  
			return $this->arMomenten; 
		} 
		
		public function getMoment($iDate) {
			if (is_null($this->arMomenten)) $this->load(); 
			if (!isset($this->arMomenten[$iDate])) return FALSE; 
			if ($this->arMomenten[$iDate]["status"] == "DELETE") return FALSE; 
			return $this->arMomenten[$iDate]; 
		}
		
		/*public function removeTimingStart($iTiming) {  // timing verwijderen ($iTiming = unix time)
			if (is_null($this->arTiming)) $this->load(); 
			if (isset($this->arTiming[intval($iTiming)])) {
				switch($this->arTiming[intval($iTiming)]){
					case "NEW": 
						unset($this->arTiming[intval($iTiming)]);
						break;
					default: 
						$this->arTiming[intval($iTiming)] = "DELETE"; 
				}
				return TRUE; 
			} else return FALSE;  
		} */
		
		public function removeMoment($iDatum) {  // timing verwijderen ($iTiming = unix time)
			if (is_null($this->arMomenten)) $this->load(); 
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
			if (is_null($this->arMomenten)) $this->load();  
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
			if (is_null($this->arMomenten)) $this->load(); 
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
	
?>