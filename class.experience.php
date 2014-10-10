<?php  
	class experience { 
		private $iUser = 0; 
		private $arDetails = array(); 
		private $arTotal = NULL; 
		private $iLevel = NULL;
		private $strKey = NULL;  
		
		public function experience($iUser = NULL) { 
			$this->user( (is_null($iUser)) ? me() : $iUser ); 
		}
		
		public function user($iUser = NULL) { // get / set user 
			if (!is_null($iUser)) {
				$this->iUser = $iUser; 
			} else return user($this->iUser);  
		}
		
		public function sleutel($strKey = NULL) { // get / set key (een key is leeg of uniek, gebruik een key als een experience maar 1x gegeven mag worden aan een user: bv. "owaes.30" of "quest.8")
			if (!is_null($strKey)) $this->strKey = $strKey; 
			return $this->strKey;  
		}
		
		public function detail($strKey, $strValue = NULL) { // get / set detail
			if (!is_null($strValue)) $this->arDetails[$strKey] = $strValue; 
			return (isset($this->arDetails[$strKey])) ? $this->arDetails[$strKey] : NULL; 
		}
		
		public function add($iNumber, $bConfirmed = FALSE) { // experience toevoegen (standaard niet confirmed)
			$arLevels = settings("levels"); 
			$iLevel = $this->level(); 
			$iMultiplier = isset($arLevels[$iLevel]["multiplier"]) ? $arLevels[$iLevel]["multiplier"] : 1; 
			$iNumber *= $iMultiplier; 

			$oDB = new database(); 
			
			$strKey = (is_null($this->sleutel()) ? "" : $this->sleutel()); 
			if ($strKey != "") {
				$strSQL = "select * from tblExperience where user = '" . $this->iUser . "' and idk = '" . $this->sleutel() . "';"; 
				$oDB->execute($strSQL);  
				if ($oDB->length() > 0) return FALSE; 
			}
			 
			$strSQL = "insert into tblExperience (idk, user, experience, datum, details, confirmed) values ('" . $strKey . "', '" . $this->iUser . "', '" . $iNumber . "', '" . owaestime() . "', '" . $oDB->escape(json_encode($this->arDetails)) . "', '" . ($bConfirmed?1:0) . "'); ";
			$oDB->execute($strSQL);  
			if (isset($this->arTotal["all"])) $this->arTotal["all"] += $iNumber; 
			if (isset($this->arTotal["confirmed"]) && $bConfirmed) $this->arTotal["confirmed"] += $iNumber; 
		}
		
		public function level($bShowNotConfirmed = FALSE) { // returns huidige level (of eventueel volgende met parameter TRUE)
			$iExp = $this->total($bShowNotConfirmed); 

			$this->iLevel = 0; 
			foreach (settings("levels") as $iLevel=>$arSettings) {
				if (($iExp >= $arSettings["threshold"]) && ($iLevel > $this->iLevel)) $this->iLevel = $iLevel;  
			} 
			return $this->iLevel; 
		}
		
		public function leveltreshold($bNext = TRUE) {  // returns experience nodig voor volgende level (bNext = false > vorige level)
			$arLevels = settings("levels"); 
			if ($bNext) {
				return (isset($arLevels[$this->level()+1])) ? $arLevels[$this->level()+1]["threshold"] : $this->total(TRUE);  
			} else {
				return (isset($arLevels[$this->level()])) ? $arLevels[$this->level()]["threshold"] : $this->total(TRUE);  
			}
		}
		
		public function confirm() { 
			$oDB = new database(); 
			$strSQL = "update tblExperience set confirmed = 1 where user = '" . $this->iUser . "' and confirmed = 0; ";
			$oDB->execute($strSQL); 
			if (isset($this->arTotal["confirmed"])) { 
				if (isset($this->arTotal["all"])) {
					$this->arTotal["confirmed"] = $this->arTotal["all"]; 
				} else unset($this->arTotal["confirmed"]); 
			}
		}
		
		public function total($bShowNotConfirmed = FALSE, $iValue = NULL) { // als parameter bShowNotConfirmed == TRUE > ook punten die nog niet bevestigd werden door gebruiker
			$strKey = $bShowNotConfirmed ? "all" : "confirmed"; 
			if (!is_null($iValue)) $this->arTotal[$strKey] = $iValue; 
			if (!isset($this->arTotal[$strKey])) {
				$oDB = new database();
				if ($bShowNotConfirmed) { 
					$oDB->execute("select round(sum(experience)) as totaal from tblExperience where user = " . $this->iUser . ";"); 
					$this->arTotal["all"] = intval($oDB->get("totaal")); 
				} else {

					$arUsers = loadedUsers();  // voert query uit voor alle users die in memory zitten
					if (!in_array($this->iUser, $arUsers)) $arUsers[] = $this->iUser;  
					foreach ($arUsers as $iUser) user($iUser)->experience()->total(FALSE, 0);
					
					$oDB->execute("select user, round(sum(experience)) as totaal from tblExperience where user in (" . implode(",", $arUsers) . ") and confirmed = 1 group by user;"); 
					while ($oDB->nextRecord()) { 
						user($oDB->get("user"))->experience()->total(FALSE, intval($oDB->get("totaal"))); 
					} 
				} 
			}  
			return $this->arTotal[$strKey]; 
		}

	} 
	 
	