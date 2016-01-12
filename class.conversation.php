<?php 
	class conversation {   // conversatie tussen 2 personen 
		private $iReceiver = NULL; 
		private $iSender = NULL; 
		private $arMessages = NULL; 
		private $arUsers = array(); 
		private $arFilter = array(); 
		 
		public function conversation($iReceiver = NULL) {  /* 
		$iReceiver = ID van de andere gebruiker 
		*/ 
			if (!is_null($iReceiver)) $this->receiver($iReceiver); 
			$this->sender(me());  
			return $this; 
		}
		
		public function receiver($iReceiver = NULL) {
			if (!is_null($iReceiver)) $this->iReceiver = $iReceiver; 
			return $this->iReceiver; 
		}
		public function sender($iSender = NULL) {
			if (!is_null($iSender)) $this->iSender = $iSender; 
			return $this->iSender; 
		}
/*
		public function users($bIncludeMyself = TRUE) { // returns array met personen waarmee gechat wordt (al dan niet met jezelf)
			$arUsers = $this->arReceivers; 
			if (!$bIncludeMyself) {
				$key = array_search(me(),$arUsers);
				if($key!==false) unset($arUsers[$key]); 
			}
			sort($arUsers); 
			return $arUsers; 
		}
		
		public function user($iUser) { // user toevoegen aan conversatie
			if (!isset($this->arUsers[$iUser])) {
				$this->arUsers[$iUser] = user($iUser); 
			}
			return $this->arUsers[$iUser]; 
		}
*/
		
		public function add($strMessage, $iMarket = 0) {// message toevoegen aan conversation / optional $iMarket
			$oDB = new database(); 

			$oMessage = new message(); 
			$oMessage->body($strMessage); 
			$oMessage->sender($this->sender()); 
			$oMessage->receiver($this->receiver()); 
			$oMessage->market($iMarket); 
			$oMessage->update(); 

			// $strMessage = $oDB->escape($strMessage);   
			// $oDB->execute("insert into tblConversations (sender, receivers, market, message, sentdate) values (" . me() . ", '" . implode(",", $this->arReceivers) . "', '$iMarket', '$strMessage', " . owaesTime() . "); "); 
			
			if (!is_null($this->arMessages)) {
				array_unshift($this->arMessages, array(
					"from" => $this->sender(), 
					"receiver" => $this->receiver(), 
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
				
				$oDB = new database("select * from tblConversations where ((receiver = " . $this->receiver() . " and sender = " . $this->sender() . ") or (receiver = " . $this->sender() . " and sender = " . $this->receiver() . ")) order by sentdate; ", TRUE);
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
			$oMessage->receiver($this->receiver()); 
			$oMessage->sender($this->sender());  
			return $oMessage; 	
		}
		 
	}
	
	class message {  // 1 bericht 
		private $iID = NULL;  
		private $strSubject = NULL; 
		private $strMessage = NULL; 
		private $iReceiver = NULL; 
		private $iSender = NULL; 
		private $iMarket = NULL; 
		private $arData = array(); 
		private $iSent = 0; 
		private $iRead = 0; 
		 
		public function message($oDBrecord = NULL) { 
			$this->iSent = owaesTime(); 
			$this->iSender = me(); 
			if (!is_null($oDBrecord)) $this->load($oDBrecord); 
		}
		
		public function load($oDBrecord) {
			$this->strSubject = $oDBrecord["subject"]; 
			$this->strMessage = $oDBrecord["message"]; 
			$this->iReceiver = $oDBrecord["receiver"]; 
			$this->iSender = $oDBrecord["sender"]; 
			$this->iMarket = $oDBrecord["market"]; 
			$this->iSent = $oDBrecord["sentdate"]; 
			$this->iRead = $oDBrecord["readdate"];
			$this->arData = json_decode($oDBrecord["data"], TRUE); 
			if (is_null($this->arData)) $this->arData = array(); 
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
		
		public function data($strField = NULL, $strValue = NULL) {  
			if (!is_null($strValue)) $this->arData[$strField] = $strValue;  
			return (is_null($strField)) ? $this->arData : (isset($this->arData[$strField]) ? $this->arData[$strField] : FALSE); 
		}
		
		public function sender($iSender = NULL) {  // Get / set sender 
			if (!is_null($iSender)) $this->iSender = $iSender; 
			return user($this->iSender); 
		}
		 
		public function receiver($iReceiver = NULL) { // Get / set receiver (one receiver / resets array!)
			if (!is_null($iReceiver)) $this->iReceiver = $iReceiver; 
			return $this->iReceiver; 
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
							(sender , receiver , subject , market , message , sentdate , readdate , isread, data) 
							values 
							('" . $this->iSender . "', '" . $this->iReceiver . "' , '" . $oDB->escape($this->strSubject) . "' , '" . $this->iMarket . "' , '" . $oDB->escape($this->strMessage) . "' , '" . $this->iSent . "' , '" . $this->iRead . "' , '" . ($this->iRead!=0) . "', '" . $oDB->escape(json_encode($this->arData)) . "');  "; 
				$oDB->execute($strSQL); 
				$this->iID = $oDB->lastInsertID(); 

				if ($this->iReceiver != $this->iSender) {
					$oNotification = new notification($this->iReceiver, "conversation." . $this->iSender); 
					$oNotification->message($this->sender()->getName() . " stuurde een berichtje"); 
					$oNotification->sender($this->iSender); 
					$oNotification->link(fixPath("conversation.php?u=" . $this->iSender)); 
					$oNotification->send();   

					if (user($this->iReceiver)->mailalert("newmessage")) {
						$oAlert = new mailalert(); 
						$oAlert->cancel("conversation." . $this->iReceiver . "." . $this->iSender);   // bestaande messages met zelfde key wissen
						$oAlert->user($this->iReceiver); 
						$oAlert->link("conversation", $this->iSender); 
						$oAlert->sleutel("conversation." . $this->iReceiver . "." . $this->iSender); 
						$oAlert->deadline(user($this->iReceiver)->mailalert("newmessage")); 
						$oAlert->message($this->sender()->getName() . " stuurde een berichtje. Klik hier om het te lezen.");  
						$oAlert->update();  
					} 
				} 
								
			} else {
				$strSQL = "update tblConversations set 
							sender='" . $this->iSender . "' ,
							receiver='" . $this->iReceiver . "' ,
							subject='" . $oDB->escape($this->strSubject) . "' ,
							market='" . $this->iMarket . "' ,
							message='" . $oDB->escape($this->strMessage) . "' ,
							sentdate='" . $this->iSent . "' ,
							readdate='" . $this->iRead . "' ,
							isread='" . (!(is_null($this->iRead))) . "' , 
							data='" . $oDB->escape(json_encode($this->arData)) . "' 
							where id = '" . $this->iID . "'  ";  
				$oDB->execute($strSQL); 
			}
			

		}
		
	 
	}  
	