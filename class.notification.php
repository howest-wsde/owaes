<?php 
	class notification {  
		private $iUser = 0; 
		private $iSender = 0; 
		private $strKey = NULL; 
		private $strMessage = NULL; 
		private $strLink = NULL;  
		 
		public function notification($iUser = NULL, $strKey = NULL, $strMessage = NULL, $strLink = NULL) {  
			if (is_null($iUser)) $iUser = me(); 
			$this->iUser = $iUser;  
			$this->strKey = is_null($strKey) ? "message" + floor(time()/10) : $strKey; 
			if (!is_null($strKey)) $this->key($strKey); 
			if (!is_null($strMessage)) $this->message($strMessage); 
			if (!is_null($strLink)) $this->link($strLink); 
			$this->sender(me()); 
		}
		
		public function sender($iSender = NULL) {
			if (!is_null($iSender)) $this->iSender = $iSender; 
			return $this->iSender; 
		}
		
		public function key($strKey = NULL) { /* get / set key => een key wordt gebruikt als een notification overschreven kan worden
			bv. key = "subscription.35" => "Marcel heeft zich ingeschreven voor dit item"
			later key = "subscription.35" => "Marcel en Ludo hebben zich ingeschreven voor dit item"
		*/
			if (!is_null($strKey)) $this->strKey = $strKey; 
			return $this->strKey; 
		}
		
		public function message($strMessage = NULL) {
			if (!is_null($strMessage)) $this->strMessage = $strMessage; 
			return $this->strMessage; 
		}
		
		public function link($strLink = NULL) {
			if (!is_null($strLink)) $this->strLink = $strLink; 
			return $this->strLink; 
		}
		
		public function send() {
			$arFields = array(
				"sleutel" => $this->strKey, 
				"readdate" => 0, 
				"message" => $this->strMessage, 
				"link" => $this->strLink, 
				"author" => $this->iSender, 
				"receiver" => $this->iUser, 
				"datum" => owaestime(), 
			); 
			$oDB = new database(); 
			$arKeys = array(); 
			$arValues = array(); 
			foreach ($arFields as $strKey=>$strValue) {
				$arKeys[] = $strKey; 
				$arValues[] = "'" . $oDB->escape($strValue) . "'"; 
			}
			$strSQL = "insert into tblNotifications (" . implode(", ", $arKeys) . ") values (" . implode(", ", $arValues) . "); "; 
			$oDB->execute($strSQL); 
			 
			return true; 
		}
		
		public function read($strKey) { 
			$oDB = new database(); 
			$strSQL = "update tblNotifications set readdate = " . owaestime() . " where receiver = " . $this->iUser . " and sleutel = '" . $oDB->escape($strKey) . "' and readdate = 0; "; 
			$oDB->execute($strSQL); 
		}
		
		public function getList($iLimit = NULL) {
			$arMessages = array(); 
			$strLimit = ""; 
			$arWhere = array(
				"receiver =" . $this->iUser, 
			); 
			if (is_null($iLimit)) $arWhere[] = "readdate = 0"; 
			if (is_numeric($iLimit)) $strLimit = " limit $iLimit "; 
			$oDB = new database("select * from tblNotifications where " . implode(" and ", $arWhere) . " order by datum  $strLimit ;", TRUE); 
			while ($oDB->nextRecord()) {
				$arMessage =array(
					"title" => "", 
					"time" => $oDB->get("datum"), 
					"from" => $oDB->get("author"),  
					"message" => $oDB->get("message"),
					"icon" => ($oDB->get("author")==0) ? fixPath("/img/owaes80x80.png") : user($oDB->get("author"))->getImage("80x80", FALSE),
				);  
				if ($oDB->get("link") != "") $arMessage["link"] = $oDB->get("link"); 
				$arMessages[$oDB->get("sleutel")] = $arMessage; 	
			}
			return $arMessages;  
		}
	}  
	
?>