<?php 
	
	class transactions { /* OVERVIEW OF ALL TRANSACTIONS  */
		private $arTransactions = NULL; 
		private $arWhere = array();  
		private $arJoin = array();  
		
		public function transactions () {
			
		}	
		
		public function result() {  // returns array van class.transaction's
			if (is_null($this->arTransactions)) {
				$strSQL = "select t.* from tblTransactions t ";
				foreach ($this->arJoin as $strKey=>$strValue) {
					$strSQL .= " $strValue ";	
				} 
				$strSQL .= " where 1 = 1 "; 
				foreach ($this->arWhere as $strKey=>$strValue) {
					$strSQL .= " and (" . $strValue . ") ";	
				} 
				$oResult = new database($strSQL, TRUE); 
				$this->arTransactions = array(); 
				while ($oResult->nextRecord()) {
					if ($oResult->get("state") != SUBSCRIBE_CANCEL) {
						$oSubscription = new transaction(); 
						$oSubscription->user($oResult->get("user")); 
						$oSubscription->market($oResult->get("market")); 
						$oSubscription->state($oResult->get("status")); 
						$this->arTransactions[] = $oSubscription; 
					} 
				}
			}
			return $this->arTransactions; 	
		}
		
	
		public function filter($strKey, $oValue) { // $strKey = "market", "sender", "receiver", "user" or "state"
			switch (strtolower($strKey)) {
				case "market": 
					$this->arWhere["market"] = "t.market = " . intval($oValue); 
					$this->arSubscriptions = NULL; 
					break; 	
				case "user": 
					$this->arWhere["user"] = "t.receiver = " . intval($oValue) . " or t.sender = " . intval($oValue); 
					$this->arSubscriptions = NULL; 
					break; 		
				case "sender": 
					$this->arWhere["user"] = "t.sender = " . intval($oValue); 
					$this->arSubscriptions = NULL; 
					break; 	
				case "receiver": 
					$this->arWhere["user"] = "t.receiver = " . intval($oValue); 
					$this->arSubscriptions = NULL; 
					break; 	
				case "state": 
					$this->arWhere["state"] = "t.state = " . intval($oValue); 
					$this->arSubscriptions = NULL; 
					break; 	
				default: 
					error("$strKey wordt niet herkend als filter (class.transactions)");  
			}
		}
	}
 
?>