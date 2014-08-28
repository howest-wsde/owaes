<?php
	class email {  
		private $strFromMail = "benedikt@beuntje.com";
		private $strFromName = "Benedikt Beun";
		private $strToMail = "benedikt@beuntje.com";
		private $strToName = "Benedikt Beun";
		private $strSubject = "OWAES"; 
		private $strMessage = ""; 
		 
		public function email($strTo = "", $strSubject = "", $strMessage = "") { 
			if ($strTo != "") {
				$this->strToMail = $strTo; 
				$this->strToName = $strTo; 
			}
			if ($strSubject != "") $this->strSubject = $strSubject; 
			if ($strMessage != "") $this->strMessage = $strMessage; 
			if (($strTo != "") && ($strMessage != "")) $this->send();  
		} 
		
		public function setTo($strMail, $strName = "") {
			$this->strToMail = $strMail; 
			$this->strToName = ($strName == "") ? $strMail : $strName;  
		}
		
		public function setBody($strHTML) {
			$this->strMessage = $strHTML; 
		}
		
		public function setSubject($strSubject) {
			$this->strSubject = $strSubject; 
		}
		
		public function send() { 
			$strHeaders  = 'MIME-Version: 1.0' . "\r\n";
			$strHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
			$strHeaders .= 'To: ' . $this->strToName . ' <' . $this->strToMail . '>' . "\r\n";
			$strHeaders .= 'From: ' . $this->strFromName . ' <' . $this->strFromMail . '>' . "\r\n"; 
			
			mail($this->strToMail, $this->strSubject, $this->strMessage, $strHeaders);
			
			// echo $this->strMessage; 
			
			// save ("cache/mail." . time() . ".txt", $strHeaders . $this->strMessage);  
		}
		 
		
	}
	 
?>