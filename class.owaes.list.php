<?php
	class owaeslist {  
		private $iUser = 0;  // current user
		private $bLoaded = FALSE;  // when calling function getList, class checks if list has to be re-generated ; bLoaded == FALSE > generate
		private $arOWAESlist = array(); // actual result
		private $arSQLwhere = array(); 
		private $arOrder = array();  
		private $arScore = array(); 
		private $arFilter = array(); 
		private $arSQLjoin = array(); 
		private $iLimit = 100; 
		private $iStart = 0; 
		private $arDetails = array(); 
		 
		public function owaeslist() { 
			$this->arOrder[] = "enkalkuli desc";   
			$this->arOrder[] = "date desc";    
		 
		}
		 
		public function setUser($oUser) {
			$this->iUser = $oUser->id(); 	
		}
		
		public function payment($iUser, $strValue) {
			switch(strtolower($strValue)) { 
				case 1: 
				case "yes": 
				case "true": 
						$this->arSQLjoin["payment" . $iUser] = " inner join tblMarketSubscriptions tt" . $iUser . " 
											on (tt" . $iUser . ".user = " . $iUser . " or m.author = " . $iUser . ") 
												and tt" . $iUser . ".market = m.id 
												and tt" . $iUser . ".overruled = 0 
												and tt" . $iUser . ".status = " . SUBSCRIBE_CONFIRMED . " " ; 
						$this->arSQLwhere["payment" . $iUser] = " m.id in (select market from tblPayments where sender = " . $iUser . " or receiver = " . $iUser . ")" ;  

					break; 
				case 0: 
				case "no": 
				case "false": 
						$this->arSQLjoin["payment" . $iUser] = " inner join tblMarketSubscriptions tt" . $iUser . " 
											on (tt" . $iUser . ".user = " . $iUser . " or m.author = " . $iUser . ") 
												and tt" . $iUser . ".market = m.id 
												and tt" . $iUser . ".overruled = 0 
												and tt" . $iUser . ".status = " . SUBSCRIBE_CONFIRMED . ""; 
						$this->arSQLwhere["payment" . $iUser] = " m.id not in (select market from tblPayments where sender = " . $iUser . " or receiver = " . $iUser . ")" ;  
					break; 
			}
		}
		
		
		public function rated($iUser, $strValue) {
			switch(strtolower($strValue)) { 
				case 1: 
				case "yes": 
				case "true": 
						$this->arSQLjoin["rating" . $iUser] = " inner join tblMarketSubscriptions ttr" . $iUser . " 
											on (ttr" . $iUser . ".user = " . $iUser . " or m.author = " . $iUser . ") 
												and ttr" . $iUser . ".market = m.id 
												and ttr" . $iUser . ".overruled = 0 
												and ttr" . $iUser . ".status = " . SUBSCRIBE_CONFIRMED . " " ; 
						$this->arSQLwhere["rating" . $iUser] = " m.id in (select market from tblStars where sender = " . $iUser . ")" ;  

					break; 
				case 0: 
				case "no": 
				case "false": 
						$this->arSQLjoin["rating" . $iUser] = " inner join tblMarketSubscriptions ttr" . $iUser . " 
											on (ttr" . $iUser . ".user = " . $iUser . " or m.author = " . $iUser . ") 
												and ttr" . $iUser . ".market = m.id 
												and ttr" . $iUser . ".overruled = 0 
												and ttr" . $iUser . ".status = " . SUBSCRIBE_CONFIRMED . ""; 
						$this->arSQLwhere["rating" . $iUser] = " m.id not in (select market from tblStars where sender = " . $iUser . ")" ;  
					break; 
			}
		}
		
		public function subscribed($iUser, $strValue) { // filtert items waar $iUser al dan niet ingeschreven is
			$arValues = array(); 
			switch($strValue) { 
				case "yes":
					$arValues = array(SUBSCRIBE_SUBSCRIBE, SUBSCRIBE_CONFIRMED);  
					break; 
				case "confirmed": 
					$arValues = array(SUBSCRIBE_CONFIRMED);  
					break; 	
				case "notconfirmed": 
					$arValues = array(SUBSCRIBE_SUBSCRIBE);  
					break; 	
			}
			switch($strValue) { 
				case "yes":
				case "confirmed":
				case "notconfirmed": 
					$this->arSQLjoin["subscribed" . $iUser] = " inner join tblMarketSubscriptions subsc" . $iUser . " 
											on (m.id = subsc" . $iUser . ".market 
												and subsc" . $iUser . ".overruled = 0 
												and subsc" . $iUser . ".user = $iUser 
												and subsc" . $iUser . ".status in (" . implode(",", $arValues) . ")) ";  
					break; 	
			}

			// $this->arSQLwhere["subscribed" . $iUser] = "tr.id IS NULL or tr.status != " . TRANSACTION_STATE_COMPLETED . "" ; 

		}
		
		public function limit($iLimit = NULL) { // get / set item count
			if (!is_null($iLimit)) $this->iLimit = $iLimit; 
			return $this->iLimit; 			
		}
		
		public function offset($iStart = NULL) { // get / set first item offset 
			if (!is_null($iStart)) $this->iStart = $iStart; 
			return $this->iStart; 			
		}
		
		public function enkalkuli($strField, $value, $value2=NULL) { /* doe sortering rekening houdend met ...
				bv. "social", 100 -> gaat minder social doen
					"mental", 10 -> zoekt achter items met "mental"
					"location", 20.3, 50.2 => zoekt op geografische nabijheid
			*/
			$this->arDetails[$strField] = is_null($value2) ? $value : array($value, $value2); 
			switch(strtolower($strField)) {
				case "social":
				case "mental":
				case "physical": 
				case "emotional": 
					$this->arDetails["indicatoren"][$strField] = $value; 
					switch(count($this->arDetails["indicatoren"])) {
						case 1:  
							if (intval($value) < 40) $this->arScore["indic." . $strField] = "(m.$strField / 111)+0.10"; 
							if (intval($value) > 70) $this->arScore["indic." . $strField] = "1-(m.$strField / 111)";  
						default: 
							$iTotal = 0; 
							$iMax = 0;
							$iMin = 100; 
							foreach ($this->arDetails["indicatoren"] as $strKey=>$iVal) {
								$iTotal+=$iVal;
								if ($iMin > $iVal) $iMin = $iVal; 
								if ($iMax < $iVal) $iMax = $iVal; 
							}
							$iAVG = $iTotal / count($this->arDetails["indicatoren"]); 
							foreach ($this->arDetails["indicatoren"] as $strKey=>$iVal) {
								unset($this->arScore["indic." . $strField]); 
								if ($iMax - $iMin > 10) {
									if (intval($iVal) > $iAVG) $this->arScore["indic." . $strField] = "1-(m.$strField / 111)"; 
									if (intval($iVal) < $iAVG) $this->arScore["indic." . $strField] = "(m.$strField / 111)+0.10"; 
								} 
							}
							break; 	
					} 
					break; 
			}
		}
		
		public function getList() {
			if (!$this->bLoaded) {
				$arOWAESlist = array();
				$strEnkalkuli = "1"; 
				foreach ($this->arScore as $strKey=>$strVal) {
					$strEnkalkuli .= "* ($strVal)";
				}
				$strSQL = "select distinct m.*, ($strEnkalkuli) as enkalkuli from tblMarket m ";
				foreach($this->arSQLjoin as $strKey=>$strVal) { 
					$strSQL .= " $strVal ";
				} 
				if (count($this->arSQLwhere)>0) {
					$strSQL .= " where m.state != " . STATE_DELETED . " "; 
					foreach($this->arSQLwhere as $strKey=>$strVal) { 
						$strSQL .= " and (" . $strVal . ")"; 
					} 
				} 
				$strSQL .= " order by " . implode(",", $this->arOrder);  
				$strSQL .= " limit " . $this->offset() . ", " . $this->limit() . "";  
				$oOWAES = new database($strSQL, true); 
				//if ($bShowSQL)  echo $oOWAES->table(TRUE); 
// vardump($strSQL); 
 // console("main.php", $strSQL); 
 //echo ("$strSQL "); 
				//
				// echo $oOWAES->getTime(); 
				while ($oOWAES->nextRecord()) {  
					$oItem = owaesitem($oOWAES->get("id"));  
					$oItem->load($oOWAES->record()); 
					$bPassFilter = TRUE;
					foreach($this->arFilter as $strKey=>$strValue) {
						switch($strKey) {
							case "executor": 
								$arSubscriptions = $oItem->subscriptions();
								if (isset($arSubscriptions[$this->arFilter["executor"]])) if ($arSubscriptions[$this->arFilter["executor"]]->state() != SUBSCRIBE_CONFIRMED) $bPassFilter = FALSE;
								break; 	 
							case "unpayed": 
								// $oTransaction = new transaction($oItem->id(), $this->arFilter["unpayed"]);
								if (1==0) $bPassFilter = FALSE;
								break; 	
						}	
					}
					if ($bPassFilter) array_push ($arOWAESlist, $oItem);  
				} 
				$this->arOWAESlist = $arOWAESlist;
				$this->bLoaded = TRUE; 
			} 
			return $this->arOWAESlist;
		}
		
		public function filterByUser($iUser, $bComp = TRUE) {
			$this->arSQLwhere["user"] = " m.author " . ($bComp?"=":"!=") . " $iUser "; 
		}
		public function filterByGroup($iGroep, $bComp = TRUE) {
			$this->arSQLwhere["user"] = " m.groep " . ($bComp?"=":"!=") . " $iGroep "; 
		}
		
		public function filterByID($iID) {
			$this->arSQLwhere["id"] = " m.id = $iID "; 
		}
		
		public function filterByType($strType) {
			$this->arSQLjoin["type"] = " inner join tblMarketTypes mtypes on m.mtype = mtypes.id "; 
			$this->arSQLwhere["type"] = "mtypes.key = '" . $strType . "' ";  
			/*
			switch(strtolower($strType)) {
				case "market": 
					$this->arSQLwhere["type"] = "m.task = 0 ";  
					break; 
				case "work": 
					$this->arSQLwhere["type"] = "m.task = 1 "; 
					break; 
			} 
			*/
		}
		
		public function search($strSearch) {  
			$arSearchQRY = array();  
			$this->arSQLjoin["search"] = " left join tblMarketTags mt on m.id = mt.market "; 
			$arFields = array(
				"m.title", 
				"m.body", 
				"m.location", 
				"mt.tag", 
			); 
			preg_match_all("/[a-zA-Z0-9_-]+/", $strSearch, $arMatches, PREG_SET_ORDER);   
			foreach ($arMatches as $arSearch) { 
				$arFieldSearches = array(); 
				foreach ($arFields as $strField) $arFieldSearches[] = "$strField like '%" . $arSearch[0] . "%'";  
				$arSearchQRY[] = "(" . implode(" or ", $arFieldSearches) . ")"; 
			}  
			$this->arSQLwhere["search"] = implode(" and ", $arSearchQRY);  
		}
		
		public function filterByState($oState) { 
			$this->arSQLwhere[] = (is_array($oState)) ? ("m.state in (" . implode(",", $oState) . ")") : "m.state = $oState";  
		} 
		
		public function order($strOrder) {
			array_unshift($this->arOrder, $strOrder); 
		}
		
		public function filterByExecutor($iUser = NULL) {
			$this->arFilter["executor"] = $iUser;  
			if (is_null($iUser)) unset($this->arFilter["executor"]); 
		} 
		
		public function filterByCreator($iUser = NULL) { 
			if (is_null($iUser)) {
				unset($this->arSQLwhere["creator"]);
			} else {
				$this->arSQLwhere["creator"] = "m.author = $iUser "; 	
			}
		} 
		/*
		public function filterByUnpayed($iUser) {
			$this->arFilter["unpayed"] = $iUser; 
			$this->arSQLjoin["unpayed"] = " inner join tblMarketSubscriptions tt 
											on (tt.user = " . $iUser . " or m.author = " . $iUser . ") 
												and tt.market = m.id 
												and tt.overruled = 0 
												and tt.status = " . SUBSCRIBE_CONFIRMED . " 
										left join tblTransactions tr 
											on tr.market = m.id 
												and (tr.sender = " . $iUser . " or tr.receiver = " . $iUser . ") 
												and (tr.sender=tt.user or tr.receiver = tt.user) "; 
			$this->arSQLwhere["unpayed"] = "tr.id IS NULL or tr.status != " . TRANSACTION_STATE_COMPLETED . "" ; 
			if (is_null($iUser)) {
				unset($this->arFilter["unpayed"]); 
				unset($this->arSQLwhere["unpayed"]); 
				unset($this->arSQLjoin["unpayed"]); 
			}
		} 
		*/
	 
		public function hasWaarde($strWaarde) {
			//$this->arSQLjoin["cat" . $iCat] = "inner join tblMarketCategories mc" . $iCat . " on m.id = mc" . $iCat . ".market and mc" . $iCat . ".categorie = " . $iCat . " "; 
			switch (strtolower($strWaarde)) {
				case "physical": 
				case "social": 
				case "emotional": 
				case "mental": 
					$this->arSQLwhere["waarde" . $strWaarde] = "m.$strWaarde > 0 ";  
			}
		}
		
		public function filterPassedDate($iTime = 0) { // geen attribuut of 0: voorbij vandaag, integer: time, FALSE of niet-integer: geen filter
			if ($iTime == 0) $iTime = owaesTime(); 
			if (is_numeric($iTime)) { 
				$this->arSQLwhere["date"] = " (md.start > " . ($iTime-6*60*60) . ") or (md.start is NULL and m.lastupdate > " . ($iTime-(60*60*24*30)) . ")  or (md.start = 0 and m.lastupdate > " . ($iTime-(60*60*24*30)) . ")  " ;  // start > 6 uur geleden of laatst updated > maand geleden 
				$this->arSQLjoin["date"] = "left join (select market, max(datum) as start from tblMarketDates group by market) md on m.id = md.market"; 
			} else {
				unset($this->arSQLwhere["date"]); 
				unset($this->arSQLjoin["date"]);  
			} 
		}
		
	}
	
?>