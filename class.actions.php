<?php 
	class actions { 
		private $iUser = NULL; 
		private $arActions = array(); 
		private $arModals = NULL; 
	
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
		
		public function modals() { 
			if (is_null($this->arModals)) {
				$arModalURLs = array();  
				foreach ($this->getList() as $oAction) {
					switch($oAction->type()) {
						case "transaction": 
							$arModalURLs[] = "modal.transaction.php?m=" . $oAction->data("market") . "&u=" . $oAction->data("user"); 
							break; 
						case "alert": 
							$arModalURLs[] = "modal.alert.php?t=" . urlencode($oAction->data("title")) . "&a=" . urlencode($oAction->data("text")); 
							$oAction->done(owaestime()); 
							$oAction->update();  
							break; 
						case "feedback": 
							if ((user($this->iUser)->level()>=3)&&($oAction->tododate() > owaestime()-(7*24*60*60))) {
								$arModalURLs[] = "modal.feedback.php?m=" . $oAction->data("market") . "&u=" . $oAction->data("user"); 
							}
							break; 
						case "badge": 
							$arModalURLs[] = "modal.badge.php?m=" . $oAction->data("type"); 
							$oAction->done(owaestime()); 
							$oAction->update();  
							break; 
						case "validateuser": 
							$arModalURLs[] = "modal.confirmuser.php?u=" . $oAction->data("user");  
							break; 
						case "experience":  
							if ( user($this->iUser)->experience()->level(FALSE) != user($this->iUser)->experience()->level(TRUE)) { 
								$arModalURLs[] = "modal.experience.php";  
								$arModalURLs[] = "modal.nextlevel.php"; 
								$oAction->done(owaestime()); 
								$oAction->update();  
							}  else if ( user($this->iUser)->experience()->total(TRUE) - user($this->iUser)->experience()->total() >= 10) { 
								$arModalURLs[] = "modal.experience.php";  
								$oAction->done(owaestime()); 
								$oAction->update();  
							}
							break; 
					} 
				} 
				$this->arModals = $arModalURLs; 
			}
			return $this->arModals; 
		}  
	} 

	class action {
		private $iID = 0; 
		private $iUser = NULL; 
		private $iCreated = NULL; 
		private $iTodoDate = NULL; 
		private $iDoneDate = 0; 
		private $strType = NULL; 
		private $arData = array(); 
		
		public function action($iUser = NULL) {
			$this->iCreated = owaestime(); 
			if (!is_null($iUser)) $this->user($iUser);  
		}
		
		public function type($strType = NULL) {
			if (!is_null($strType)) $this->strType = $strType; 
			return $this->strType; 
		} 
		
		public function data($strKey, $strValue = NULL) { 
			if (!is_null($strValue)) $this->arData[$strKey] = $strValue; 
			return isset($this->arData[$strKey]) ? $this->arData[$strKey] : FALSE; 
		}
		
		public function tododate($iDate = NULL) {
			if (!is_null($iDate)) $this->iTodoDate = $iDate; 
			return $this->iTodoDate; 
		}
		
		public function done($iDate = NULL) {
			if (!is_null($iDate)) {
				if (is_numeric($iDate)) {
					$this->iDoneDate = $iDate; 
				} elseif (is_bool($iDate)) {
					$this->iDoneDate = ($iDate ? owaestime() : 0);
				}
			}
			return ($this->iDoneDate != 0); 
		}
		
		public function donedate($iDate = NULL) {
			if (!is_null($iDate)) $this->iDoneDate = $iDate; 
			return ($this->iDoneDate); 
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
		
		public function checkID() {
			$iUser = $this->iUser;
			$strActie = $this->strType; 
			$strData = json_encode($this->arData);  
			$oDB = new database(); 
			
			$oDB->execute("select * from tblActions where user = " . $iUser . " and actie = '" . $oDB->escape($strActie) . "' and data = '" . $oDB->escape($strData) . "' order by id desc; "); 
			if ($oDB->nextRecord())  $this->loadRecord($oDB->record()); //$this->iID = $oDB->get("id");  
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
		
			if ($this->iID == 0) $this->checkID(); 

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
