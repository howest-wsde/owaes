<?php 
	define ("TRANSACTION_STATE_INVALID", -1); 
	define ("TRANSACTION_STATE_NEW", 0); 
	define ("TRANSACTION_STATE_WAITING", 10);  
	define ("TRANSACTION_STATE_TOVALIDATE", 11); // alleen for public (niet in database, is onderdeel van WAITING)
	define ("TRANSACTION_STATE_COMPLETED", 20); 
	define ("TRANSACTION_NOTMYBUSINESS", -10); // alleen for public 
	
	define ("TOPUBLIC", 10); 
	define ("TOPRIVATE", 20);  
 

	class transaction {  
		private $iMarket = 0;
		private $iInitiator = 0;
		private $iSender = 0;
		private $iReceiver = 0;  
		private $iOther = 0;
		private $iCredits = 0; 
		private $iPhysical = 0;
		private $iMental = 0;
		private $iEmotional = 0;
		private $iSocial = 0;  
		private $strCode = NULL;  
		private $iID = NULL; 
		private $iSenderSigned = NULL; 
		private $iReceiverSigned = NULL; 
		private $iStatus = TRANSACTION_STATE_INVALID; 
		
		public function transaction($iMarket, $iUser = NULL) {  
			$oOwaesItem = $this->market($iMarket);  
			$this->credits($oOwaesItem->credits());
			$this->physical($oOwaesItem->physical());
			$this->mental($oOwaesItem->mental());
			$this->emotional($oOwaesItem->emotional());
			$this->social($oOwaesItem->social()); 
			
			if (is_null($iUser)) $iUser = me(); 
			if ($oOwaesItem->task()) { // checken of het een opdracht is (creator betaalt) of een dienst (creator wordt betaald)
				$this->sender($oOwaesItem->author()->id()); 
				if ($oOwaesItem->author()->id() != $iUser) {
					$this->receiver($iUser);  
				} else if ($oOwaesItem->author()->id() != me()) {
					$this->receiver(me());  
				}
			} else {
				$this->receiver($oOwaesItem->author()->id()); 
				if ($oOwaesItem->author()->id() != $iUser) {
					$this->sender($iUser);  
				} else if ($oOwaesItem->author()->id() != me()) {
					$this->sender(me());   
				}
			} 
		}
		

		public function sender($iSender = NULL) {
			if (!is_null($iSender)) {
				$this->iSender = intval($iSender); 
				if (($this->iSender != 0) && ($this->iReceiver != 0) && (is_null($this->strCode))) $this->load();  
			}
			return $this->iSender; 
		}
		public function receiver($iReceiver = NULL) {
			if (!is_null($iReceiver)) {
				$this->iReceiver = intval($iReceiver); 
				if (($this->iSender != 0) && ($this->iReceiver != 0) && (is_null($this->strCode))) $this->load(); 
			}
			return $this->iReceiver; 
		}
		
		
		private function load() {
			// $oDB = new database("select * from tblTransactions where market = " . $this->market()->id() . " and sender = " . $this->sender() . " and receiver = " . $this->receiver() . "; ", TRUE);  
			$oDB = new database("select * from tblPayments where market = " . $this->market()->id() . " and sender = " . $this->sender() . " and receiver = " . $this->receiver() . " and reason = 1 and actief = 1; ", TRUE);   
			
			if ($oDB->records()){
				$this->iID = $oDB->get("id"); 
				//$this->code($oDB->get("code"));
				$this->credits($oDB->get("credits"));
				//$this->physical($oDB->get("physical"));
				//$this->mental($oDB->get("mental"));
				//$this->emotional($oDB->get("emotional"));
				//$this->social($oDB->get("social")); 
				//$this->status($oDB->get("status")); 
				//$this->initiator($oDB->get("initiator")); 
				//$this->signed($this->sender(), $oDB->get("sendersigned")); 
				//$this->signed($this->receiver(), $oDB->get("receiversigned"));   
			} else {
				//$this->code(uniqueKey()); 
				//$this->initiator(me()); 
				$this->status(TRANSACTION_STATE_NEW);  
				$this->iID = 0; 
				//if (is_null($this->iCredits)) $this->credits(0);
				//if (is_null($this->iPhysical)) $this->physical(0);
				//if (is_null($this->iMental)) $this->mental(0);
				//if (is_null($this->iEmotional)) $this->emotional(0);
				//if (is_null($this->iSocial)) $this->social(0); 
				//if (is_null($this->iSenderSigned)) $this->signed($this->sender(), FALSE); 
				//if (is_null($this->iReceiverSigned)) $this->signed($this->receiver(), FALSE); 
			}
			//$this->checksubmit(); 
		}
		  
		public function update() { 
			$oDB = new database(); 
			if (is_null($this->iID) or ($this->iID == 0)) {
				$oDB->sql("insert into tblTransactions (date, initiator, sender, receiver, number, physical, mental, emotional, social, status, info, market, code, sendersigned, receiversigned) values ('" . owaesTime() . "', '" . $this->initiator()->id() . "', '" . $this->sender() . "', '" . $this->receiver() . "', '" . $this->credits() . "', '" . $this->physical() . "', '" . $this->mental() . "', '" . $this->emotional() . "', '" . $this->social() . "', '" . $this->status() . "', '', '" . $this->market()->id() . "', '" . $this->code() . "', '" . $this->iSenderSigned . "', '" . $this->iReceiverSigned . "'); "); 
				$oDB->execute(); 
				$this->iID = $oDB->lastInsertID();
			} else {
				$oDB->sql("update tblTransactions set status = '" . $this->status() . "', info = 'update', sendersigned =  '" . $this->iSenderSigned . "', receiversigned = '" . $this->iReceiverSigned . "' where id = " . $this->iID . ";"); 
				$oDB->execute(); 
			}  
			//echo $oDB->sql(); 
		}
		
		public function code($strCode = NULL) {
			if (!is_null($strCode)) $this->strCode = $strCode; 
			return $this->strCode;  
		}
		public function physical($iPhysical = NULL) {
			if (!is_null($iPhysical)) $this->iPhysical = intval($iPhysical); 
			return $this->iPhysical; 
		}
		public function mental($iMental = NULL) {
			if (!is_null($iMental)) $this->iMental = intval($iMental); 
			return $this->iMental; 
		}
		public function emotional($iEmotional = NULL) {
			if (!is_null($iEmotional)) $this->iEmotional = intval($iEmotional); 
			return $this->iEmotional; 
		}
		public function social($iSocial = NULL) {
			if (!is_null($iSocial)) $this->iSocial = intval($iSocial); 
			return $this->iSocial; 
		}
		public function credits($iCredits = NULL) {
			if (!is_null($iCredits)) $this->iCredits = intval($iCredits); 
			return $this->iCredits; 
		}
		
		public function market($iMarket = NULL) {
			if (!is_null($iMarket)) $this->iMarket = intval($iMarket); 
			return new owaesitem($this->iMarket); 
		}
		 
		
		public function status($iStatus = NULL) {
			if (!is_null($iStatus)) $this->iStatus = intval($iStatus); 
			return $this->iStatus; 
		}
		
		public function initiator($iInitiator = NULL) {
			if (!is_null($iInitiator)) $this->iInitiator = intval($iInitiator); 
			if (is_null($this->iInitiator)) $this->load(); 
			return user($this->iInitiator); 
		}
		
		public function sign() { 
			$this->signed($this->sender(), TRUE); 
			$this->signed($this->receiver(), TRUE); 
			$this->update(); 
			
			$oDB = new database(); 
			$oDB->execute("insert into tblPayments (datum, sender, receiver, credits, reason, link) values ('" . owaesTime() . "', '" . $this->sender() . "', '" . $this->receiver() . "', '" . $this->credits() . "', 1, '" . $this->market()->id() . "'); "); 
			
			$oDB->execute("insert into tblIndicators (user, datum, physical, mental, emotional, social, reason, link) values ('" . $this->receiver() . "', '" . owaesTime() . "', '" . $this->physical() . "', '" . $this->mental() . "', '" . $this->emotional() . "', '" . $this->social() . "', '4', '" . $this->market()->id() . "'); "); 
			$arCheckUsers = array($this->sender(), $this->receiver());
			foreach ($arCheckUsers as $iUser) {
				$oUser = user($iUser); 
				$oDB->execute("select count(id) as aantal from tblPayments where sender = " . $iUser . " or receiver = " . $iUser . " and actief = 1;"); 
				switch ($oDB->get("aantal")) {
					case 1: 
						$oUser->addBadge("1transaction"); 
						break; 
					case 10: 
						$oUser->addBadge("10transactions"); 
						break; 
					case 25: 
						$oUser->addBadge("25transactions"); 
						break; 
					case 50: 
						$oUser->addBadge("50transactions"); 
						break; 
				}
			}
		} 
		 /*
		public function signed($iPerson = NULL, $bValue = NULL) {
			if (is_null($iPerson)) $iPerson = me(); 
			if (!is_null($bValue)) {
				if ($iPerson == $this->sender()) $this->iSenderSigned = ($bValue?owaesTime():NO); 
				if ($iPerson == $this->receiver()) $this->iReceiverSigned  = ($bValue?owaesTime():NO); 
				if (($this->iSenderSigned != NO)||($this->iReceiverSigned != NO)) $this->status(TRANSACTION_STATE_WAITING); 
				if (($this->iSenderSigned != NO)&&($this->iReceiverSigned != NO)) $this->status(TRANSACTION_STATE_COMPLETED); 
			}
			if ($iPerson == $this->sender()) if (is_null($this->iSenderSigned)) $this->load(); 
			if ($iPerson == $this->receiver()) if (is_null($this->iReceiverSigned)) $this->load(); 
			return ((($iPerson == $this->sender()) ? $this->iSenderSigned : $this->iReceiverSigned) != NO); 
		} 
		*/ 
		
		private function formkey() {
			return md5( (($this->status() == TRANSACTION_STATE_NEW) ? "new" : $this->code()) . ":" . $this->sender() . ">" . $this->receiver() . "+sleutelken");
		} 
	}
	
?>