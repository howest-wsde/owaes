<?php  
	class paymentlist {  
		private $arList = NULL; 
		private $iSender = NULL; 
		private $iReceiver = NULL; 
		private $iSenderGroup = NULL; 
		private $iReceiverGroup = NULL; 
		private $iMarket = NULL; 


		
		public function paymentlist($arArguments) {
			foreach ($arArguments as $strKey=>$oValue) {
				switch(strtolower($strKey)) {
					case "sender": 
						$this->sender($oValue); 
						break; 
					case "receiver": 
						$this->receiver($oValue); 
						break; 
					case "sendergroup": 
						$this->sender(NULL, $oValue); 
						break; 
					case "receivergroup": 
						$this->receiver(NULL, $oValue); 
						break; 
					case "market": 
						$this->market($oValue); 
						break;
					default: 
						error("class.payment.list : $strKey is ongeldig argument"); 
				}	
			}	
		}
		
		public function sender($iSender = NULL, $iGroup = NULL){
			if (!is_null($iSender)) $this->iSender = $iSender; 
			if (!is_null($iGroup)) $this->iSenderGroup = $iGroup; 
			return $this->iSender; 	
		}
		 
		public function receiver($iReceiver = NULL){
			if (!is_null($iReceiver)) $this->iReceiver = $iReceiver; 
			if (!is_null($iGroup)) $this->iReceiverGroup = $iGroup; 
			return $this->iReceiver; 	
		}
		 
		public function market($iMarket = NULL){
			if (!is_null($iMarket)) $this->iMarket = $iMarket; 
			return $this->iMarket; 	
		}
		
		public function getList(){
			if (is_null($this->arList)) {
				$arWhere = array(); 
				if (!is_null($iMarket)) $arWhere[] = "(market = " . $iMarket . " or voorschot = " . $iMarket . ")"; 
				if (!is_null($iSender)) $arWhere[] = "sender = " . $iSender; 
				if (!is_null($iReceiver)) $arWhere[] = "receiver = " . $iReceiver; 
				if (!is_null($iSenderGroup)) $arWhere[] = "sendergroup = " . $iSender; 
				if (!is_null($iReceiverGroup)) $arWhere[] = "receivergroup = " . $iReceiver; 
				$this->arList = array(); 
				$oDB = new database("select * from tblPayments where " . implode(" and ", $arWhere) . " order by id desc;" , TRUE); 
				while ($oDB->nextRecord()) {
					$oPayment = new payment(array(
						"id" => $oDB->get("id"), 
						"sender" => $oDB->get("sender"), 
						"receiver" => $oDB->get("receiver"), 
						"sendergroup" => $oDB->get("sendergroup"), 
						"receivergroup" => $oDB->get("receivergroup"), 
						"initiator" => $oDB->get("initiator"), 
						"market" => $oDB->get("market"), 
						"voorschot" => ($oDB->get("voorschot")!=0), 
					)); 
					if ($oPayment->voorschot()) $oPayment->market($oDB->get("market")); 
					$this->arList[] = $oPayment; 
				}
			}
			return $this->arList;  
		} 
	}
