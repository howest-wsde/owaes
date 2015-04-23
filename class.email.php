<?php
	require_once('phpmailer/class.phpmailer.php');
	require ('phpmailer/PHPMailerAutoload.php'); 
	
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
			if (settings("mail", "smtp")) {
				$oPHPmailer = new PHPMailer(); 
				
				$oPHPmailer->IsSMTP(); 
				
				//$oPHPmailer->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
				//  $oPHPmailer->Debugoutput = 'html';
				$oPHPmailer->Host       = settings("mail", "Host"); // "smtp.gmail.com";      // sets GMAIL as the SMTP server
				$oPHPmailer->SMTPAuth   = settings("mail", "SMTPAuth"); // true;                  // enable SMTP authentication
				$oPHPmailer->SMTPSecure = settings("mail", "SMTPSecure"); // "ssl";                 // sets the prefix to the servier
				$oPHPmailer->Port       = settings("mail", "Port"); // 465;                   // set the SMTP port for the GMAIL server
				$oPHPmailer->Username   = settings("mail", "Username"); // "esf.owaes@gmail.com";  // GMAIL username
				$oPHPmailer->Password   = settings("mail", "Password"); // "ESF-Howest-OWAES-2015";            // GMAIL password  
				 
				$oPHPmailer->SetFrom($this->strFromMail, $this->strFromName);		 		
				$oPHPmailer->AddAddress($this->strToMail, $this->strToName);			
				$oPHPmailer->Subject    = $this->strSubject;			
				$oPHPmailer->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
				
				$oPHPmailer->MsgHTML($this->strMessage);
				
				if(!$oPHPmailer->Send()) {
					//echo "Mailer Error: " . $oPHPmailer->ErrorInfo;
					return FALSE; 
				} else {
					//echo "Message sent!";
					return TRUE; 
				}
				
			} else {
			
				$strHeaders  = 'MIME-Version: 1.0' . "\r\n";
				$strHeaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
				$strHeaders .= 'To: ' . $this->strToName . ' <' . $this->strToMail . '>' . "\r\n";
				//$strHeaders .= 'From: ' . $this->strFromName . ' <' . $this->strFromMail . '>' . "\r\n"; 
				
				mail($this->strToMail, $this->strSubject, $this->strMessage, $strHeaders);
			}
			
 		}
		 
		
	}
	 