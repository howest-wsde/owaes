<?php 
	class conversation {   // conversatie tussen 2 personen
		private $arReceivers = array(); 
		private $iMe = 0; 
		private $arMessages = NULL; 
		private $arUsers = array(); 
		private $arFilter = array(); 
		 
		public function conversation($oReceivers) {  /* 
		$oReceivers = ID van de andere gebruiker, of array met ID's 
		(TODO: opzetting was dat chat tss 3 of meer partijen ook mogelijk was, is nu niet meer)
		*/
			global $oPage; 
			$iMe = $oPage->iUser; 
			$this->iMe = $iMe; 
			if (is_array($oReceivers)) {
				$arReceivers = $oReceivers; 
			} else if (is_numeric($oReceivers)) {
				$arReceivers = array($oReceivers); 
			} else $arReceivers = array(); 
			if (!in_array($iMe, $arReceivers)) array_push($arReceivers, $iMe); 
			sort($arReceivers); 
			$this->arReceivers = $arReceivers; 
			return $this; 
		}
		
		public function users($bIncludeMyself = TRUE) { // returns array met personen waarmee gechat wordt (al dan niet met jezelf)
			$arUsers = $this->arReceivers; 
			if (!$bIncludeMyself) {
				$key = array_search($this->iMe,$arUsers);
				if($key!==false) unset($arUsers[$key]); 
			}
			sort($arUsers); 
			return $arUsers; 
		}
		
		public function user($iUser) { // user toevoegen aan conversatie
			if (!isset($this->arUsers[$iUser])) {
				$this->arUsers[$iUser] = new user($iUser); 
			}
			return $this->arUsers[$iUser]; 
		}
		
		public function add($strMessage, $iMarket = 0) {// message toevoegen aan conversation / optional $iMarket
			$oDB = new database(); 

			$oMessage = new message(); 
			$oMessage->body($strMessage); 
			$oMessage->sender($this->iMe); 
			$oMessage->receivers($this->arReceivers); 
			$oMessage->market($iMarket); 
			$oMessage->update(); 

			// $strMessage = $oDB->escape($strMessage);   
			// $oDB->execute("insert into tblConversations (sender, receivers, market, message, sentdate) values (" . $this->iMe . ", '" . implode(",", $this->arReceivers) . "', '$iMarket', '$strMessage', " . owaesTime() . "); "); 
			
			if (!is_null($this->arMessages)) {
				array_unshift($this->arMessages, array(
					"from" => $this->iMe, 
					"receivers" => implode(",", $this->arReceivers), 
					"subject" => "", 
					"message" => $strMessage, 
					"market" => $iMarket, 
					"sentdate" => owaesTime() 
				)); 
			}
		}
		
		public function messages() { // returns array van class.message's (eventueel na filter)
			if (is_null($this->arMessages)) {
				$arMessages = array(); 
				$oDB = new database("select * from tblConversations where receivers = '" . implode(",", $this->arReceivers) . "' order by sentdate; ", TRUE);
                //select * from tblConversations where receivers ='30,31' order by market, sentdate desc
				// echo $oDB->table(); 
				$arUsers = array(); 
				while ($oDB->nextRecord()) {
					array_push($arMessages, new message($oDB->record())); 
					/*
					
					array(
						"from" => $oDB->get("sender"), 
						"receivers" => $oDB->get("receivers"), 
						"subject" => $oDB->get("subject"), 
						"message" => $oDB->get("message"), 
						"sentdate" => $oDB->get("sentdate"), 
					)); */
				} 
				$this->arMessages = $arMessages; 
			}
			$arResult = $this->arMessages; 
			if (isset($this->arFilter["owaes"])) {
				$arTemp = array(); 
				foreach ($arResult as $oMessage) { 
					if ($oMessage->market() == $this->arFilter["owaes"]) array_push($arTemp, $oMessage); 
				}
				$arResult = $arTemp; 
			}
			return $arResult; 
		}
		

		public function filter($strWhat, $strValue) { // set filter bv. ("owaes", 1)
			if ($strValue != "") {
				$this->arFilter[strtolower($strWhat)] = $strValue; 
			} else {
				if (isset($this->arFilter[strtolower($strWhat)])) unset($this->arFilter[strtolower($strWhat)]); 
			}
		}
		
		function addMessage($strMessage = NULL) { // bericht toevoegen (TODO: wa's verschil met add? )
			$oMessage = new message(); 
			if (!is_null($strMessage)) $oMessage->body($strMessage); 
			$oMessage->receivers($this->arReceivers); 
			$oMessage->sender($this->iMe);  
			return $oMessage; 	
		}
		 
	}
	
	class message {  // 1 bericht 
		private $iID = NULL;  
		private $strSubject = NULL; 
		private $strMessage = NULL; 
		private $arReceivers = NULL; 
		private $iSender = NULL; 
		private $iMarket = NULL; 
		private $iSent = 0; 
		private $iRead = 0; 
		 
		public function message($oDBrecord = NULL) { 
			$this->iSent = owaesTime(); 
			if (!is_null($oDBrecord)) $this->load($oDBrecord); 
		}
		
		public function load($oDBrecord) {
			$this->strSubject = $oDBrecord["subject"]; 
			$this->strMessage = $oDBrecord["message"]; 
			$this->arReceivers = explode(",", $oDBrecord["receivers"]); 
			$this->iSender = $oDBrecord["sender"]; 
			$this->iMarket = $oDBrecord["market"]; 
			$this->iSent = $oDBrecord["sentdate"]; 
			$this->iRead = $oDBrecord["readdate"];
			$this->iID = $oDBrecord["id"]; 
		}
		
		public function subject($strSubject = NULL) { // Get / set titel 
			if (!is_null($strSubject)) $this->strSubject = $strSubject; 
			return $this->strSubject; 
		}
		
		public function body($strMessage = NULL) { // Get / set berichtbody 
			if (!is_null($strMessage)) $this->strMessage = $strMessage; 
			return $this->strMessage; 
		}
		
		public function sender($iSender = NULL) {  // Get / set sender 
			if (!is_null($iSender)) $this->iSender = $iSender; 
			return user($this->iSender); 
		}
		
		public function receivers($arReceivers = NULL) { // Get / set receiver(s)  (array)
			if (!is_null($arReceivers)) $this->arReceivers = $arReceivers; 
			return $this->arReceivers; 
		}
		
		public function market($iMarket = NULL) { // get/set (optional) market-ID
			if (!is_null($iMarket)) $this->iMarket = $iMarket; 
			return $this->iMarket; 
		}
		
		public function sent($iSent = NULL) { // get / set "sent" (TODO: wut?)
			if (!is_null($iSent)) $this->iSent = $iSent; 
			return $this->iSent; 
		}
		
		public function read($iRead = NULL) { // get / set READ
			if (!is_null($iRead)) $this->iRead = $iRead; 
			return ($this->iRead != 0); 
		}
		
		public function doRead() { // set read (TODO: verschil "read()"? 
			if (is_null($this->iRead) || ($this->iRead==0)) {
				$this->iRead = owaesTime(); 	
				$this->update(); 
			}
		}
				
		public function update() { // save
			$oDB = new database(); 
			if (is_null($this->iID)) { 
				$strSQL = "insert into tblConversations 
							(sender , receivers , subject , market , message , sentdate , readdate , isread) 
							values 
							('" . $this->iSender . "', '" . implode(",", $this->arReceivers) . "' , '" . $oDB->escape($this->strSubject) . "' , '" . $this->iMarket . "' , '" . $oDB->escape($this->strMessage) . "' , '" . $this->iSent . "' , '" . $this->iRead . "' , '" . ($this->iRead!=0) . "');  "; 
				$oDB->execute($strSQL); 
				$this->iID = $oDB->lastInsertID(); 

				foreach ($this->arReceivers as $iReceiver){
					if ($iReceiver != $this->iSender) {
						$oNotification = new notification($iReceiver, "conversation." . implode(",", $this->receivers())); 
						$oNotification->message($this->sender()->getName() . " stuurde een berichtje"); 
						$oNotification->sender($this->iSender); 
						$oNotification->link(fixPath("conversation.php?users=" . implode(",", $this->arReceivers))); 
						$oNotification->send(); 
					}
				}
								
			} else {
				$strSQL = "update tblConversations set 
							sender='" . $this->iSender . "' ,
							receivers='" . implode(",", $this->arReceivers) . "' ,
							subject='" . $oDB->escape($this->strSubject) . "' ,
							market='" . $this->iMarket . "' ,
							message='" . $oDB->escape($this->strMessage) . "' ,
							sentdate='" . $this->iSent . "' ,
							readdate='" . $this->iRead . "' ,
							isread='" . (!(is_null($this->iRead))) . "' 
							where id = '" . $this->iID . "'  ";  
				$oDB->execute($strSQL); 
			}
			

		}
		
	 
	}  
	
?>