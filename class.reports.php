<?php 
	class reports { 
		private $arReports = array(); 
	
		public function reports() {
		}
		 
		public function getList() {
			
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
		
		public function data($strKey, $strValue = NULL) {
			if (is_null($this->arData)) $this->arData = array(); 
			if (!is_null($strValue)) $this->arData[$strKey] = $strValue; 
			return isset($this->arData[$strKey]) ? $this->arData[$strKey] : FALSE; 
		}
		
		public function user($iUser = NULL) {
			if (!is_null($iUser)) $this->iUser = $iUser; 	
			return $this->iUser; 
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
