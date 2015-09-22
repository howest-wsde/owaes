<?php  
	class payment {  
		private $iMarket = NULL;
		private $iInitiator = NULL;
		private $iSender = NULL;
		private $iReceiver = NULL;
		private $iSenderGroup = 0;
		private $iReceiverGroup = 0;   
		private $iCredits = NULL;  
		private $iReason = 0;  
		private $iID = NULL;  
		private $bSigned = NULL; 
		private $bVoorschot = FALSE; 
		
		public function payment($arArguments = array()) {  // payment(array("sender"=>$x, "receiver"=>$y, "market"=>$z))
			foreach ($arArguments as $strKey=>$oValue) {
				switch(strtolower($strKey)) {
					case "sender": 
						$this->sender($oValue); 
						break; 
					case "receiver": 
						$this->receiver($oValue); 
						break; 
					case "sendergroup": 
						$this->sender(NULL, $oValue); 
						break; 
					case "receivergroup": 
						$this->receiver(NULL, $oValue); 
						break; 
					case "market": 
						$this->market($oValue); 
						break; 
					case "credits": 
						$this->credits($oValue); 
						break;  
					case "id": 
						$this->id($oValue); 
						break; 
					case "initiator": 
						$this->initiator($oValue); 
						break; 
					case "voorschot": 
						$this->voorschot($oValue); 
						break; 
					default: 
						error("class.payment : $strKey is ongeldig argument"); 
				}	
			} 
		}
		
		public function sender($iSender = NULL, $iSenderGroup = NULL){
			if (!is_null($iSender)) $this->iSender = $iSender; 
			if (!is_null($iSenderGroup)) $this->iSenderGroup = $iSenderGroup; 
			if (is_null($this->iSender)) $this->iSender = me(); 
			return $this->iSender; 	
		}
		
		public function initiator($iInitiator = NULL){
			if (!is_null($iInitiator)) $this->iInitiator = $iInitiator; 
			if (is_null($this->iInitiator)) $this->load(); 
			return $this->iInitiator; 	
		}
		
		public function receiver($iReceiver = NULL, $iReceiverGroup = NULL){
			if (!is_null($iReceiver)) $this->iReceiver = $iReceiver; 
			if (!is_null($iReceiverGroup)) $this->iReceiverGroup = $iReceiverGroup; 
			return $this->iReceiver; 
		}
		
		public function credits($iCredits = NULL){
			if (!is_null($iCredits)) $this->iCredits = abs($iCredits); 
			if (is_null($this->iCredits)) $this->load(); 
			return $this->iCredits; 
		}
		
		public function id($iID = NULL){
			if (!is_null($iID)) $this->iID = $iID; 
			return $this->iID; 	
		}
		
		public function voorschot($bValue = NULL){
			if (!is_null($bValue)) $this->bVoorschot = $bValue; 
			return $this->bVoorschot; 	
		}
		
		public function market($iMarket = NULL){
			if (!is_null($iMarket)) $this->iMarket = $iMarket; 
			return $this->iMarket; 	
		}
		
		public function reason($iReason = NULL){
			if (!is_null($iReason)) $this->iReason = $iReason; 
			return $this->iReason; 	
		}
		
		public function load() {
			$arWhere = array(); 
			$arWhere[] = "sender = " . $this->sender(); 
			$arWhere[] = "receiver = " . $this->receiver(); 
			$arWhere[] = "actief = 1";   
			if (!is_null($this->iID)) $arWhere[] = "id = " . $this->id(); 
			if (!is_null($this->iMarket)) {
				if ($this->market() == 0) {
					if (is_null($this->iID)) $arWhere[] = "id = 0";  // als er geen id gegeven wordt, en market = 0, dan mag er geen result zijn (anders kan er niet 2x geschonken worden aan zelfde persoon) 
				} else {
					$arWhere[] = "market = " . $this->iMarket; 
				}
			} else {
				if (is_null($this->iID)) $arWhere[] = "id = 0";  // als er geen id gegeven wordt, en market = 0, dan mag er geen result zijn (anders kan er niet 2x geschonken worden aan zelfde persoon) 
			}
			$oDB = new database("select * from tblPayments where " . implode(" and ", $arWhere) . " order by id desc limit 0, 1" , TRUE); 
			if ($oDB->record()) {
				$this->bSigned = TRUE; 
				$this->id($oDB->get("id")); 
				$this->credits($oDB->get("credits"));
				$this->initiator($oDB->get("initiator"));
				$this->market($oDB->get("market"));
				if ($oDB->get("voorschot")>0) {
					$this->market($oDB->get("voorschot"));
					$this->voorschot(TRUE); 
				}
			} else {
				$this->bSigned = FALSE; 
				$this->id(0); 
				if (is_null($this->iInitiator)) $this->initiator(me());
				if (is_null($this->iSender)) $this->sender(me());
				if (is_null($this->iCredits)) $this->credits(0); 
				if (is_null($this->iMarket)) $this->market(0);  
			} 
		}
		
		public function signed($bValue = NULL) {
			if (is_null($this->bSigned)) $this->load();  
			if (is_null($bValue)) { 
				return $this->bSigned; 
			} else { 
				if ($this->signed() != $bValue) {
					$iMarket = $this->voorschot() ? 0 : $this->market(); 
					$iVoorschot = $this->voorschot() ? $this->market() : 0;  
					if ($this->id() == 0) {
						if ($bValue) {
							$oDB = new database("insert into tblPayments 
									(datum, sender, receiver, sendergroup, receivergroup, initiator, credits, reason, link, 
										market, actief, voorschot) 
									values (" . owaesTime() . ", " . $this->sender() . ", 
										" . $this->receiver() . ", " . $this->iSenderGroup . ", 
										" . $this->iReceiverGroup . ", " . $this->initiator() . ", 
										" . $this->credits() . ", " . $this->reason() . ", 0, 
										" . $iMarket . ", 1, " . $iVoorschot . ");"
								, TRUE); 
							$oConversation = new conversation($this->receiver()); 
							$oConversation->add("Er werden " . $this->credits() . " " . settings("credits", "name", "x") . " overgedragen", $this->market());  
						}
					} else {
						$oDB = new database("update tblPayments set actief = " . ($bValue ? 1 : 0) . ", reason = " . $this->reason() . " where id = " . $this->id() . ";" , TRUE); 
					}
				} 
			}		
		}

		public function html($strTemplate = NULL) { 
			if (is_null($strTemplate)) {
				$strHTML = "<form class=\"pay\" action=\"pay.php\" method=\"post\">"; 
				if ($this->signed()) {
					$strHTML .= "<dl class=\"payment\">";
					if ($this->sender() == me()) { 
						$strHTML .= "<dt>betaald aan " . user($this->receiver())->getName() . ": " . $this->credits() . " " . settings("credits", "name", "x") . "</dt>"; 
					} else {
						$strHTML .= "<dt>betaald door " . user($this->sender())->getName() . ": " . $this->credits() . " " . settings("credits", "name", "x") . "</dt>"; 
					}
					$strHTML .= "</dl>"; 
				} else {
					if ($this->sender() == me()) { 
						$strHTML .= "
							<input type=\"hidden\" name=\"market\" value=\"" . $this->market() . "\" />
							<input type=\"hidden\" name=\"receiver\" value=\"" . $this->receiver() . "\" />
							<input type=\"hidden\" name=\"score\" value=\"\" />
						<fieldset>
										<dl class=>";
						$strHTML .= "<dt>Draag " . $this->credits() . " " . settings("credits", "name", "x") . " over naar " . user($this->receiver())->getName() . " voor dit item</dt>";    
						$strHTML .= "</dl>
										</fieldset> 
								"; 
					}
				} 
				$strHTML .= "</form>"; 
				return $strHTML; 	
			} else { 
				$strHTML = template($strTemplate);  
				preg_match_all("/\[([a-zA-Z0-9-_:#]+)\]/", $strHTML, $arResult);   // alle tags (zonder whitespace)
				if (isset($arResult[1])) foreach ($arResult[1] as $strTag){ 
					$strResult = $this->HTMLvalue($strTag);  
					if (!is_null($strResult)) $strHTML = str_replace("[$strTag]", $strResult, $strHTML); 
				} 
				return $strHTML; 	
			}
		}
		
		private function HTMLvalue($strTag) {
			switch($strTag) { 
				case "id": 
					return $this->id();  
				case "credits": 
					return ($this->sender() == me() ? "-":"") . $this->credits();  
				case "in-out": 
					return ($this->sender() == me()) ? "out" : "in";  
				case "owaes": 
					if ($this->market() == 0) {
						return "schenking";  
					} else {
						return owaesitem($this->market())->title();  
					}
				case "owaes:url": 
					if (($this->market() == 0) && (!$this->voorschot())) {
						return "#";  
					} else {
						return owaesitem($this->market())->url(); 
					} 
				case "img:src:30x30": 
					return ($this->sender() == me()) ? user($this->receiver())->getImage("30x30", FALSE) : user($this->sender())->getImage("30x30", FALSE);  
				default: 
					return NULL; 
			}
		}
		
	}
	


	