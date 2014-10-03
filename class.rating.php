<?php  
	class rating {  
		private $iMarket = NULL; 
		private $iSender = NULL;
		private $iReceiver = NULL;   
		private $iStars = NULL;     
		private $strComment = NULL;   
		private $iID = NULL;   
		private $bRated = NULL; 
		
		public function rating($arArguments) {
			foreach ($arArguments as $strKey=>$oValue) {
				switch(strtolower($strKey)) {
					case "sender": 
						$this->sender($oValue); 
						break; 
					case "receiver": 
						$this->receiver($oValue); 
						break; 
					case "market": 
						$this->market($oValue); 
						break; 
					case "stars": 
						$this->stars($oValue); 
						break;  
					case "id": 
						$this->id($oValue); 
						break;  
					default: 
						error("class.rating : $strKey is ongeldig argument"); 
				}	
			} 
		}
		
		public function sender($iSender = NULL){
			if (!is_null($iSender)) $this->iSender = $iSender; 
			return $this->iSender; 	
		}
		 
		
		public function receiver($iReceiver = NULL){
			if (!is_null($iReceiver)) $this->iReceiver = $iReceiver; 
			return $this->iReceiver; 	
		}
		
		public function stars($iStars = NULL){
			if (!is_null($iStars)) $this->iStars = $iStars; 
			if (is_null($this->iStars)) $this->load(); 
			return ($this->iStars==-1) ? FALSE : $this->iStars; 	
		}
		
		public function comment($strComment = NULL){
			if (!is_null($strComment)) $this->strComment = $strComment; 
			if (is_null($this->strComment)) $this->strComment(); 
			return $this->strComment; 	
		}
		
		public function id($iID = NULL){
			if (!is_null($iID)) $this->iID = $iID; 
			return $this->iID; 	
		}
		
		public function market($iMarket = NULL){
			if (!is_null($iMarket)) $this->iMarket = $iMarket; 
			return $this->iMarket; 	
		}
		 
		public function load() {
			$arWhere = array(); 
			$arWhere[] = "sender = " . $this->sender(); 
			$arWhere[] = "receiver = " . $this->receiver();  
			$arWhere[] = "actief = 1";  
			if (!is_null($this->iID)) $arWhere[] = "id = " . $this->id(); 
			if (!is_null($this->iMarket)) $arWhere[] = "market = " . $this->iMarket; 
			$oDB = new database("select * from tblStars where " . implode(" and ", $arWhere) . " order by id desc limit 0, 1" , TRUE);  
			if ($oDB->record()) {
				$this->bRated = TRUE; 
				$this->id($oDB->get("id")); 
				$this->stars($oDB->get("stars"));
				$this->comment($oDB->get("comment")); 
			} else {
				$this->bRated = FALSE; 
				$this->id(0);  
				if (is_null($this->iStars)) $this->stars(-1); 
				if (is_null($this->strComment)) $this->comment(""); 
			} 
		}
		
		public function rated($bValue = NULL) {
			if (is_null($this->bRated)) $this->load();  
			if (is_null($bValue)) { 
				return $this->bRated; 
			} else { 
				if ($this->rated() != $bValue) {
					if ($this->id() == 0) {
						if ($bValue) {
							$oDB = new database(); 
							$oDB->execute("insert into tblStars (datum, sender, receiver, stars, comment, market, actief) values (" . owaesTime() . ", " . $this->sender() . ", " . $this->receiver() . ", " . $this->stars() . ", '" . $oDB->escape($this->comment()) . "', " . $this->market() . ", 1);"); 
							$oConversation = new conversation($this->receiver()); 
							$oConversation->add("U kreeg een score van " . (($this->stars()==1) ? "1 ster" : ($this->stars() . " sterren")) . "  voor deze opdracht", $this->market());  
							$oConversation->add($this->comment(), $this->market());  
						}
					} else {
						$oDB = new database(); 
						$oDB->execute("update tblStars set stars = " . $this->stars() . ", comment = '" . $oDB->escape($this->comment()) . "', actief = " . ($bValue ? 1 : 0) . " where id = " . $this->id() . ";"); 
					}
				} 
				$this->bRated = $bValue; 
			}		
		} 
		
		public function html() { 
			$strHTML = "<form class=\"rating\" action=\"rate.php\" method=\"post\">"; 
			if ($this->rated()) {
				$strHTML .= "<dl class=\"rating\">";
				if ($this->sender() == me()) { 
					$strHTML .= "<dt>gegeven aan " . user($this->receiver())->getName() . ":</dt>"; 
				}
				for ($i=1; $i<=$this->stars(); $i++) {
					$strHTML .= "<dd></dd>"; 	
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
					$strHTML .= "<dt>Beoordeel " . user($this->receiver())->getName() . " voor deze opdracht</dt>";  
					for ($i=1; $i<=5; $i++) { 
						$strHTML .= "<dd class=\"stars" . $i . "\"><a href=\"#rate" . $i . "\" rel=\"$i\" title=\"Beoordeel met $i/5\" class=\"rate\">$i</a></dd>"; 	
					} 
					$strHTML .= "</dl>
									</fieldset>
									<fieldset>
										<input type=\"text\" name=\"comment\" value=\"\" />
										<input type=\"submit\" value=\"opslaan\" />
									</fieldset> 
							"; 
				}
			} 
			$strHTML .= "</form>"; 
			return $strHTML; 	
		}
	}
	
?> 