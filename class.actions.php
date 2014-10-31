<?php 
	class actions { 
		private $iUser = NULL; 
		private $arActions = array(); 
	
		public function actions($iUser) {
			$this->iUser = $iUser;  
		}
		
		public function search($arData) {
			foreach ($this->getList(FALSE) as $oAction) {
				$bOK = TRUE; 
				foreach ($arData as $strKey=>$strValue) {
					switch($strKey) {
						case "type": 
							if ($oAction->type() != $strValue) $bOK = FALSE; 
							break; 
						default: 	
							if ($oAction->data($strKey) != $strValue) $bOK = FALSE; 
					} 
				}
				if ($bOK) return $oAction; 
			}
			return FALSE; 
		}
		
		public function getList($bOnlyOpen = TRUE) {
			$strKey = $bOnlyOpen ? "open" : "all"; 
			if (!isset($this->arActions[$strKey])) {
				$arActions = array(); 
				$oDB = new database(); 
				$arWhere = array("user = '" . $this->iUser . "'"); 
				if ($bOnlyOpen) {
					$arWhere[] = "tododate < " . owaestime(); 
					$arWhere[] = "completed = 0"; 
				}
				$oDB->execute("select * from tblActions where " . implode(" and ", $arWhere) . "; ");  
				while ($oDB->nextRecord()) {
					$oAction = new action();  
					$oAction->loadRecord($oDB->record()); 
					$arActions[] = $oAction; 
				}
				$this->arActions[$strKey] = $arActions; 
			}
			return $this->arActions[$strKey]; 
		}
	}
 

	class action {
		private $iID = 0; 
		private $iUser = NULL; 
		private $iCreated = NULL; 
		private $iTodoDate = NULL; 
		private $iDoneDate = 0; 
		private $strType = NULL; 
		private $arData = NULL; 
		
		public function action($iUser = NULL) {
			$this->iCreated = owaestime(); 
			if (!is_null($iUser)) $this->user($iUser);  
		}
		
		public function type($strType = NULL) {
			if (!is_null($strType)) $this->strType = $strType; 
			return $this->strType; 
		} 
		
		public function data($strKey, $strValue = NULL) {
			if (is_null($this->arData)) $this->arData = array(); 
			if (!is_null($strValue)) $this->arData[$strKey] = $strValue; 
			return isset($this->arData[$strKey]) ? $this->arData[$strKey] : FALSE; 
		}
		
		public function tododate($iDate = NULL) {
			if (!is_null($iDate)) $this->iTodoDate = $iDate; 
			return $this->iTodoDate; 
		}
		
		public function done($iDate = NULL) {
			if (!is_null($iDate)) $this->iDoneDate = $iDate; 
			return ($this->iDoneDate != 0); 
		}
		
		
		public function user($iUser = NULL) {
			if (!is_null($iUser)) $this->iUser = $iUser; 
			return $this->iUser; 
		}
		
		public function loadRecord($oRecord) {
			$this->iID = $oRecord["id"]; 
			$this->user($oRecord["user"]); 
			$this->tododate($oRecord["tododate"]); 
			$this->done($oRecord["completed"]); 
			$this->type($oRecord["actie"]); 
			$this->iCreated = $oRecord["created"]; 
			$this->arData = json_decode($oRecord["data"], TRUE); 
		}
		
		public function update() {
			$arVelden = array(
				"created" => $this->iCreated, 
			); 
			if (!is_null($this->iUser)) $arVelden["user"] = $this->iUser; 
			if (!is_null($this->iTodoDate)) $arVelden["tododate"] = $this->iTodoDate; 
			if (!is_null($this->iDoneDate)) $arVelden["completed"] = $this->iDoneDate; 
			if (!is_null($this->strType)) $arVelden["actie"] = $this->strType; 
			if (!is_null($this->arData)) $arVelden["data"] = json_encode($this->arData); 

			$oDB = new database(); 
			if ($this->iID == 0) {
				$arFields = array(); 
				$arValues = array(); 
				foreach ($arVelden as $strField=>$strValue) {
					$arFields[] = $strField; 
					$arValues[] = "'" . $oDB->escape($strValue) . "'"; 
				} 
				$oDB->sql("insert into tblActions (" . implode(",", $arFields) . ") values (" . implode(",", $arValues) . "); "); 
				$oDB->execute(); 
				$this->iID = $oDB->lastInsertID(); 
			} else {
				$arUpdates = array(); 
				foreach ($arVelden as $strField=>$strValue) {
					$arUpdates[] = $strField . " = '" . $oDB->escape($strValue) . "'"; 
				} 
				$oDB->sql("update tblActions set " . implode(",", $arUpdates) . " where id = " . $this->iID . ";"); 
				$oDB->execute(); 
			}	
		}
		
		
	}
