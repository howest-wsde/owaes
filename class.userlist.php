<?php
	class userlist { /*
			wordt gebruikt om een lijst te genereren van gebruikers, bv. op gebruikerspagina of groepspagina. 
		*/
		private $arUserlist = NULL; 
		private $arSQLwhere = array(); 
		private $arSQLjoin = array(); 
		private $arOrder= array(); 
		 
		public function userlist() { 
			$this->arOrder[] = "u.lastname"; 
		}
		
		public function filter($strType) {
			switch(strtolower($strType)) {
				case "visible":
					$this->arSQLwhere["visible"] = " visible = 1 "; 
					break; 
				case "friends":
					$this->arSQLjoin["friends"] = " inner join tblFriends f on (f.user = " . me() . " and friend = u.id and confirmed = 1) "; 
					break; 
			}
		}
		
		public function group($iGroup) { // enkel de gebruikers die in een bepaalde groep zitten 
			$this->arSQLjoin["group"] = "inner join tblGroupUsers gu on gu.user = u.id and gu.groep = " . intval($iGroup);  
		}
		

		public function search($strSearch) {  
			$arSearchQRY = array();  
			$arFields = array(
				"u.alias", 
				"u.lastname", 
				"u.firstname", 
				"u.description", 
			); 
			preg_match_all("/[a-zA-Z0-9_-]+/", $strSearch, $arMatches, PREG_SET_ORDER);   
			foreach ($arMatches as $arSearch) { 
				$arFieldSearches = array(); 
				foreach ($arFields as $strField) $arFieldSearches[] = "$strField like '%" . $arSearch[0] . "%'";  
				$arSearchQRY[] = "(" . implode(" or ", $arFieldSearches) . ")"; 
			} 
			$this->arSQLwhere["search"] = implode(" and ", $arSearchQRY);  
		}
		
		public function getList() { // returns een array van users (class.user)
			if (is_null($this->arUserlist)) {
				$arUserlist = array();
				$strSQL = "select u.* from tblUsers u "; 
				foreach($this->arSQLjoin as $strKey=>$strVal) { 
					$strSQL .= " $strVal ";
				} 
				if (count($this->arSQLwhere)>0) {
					$strSQL .= " where u.deleted = 0 "; 
					foreach($this->arSQLwhere as $strKey=>$strVal) { 
						$strSQL .= " and (" . $strVal . ")"; 
					} 
				} 
				$strSQL .= " order by " . implode(",", $this->arOrder); 
				$oOWAES = new database($strSQL, true); 
				while ($oOWAES->nextRecord()) { 
					$oUser = new user($oOWAES->get("id")); 

					array_push ($arUserlist, $oUser);  
				}  
				$this->arUserlist = $arUserlist; 
			}
			return $this->arUserlist;
		}
		 

	}
	
?>