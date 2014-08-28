<?php 
	class log { 
		private $iID = NULL;   
		private $strTekst = NULL;  
		private $arData = array();  
		
		public function log($strTekst, $arData = array()) { 
			$this->strTekst = $strTekst; 
			$this->arData = $arData; 
			$this->data("datum", date('m-d-Y h:i:s', time())); 
			$this->data("domein", settings("domain", "name")); 
			$this->save(); 
		}
		
		public function data($strItem, $strValue = NULL) {
			if (!is_null($strValue)) $this->arData[$strItem] = $strValue; 
			return $this->arData[$strItem]; 
		}
		 
		public function save() {
			$oDB = new database(); 
			if (is_null($this->iID)) {
				$oDB->sql("insert into tblLog (user, datum, info, data) values ('" . me() . "', '" . owaesTime() . "', '" . $oDB->escape($this->strTekst) . "', '" . $oDB->escape(json_encode($this->arData)) . "'); "); 
				$oDB->execute(); 
				$this->iID = $oDB->lastInsertID(); 
			} else {
				$oDB->sql("update tblLog set info = '" . $oDB->escape($this->strTekst) . "', data = '" . $oDB->escape(json_encode($this->arData)) . "' where id = " . $this->iID . "; "); 
				$oDB->execute(); 
			}	
		}

	}
	
?>