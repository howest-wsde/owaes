<?php 
	class mailalert { 
		private $iID = NULL; 
		private $iUser = NULL; 
		private $arLink = NULL; 
		private $strMessage = NULL; 
		private $iDeadline = NULL; 
		private $iSent = NULL;  
		private $strSleutel = NULL;  
		private $bUniekeSleutel = FALSE; 
			
		public function mailalert() {
//			$this->iUser = $iUser;  
		} 
		
		public function cancel($strSleutel) { 
			$oDB = new database();
			$strSQL = "update tblMailalerts set sent = 0 where sent is NULL and sleutel = '" . $oDB->escape($strSleutel) . "'; "; 
			$oDB->execute($strSQL);  
		}
		
		public function id($iID = NULL) { // get / set ID (enkel set via DB)
			if (!is_null($iID)) $this->iID = $iID; 
			return $this->iID; 	
		} 
		
		public function link($strType=NULL, $iID=NULL) { 
			if (!is_null($iID)) $this->arLink = array("type" => $strType, "id" => $iID); 
			switch($this->arLink["type"]) {
				case "market": 
					return owaesitem($this->arLink["id"])->url(); 
					break; 	
				case "user": 
					return user($this->arLink["id"])->getURL(); 
					break; 	
			}
			return false; 	
		}
		
		public function message($strMessage = NULL) {
			if (!is_null($strMessage)) $this->strMessage = $strMessage; 
			return $this->strMessage; 	
		} 
		
		public function sleutel($strSleutel = NULL, $bUnique = NULL) {
			if (!is_null($strSleutel)) $this->strSleutel = $strSleutel; 
			if (!is_null($bUnique)) $this->bUniekeSleutel = $bUnique; 
			return $this->strSleutel; 	
		} 
		
		public function deadline($iDeadline = NULL) {
			if (!is_null($iDeadline)) $this->iDeadline = ($iDeadline > 943920000) ? $iDeadline : owaestime()+$iDeadline; 
			return $this->iDeadline; 	
		}
		
		public function sent($iSent = NULL) {
			if (!is_null($iSent)) $this->iSent = $iSent; 
			return $this->iSent; 	
		}
		
		public function user($iUser = NULL) {
			if (!is_null($iUser)) $this->iUser = $iUser; 
			return $this->iUser; 	
		}
		
		public function update() {   
			$oDB = new database(); 
		
			$arVelden = array(
				"id" => $this->iID, 
				"user" => $this->iUser,  
				"sleutel" => $this->strSleutel,  
				"link" => json_encode($this->arLink), 
				"message" => $this->strMessage, 
				"deadline" => $this->iDeadline, 
				"sent" => $this->iSent,  
			);   
		 
			if (is_null($this->iID)) {
				$arVeldKeys = array(); 
				$arWaarden = array(); 
				foreach ($arVelden as $strVeld=>$strWaarde) {
					$arVeldKeys[] = $strVeld; 
					$arWaarden[] = $oDB->escape($strWaarde, TRUE); 
				}
				$strSQL = "insert into tblMailalerts (" . implode(", ", $arVeldKeys) . ") values (" . implode(", ", $arWaarden) . ");"; 
				$oDB->execute($strSQL);  
				$this->id($oDB->lastInsertID()); 
			} else { 
				$arUpdates = array(); 
				foreach ($arVelden as $strVeld=>$strWaarde) {
					$arUpdates[] = $strVeld . " = " . $oDB->escape($strWaarde, TRUE); 
				}
				$strSQL = "update tblMailalerts set " . implode(", ", $arUpdates) . " where id = " . $this->id() . ";"; 
				$oDB->execute($strSQL);  
			} 
		}
		
	}
