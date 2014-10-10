<?
 
class subscriptions {
	private $arSubscriptions = NULL; 
	private $arWhere = array();  
	private $arJoin = array();  
	
	public function subscriptions($arFilter = array()) { // overzicht van alle inschrijvingen op een owaes-item
		// $this->arJoin["onlylastrecord"] = " left join tblMarketSubscriptions m2 on (m.user = m2.user and m.market = m2.market and m.id < m2.id)"; 
		foreach ($arFilter as $strKey=>$oValue) {
			switch(strtolower($strKey)) {
				case "market": 
				case "user": 
				case "state": 
					$this->filter($strKey, $oValue); 
					break; 
			}	
		}
		$this->arWhere["onlylastrecord"] = "m.overruled = 0 "; 
	}
	
	public function filter($strKey, $oValue) { // $strKey = "market", "user" or "state" / $oState = INT of array van INTs
		$strCompare = (is_array($oValue)) ? 
					(" in (" . implode(",", $oValue) . ") ") :
					(" = " . $oValue);
		switch (strtolower($strKey)) {
			case "market": 
				$this->arWhere["market"] = "m.market " . $strCompare;
				$this->arSubscriptions = NULL; 
				break; 	
			case "user": 
				$this->arWhere["user"] = "m.user " . $strCompare;
				$this->arSubscriptions = NULL; 
				break; 	
			case "state": 
				$this->arWhere["state"] = "m.status " . $strCompare;
				$this->arSubscriptions = NULL; 
				break; 	
			default: 
				error("$strKey wordt niet herkend als filter (class.subscriptions)");  
		}
	}
		
	public function result() { // returns array van class.subscription's
		if (is_null($this->arSubscriptions)) {
			$strSQL = "select m.* from tblMarketSubscriptions m ";
			foreach ($this->arJoin as $strKey=>$strValue) {
				$strSQL .= " $strValue ";	
			} 
			$strSQL .= " where 1 = 1 "; 
			foreach ($this->arWhere as $strKey=>$strValue) {
				$strSQL .= " and (" . $strValue . ") ";	
			}  
			$oResult = new database($strSQL, TRUE); 
			// echo ($oResult->table(TRUE)); 
			$this->arSubscriptions = array(); 
			while ($oResult->nextRecord()) {
				if ($oResult->get("status") != SUBSCRIBE_CANCEL) {
					$oSubscription = new subscription(); 
					$oSubscription->user($oResult->get("user")); 
					$oSubscription->market($oResult->get("market")); 
					$oSubscription->state($oResult->get("status")); 
					$this->arSubscriptions[] = $oSubscription; 
				} 
			}
		}
		return $this->arSubscriptions; 
	} 
	 
}
 