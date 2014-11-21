<?php
 
	class grouplist { /*
		overzicht van alle of gefilterde groepen
	*/
		private $arList = NULL; 
		private $arJoin = array(); 
		private $arWhere = array(); 
		private $arOrder = array(); 
		 
		public function grouplist() {  
			 $this->arWhere[] = " g.deleted = 0 ";  
		}
		
		public function user($iUser) { // select only groups where this user exists
			$this->arJoin["user" . $iUser] = " inner join tblGroupUsers ug" . $iUser . " on g.id = ug" . $iUser . ".groep and ug" . $iUser . ".user = " . $iUser . " and ug" . $iUser . ".confirmed = 1 ";  
			$this->reset(); 
		}
		
		public function reset() { // reset resultaat. TODO: checken of dit niet private mag
			$this->arList = NULL; 	
		}
		
		public function order($strOrder) { // reset resultaat. TODO: checken of dit niet private mag
			$this->arOrder[] = $strOrder; 
		}
		
		
		public function search($strSearch) {  
			$arSearchQRY = array();  
			$arFields = array(
				"g.naam", 
				"g.website", 
				"g.info", 
			); 
			preg_match_all("/[a-zA-Z0-9_-]+/", $strSearch, $arMatches, PREG_SET_ORDER);      
			if(count($arMatches)>0) {
				foreach ($arMatches as $arSearch) { 
					$arFieldSearches = array(); 
					foreach ($arFields as $strField) $arFieldSearches[] = "$strField like '%" . $arSearch[0] . "%'";  
					$arSearchQRY[] = "(" . implode(" or ", $arFieldSearches) . ")"; 
				} 
				$this->arWhere[] = implode(" and ", $arSearchQRY);  
			}
		}
		
		public function load() { /* lijst creeren (moet niet perse aangeroepen worden, wordt ook aangeroepen vanuit 'getList') 
		 	TODO: Checken of dit niet private mage
		*/
			$this->arList = array(); 
			
			$oDB = new database(); 
			
			$strSQL = "select g.* from tblGroups g ";
			foreach ($this->arJoin as $strJoin) $strSQL .= $strJoin; 
			if (count($this->arWhere)>0) $strSQL .= " where " . implode(" and ", $this->arWhere); 
			$this->arOrder[] = "g.naam"; 
			$strSQL .= " order by " . implode(",", $this->arOrder); 
			$oDB->sql($strSQL); 
			$oDB->execute();    
			while ($oDB->nextRecord()) {
				$oGroep = new group(); 
				$oGroep->id($oDB->get("id")); 
				$oGroep->naam($oDB->get("naam")); 
				$oGroep->info($oDB->get("info")); 
				$oGroep->admin($oDB->get("admin")); 
				$oGroep->website($oDB->get("website")); 
				$this->arList[] = $oGroep; 
			}	
		}
		
		public function getList() { // returns array van groepen (class.group)
			if (is_null($this->arList)) $this->load(); 
			return $this->arList; 
		}

	}
	