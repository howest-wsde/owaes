<?
define ("SUBSCRIBE_CANCEL", -1);
define ("SUBSCRIBE_SUBSCRIBE", 0); 
define ("SUBSCRIBE_CONFIRMED", 2);
define ("SUBSCRIBE_DECLINED", 3);
define ("SUBSCRIBE_ANNULATION", 4);
 

class subscription {
	private $iMarket = NULL; 
	private $iUser = NULL; 
	private $iStatus = NULL;   
	private $oPayment = NULL; 
	private $arRating = array(); 
	
	public function subscription() { // status van een "schrijf in" of "onderhandel" bij een owaes-item. 
		
	}
	
	public function user($iUser = NULL) { // (optional) sets user (by id) and (always) returns user-class
		if (!is_null($iUser)) $this->iUser = intval($iUser); 
		if (is_null($this->iUser)) $this->load(); 
		return user($this->iUser); 
	}
	
	public function market($iMarket = NULL) { // (optional) sets user (by id) and (always) returns user-class
		if (!is_null($iMarket)) $this->iMarket = intval($iMarket); 
		if (is_null($this->iMarket)) $this->load(); 
		return owaesitem($this->iMarket); 
	}
	
	public function state($iStatus = NULL){ // (optional) sets state (by value) and (always) returns state-value
		if (!is_null($iStatus)) $this->iStatus = $iStatus; 
		if (is_null($this->iStatus)) $this->load(); 
		return $this->iStatus; 
	}
	
	
	public function payment() {
		if (is_null($this->oPayment)) $this->oPayment = new payment(array(
															"sender" => ($this->market()->task() ? $this->market()->author()->id() : $this->user()->id()), 
															"receiver" => ($this->market()->task() ? $this->user()->id() : $this->market()->author()->id()),
															"market" => $this->market()->id(),  
															"credits" => $this->market()->credits(),  
														)); 
		return $this->oPayment; 
	}
	
	public function rating($iUser = NULL) {
		if (is_null($iUser)) $iUser = me(); 
		if (!isset($this->arRating[$iUser])) $this->arRating[$iUser] = new rating(array(
															"sender" => $iUser, 
															"receiver" => (($this->market()->author()->id()==$iUser) ? $this->user()->id() : $this->market()->author()->id()), 
															"market" => $this->market()->id(),  
														)); 
		return $this->arRating[$iUser]; 
	}
	
	public function load() { // TODO: mag private? 
		if (is_null($this->iMarket)) error("Market niet gedefinieerd (class.subscriptions.php)");
		if (is_null($this->iUser)) error("User niet gedefinieerd (class.subscriptions.php)"); 
		$strSQL = "select * from tblMarketSubscriptions where market = " . $this->iMarket . " and user = " . $this->iUser . " order by id desc limit 1; ";
		$oDB = new database($strSQL, TRUE); 
		if ($oDB->length() == 1) {
			$this->state($oDB->get("status"));
		} else $this->state(SUBSCRIBE_CANCEL);
	}
	
	public function save() { // opslaan na statusaanpassing 
		if (is_null($this->iMarket)) error("Market niet gedefinieerd (class.subscriptions.php)");
		if (is_null($this->iUser)) error("User niet gedefinieerd (class.subscriptions.php)"); 
		if (is_null($this->iStatus)) error("Status niet gedefinieerd (class.subscriptions.php)"); 
		$oItem = owaesitem($this->iMarket); 
		$oItem->load(); 
		$oDB = new database(); 
		$oDB->execute("update tblMarketSubscriptions set overruled = 1 where market = " . $this->iMarket . " and user = " . $this->iUser . ";");  
		$oDB->execute("insert into tblMarketSubscriptions (market, user, status, doneby, clickdate, snap) values (" . $this->iMarket . ", " . $this->iUser . ", " . $this->iStatus . ", " . me() . ", " . owaesTime() . ", '" . $oDB->escape(json_encode($oItem->snap())) . "'); ");  
		$iSaveID = $oDB->lastInsertID(); 
		switch($this->state()) {
			case SUBSCRIBE_CANCEL:  
				break; 
			case SUBSCRIBE_SUBSCRIBE :  
				$oDB->execute("update tblIndicators set actief = 0 where user = " . $this->iUser . " and link = " . $this->iMarket . "; "); 
				// TODO: eventuele vorige timeouts moeten gecompenseerd worden
				$oDB->execute("insert into tblIndicators (user, datum, physical, mental, emotional, social, reason, link) values (" . $this->iUser . ", " . owaesTime() . ", 0, 0, 0, 0, " . TIMEOUT_CLICKED . ", " . $this->iMarket . "); "); 
				break;  
			case SUBSCRIBE_CONFIRMED:  
				$oMarket = owaesitem($this->iMarket);  
				$iMultiplier = settings("indicatoren", "multiplier") ? settings("indicatoren", "multiplier") : 1;
				$oDB->execute("insert into tblIndicators 
									(user, datum, physical, mental, emotional, social, reason, link)
									values (" . $this->iUser . ", " . owaesTime() . ", " . ($oMarket->physical()/25*$iMultiplier) . ", 
										" . ($oMarket->mental()/25*$iMultiplier) . ", " . ($oMarket->emotional()/25*$iMultiplier) . ", 
										" . ($oMarket->social()/25*$iMultiplier) . ", " . TIMEOUT_CONFIRMED . ", " . $this->iMarket . "); ");  
				break; 
			case SUBSCRIBE_DECLINED:  
				break;  
		}
	}
	
	
} 