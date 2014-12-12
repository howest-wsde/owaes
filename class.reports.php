<?php 
	class reports { 
		private $arReports = NULL; 
	
		public function reports() {
		}
		 
		public function getList() {
			if (is_null($this->arReports)) {
				$arReports = array(); 
				$oDB = new database(); 
				$oDB->execute("select * from tblAbuse order by id desc; "); 
				while ($oDB->nextRecord()) {
					$oReport = new report(); 
					$oReport->loadRecord($oDB->record()); 
					$arReports[] = $oReport; 
				}
				$this->arReports = $arReports; 
			}
			return $this->arReports; 
		}
		 
		
		public function __toArray() {
			return $this->getList(); 
		}
	} 

	class report {
		private $iID = 0; 
		private $iReporter = NULL;  
		private $iCreated = NULL; 
		private $iUser = NULL; 
		private $iMarket = NULL;  
		private $strReason = NULL;  
		private $arData = NULL; 
		
		public function report($strReason = NULL) {
			if (!is_null($strReason)) $this->reason($strReason); 
			$this->iCreated = owaestime(); 
			$this->reporter(me()); 
		} 
		
		public function data($strKey = NULL, $strValue = NULL) {
			if (is_null($this->arData)) $this->arData = array(); 
			if (!is_null($strKey)) {
				if (!is_null($strValue)) $this->arData[$strKey] = $strValue; 
				return isset($this->arData[$strKey]) ? $this->arData[$strKey] : FALSE; 
			} else {
				return $this->arData; 
			}
		}
		
		public function user($iUser = NULL) {
			if (!is_null($iUser)) $this->iUser = $iUser; 	
			return $this->iUser; 
		}
		
		public function timestamp() { 	
			return $this->iCreated; 
		}
		
		public function market($iMarket = NULL) {
			if (!is_null($iMarket)) $this->iMarket = $iMarket; 	
			return $this->iMarket; 
		}
		
		public function reason($strReason = NULL) {
			if (!is_null($strReason)) $this->strReason = $strReason; 	
			return $this->strReason; 
		}
		
		public function reporter($iReporter = NULL) {
			if (!is_null($iReporter)) $this->iReporter = $iReporter; 	
			return $this->iReporter; 
		}
		
		public function loadRecord($oRecord) { 
			$this->iID = $oRecord["id"]; 
			$this->iReporter = $oRecord["reporter"]; 
			$this->strReason = $oRecord["reason"]; 
			$this->iUser = $oRecord["user"]; 
			$this->iMarket = $oRecord["market"];  
			$this->iCreated = $oRecord["created"]; 
			$this->arData = json_decode($oRecord["data"], TRUE); 
		}
		 
		
		public function update() {
			$arVelden = array(
				"created" => $this->iCreated, 
			); 
			if (!is_null($this->iReporter)) $arVelden["reporter"] = $this->iReporter; 
			if (!is_null($this->iUser)) $arVelden["user"] = $this->iUser; 
			if (!is_null($this->strReason)) $arVelden["reason"] = $this->strReason; 
			if (!is_null($this->iMarket)) $arVelden["market"] = $this->iMarket; 
			if (!is_null($this->iCreated)) $arVelden["created"] = $this->iCreated; 
			if (!is_null($this->arData)) $arVelden["data"] = json_encode($this->arData); 
		
			$oDB = new database();  
			if ($this->iID == 0) {
				$arFields = array(); 
				$arValues = array(); 
				foreach ($arVelden as $strField=>$strValue) {
					$arFields[] = $strField; 
					$arValues[] = "'" . $oDB->escape($strValue) . "'"; 
				} 
				$oDB->sql("insert into tblAbuse (" . implode(",", $arFields) . ") values (" . implode(",", $arValues) . "); "); 
				$oDB->execute(); 
				$this->iID = $oDB->lastInsertID(); 
				
				$iUser = (is_null($this->user())||$this->user()==0) ? ((is_null($this->market())||$this->market()==0) ? 0 : owaesitem($this->market())->author()->id()) : $this->user(); // bron van alle onheil
				if ($iUser != 0){
					foreach (user($iUser)->groups() as $oGroup){
						$oMessage = new message(); 
						$oMessage->sender(0); 
						$oMessage->receiver($iUser); 
						$oMessage->body("Er werd misbruik gemeld over onderstaande: ");   
						if (!is_null($this->market())) $oMessage->data("market", $this->market());   
						$oMessage->data("user", $iUser);
						$oMessage->data("info", $this->reason());  
						$oMessage->data("reporter", me());     
						$oMessage->update(); 
					}
				}
				$arAdmins = new userlist(); 
				$arAdmins->filter("admin"); 
				foreach ($arAdmins->getList() as $oAdmin) {
					$oMessage = new message(); 
					$oMessage->sender(0); 
					$oMessage->receiver($oAdmin->id()); 
					$oMessage->body("Er werd misbruik gemeld over onderstaande ");   
					if (!is_null($this->market())) $oMessage->data("market", $this->market());   
					$oMessage->data("user", $iUser);   
					$oMessage->data("info", $this->reason()); 
					$oMessage->data("reporter", me());  
					$oMessage->update(); 
				}
				$oMessage = new message(); 
				$oMessage->sender(0); 
				$oMessage->receiver($iUser); 
				$oMessage->body("Onderstaand misbruik werd doorgestuurd");   
				if (!is_null($this->market())) $oMessage->data("market", $this->market());   
				if (!is_null($this->user())) $oMessage->data("user", $this->user());   
				$oMessage->data("info", $this->reason());   
				$oMessage->update(); 
			} else {
				$arUpdates = array(); 
				foreach ($arVelden as $strField=>$strValue) {
					$arUpdates[] = $strField . " = '" . $oDB->escape($strValue) . "'"; 
				} 
				$oDB->sql("update tblAbuse set " . implode(",", $arUpdates) . " where id = " . $this->iID . ";"); 
				$oDB->execute(); 
			}	
		}
		
		
	}
