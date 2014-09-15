<?php
 
 	$ar_GLOBAL_groups = array(); 
	
	function group($iID = NULL) { // FUNCTION group(5) == CLASS new group(5) , maar met call by ref
		global $ar_GLOBAL_groups; 
		if (!isset($ar_GLOBAL_groups[$iID])) {
			$oGroep = new group($iID); 
			$ar_GLOBAL_groups[$iID] = &$oGroep; 
		}
		return $ar_GLOBAL_groups[$iID]; 
	}
	
	class group { 
		private $iID = 0;  
		private $strNaam = NULL;
		private $strAlias = NULL;  
		private $strInfo = NULL;  
		private $iAdmin = NULL;  
		private $arUsers = NULL;  
		private $strImage = NULL;
		private $iLastUpdate = NULL; 
		private $arUserRights = array(); 
		 
		public function group($strKey=NULL) { // $strKey = ID or ALIAS // when not defined: create new user  
			if (!is_null($strKey)) {
				if (is_numeric($strKey)) { 
					$this->id($strKey);
				} else {
					$this->alias($strKey); 
				} 
			} else { 
				$this->id(0); 
			}
		}
		
		public function id($iID = NULL) {
			if (!is_null($iID)) $this->iID = $iID; 
			return intval($this->iID); 
		}
		
		public function admin($iAdmin = NULL) {
			if (!is_null($iAdmin)) $this->iAdmin = $iAdmin; 
			if (is_null($this->iAdmin)) $this->load(__LINE__); 
			return user($this->iAdmin); 
		}
		
		public function naam($strNaam = NULL) {
			if (!is_null($strNaam)) $this->strNaam = $strNaam; 
			if (is_null($this->strNaam)) $this->load(__LINE__); 
			return $this->strNaam; 
		}
		
		public function alias($strAlias = NULL) {
			if (!is_null($strAlias)) $this->strAlias = $strAlias; 
			if (is_null($this->strAlias)) $this->load(__LINE__); 
			return $this->strAlias; 
		}
		
		public function info($strInfo = NULL) {
			if (!is_null($strInfo)) $this->strInfo = $strInfo; 
			return $this->strInfo; 
		}
		
		public function image($strImage = NULL) {
			if (!is_null($strImage)) $this->strImage = $strImage; 
			if (is_null($this->strImage)) $this->load(__LINE__); 
			return $this->strImage; 
		}
		
		

		public function getImage($strSize="100x100", $bHTML = TRUE) {  
			$iWidth = 100; 
			$iHeight = 100; 
			$arSize = explode("x", $strSize);
			switch(count($arSize)) {
				case 2: 
					if (is_numeric($arSize[0])) $iWidth = $arSize[0];
					if (is_numeric($arSize[1])) $iHeight = $arSize[1];
					break; 
				case 1: 
					if (is_numeric($arSize[0])){
						$iWidth = $arSize[0]; 
						$iHeight = $arSize[0]; 
					} 
					break;  
			}
			$strIMG = fixpath("groupimg.php?id=" . $this->id() . "&w=" . $iWidth . "&h=" . $iHeight . "&v=" . ($this->lastupdate()%500));   
			if ($bHTML) {
				return "<img src=\"$strIMG\" alt=\"" . $this->naam() . "\" width=\"" . $iWidth . "\" height=\"" . $iHeight . "\" />"; 	 
			} else {
				return $strIMG ; 
			} 
		} 

		private function lastupdate($iLastUpdate = NULL) {
			if (!is_null($iLastUpdate)) $this->iLastUpdate = $iLastUpdate; 
			if (is_null($this->iLastUpdate)) $this->load(__LINE__); 
			return $this->iLastUpdate; 
		}
		
		public function users($iUser = NULL) { // if iUser is specified: return user, otherwise return array with users
			if (is_null($this->arUsers)) {
				$oUsers = new userlist(); 
				$oUsers->group($this->id()); 
				$this->arUsers = $oUsers->getList(); 
			}
			if (is_null($iUser)) {
				return $this->arUsers; 
			} else {
				foreach ($this->arUsers as $oUser) if ($oUser->id() == $iUser) return $oUser; 
				return FALSE; 
			}
		}
		
		public function addUser($iUser) {
			if ($this->userrights(me())->useradd()) {
				$this->arUsers = NULL; 
				$oDB = new database(); 
				$strSQL = "insert into tblGroupUsers (groep, user) values (" . $this->id() . ", " . $iUser . "); "; 
				$oDB->execute($strSQL); 
				return TRUE; 
			} else return FALSE; 
		}
		
		public function removeUser($iUser) {
			if ($this->userrights(me())->userdel()) {
				$this->arUsers = NULL; 
				$oDB = new database(); 
				$strSQL = "delete from tblGroupUsers where groep = " . $this->id() . " and user = " . $iUser . "; "; 
				$oDB->execute($strSQL); 
				return TRUE; 
			} else return FALSE; 
		}
		
		public function userrights($iUser = NULL) { 
			if (is_null($iUser)) $iUser = me(); 
			if (!isset($this->arUserRights[$iUser])) {
				 $this->arUserRights[$iUser] = new usergrouprights($this, $iUser); 
			}
			return $this->arUserRights[$iUser]; 
		}
		
		public function getURL() {
			return fixPath("group.php?id=" . $this->id()); 
		}
		public function getLink($bHTML = TRUE) { // link to article details  
			return "<a href=\"" . $this->getURL() . "\">" . $this->naam() . "</a>"; 
		}
		
		
		public function load() {
			if ($this->id()	!= 0) {
				$strSQL = "select * from tblGroups where id = " . $this->id() . "; "; 
				$oDB = new database($strSQL, TRUE); 
				if ($oDB->record()) {
					if (is_null($this->strNaam)) $this->naam($oDB->get("naam")); 
					if (is_null($this->strAlias)) $this->alias($oDB->get("alias")); 
					if (is_null($this->strInfo)) $this->info($oDB->get("info")); 
					if (is_null($this->iAdmin)) $this->admin($oDB->get("admin")); 
					if (is_null($this->strImage)) $this->image($oDB->get("img")); 
					if (is_null($this->iLastUpdate)) $this->lastupdate(0); 
				} else {
					if (is_null($this->strNaam)) $this->naam(""); 
					if (is_null($this->strAlias)) $this->alias(""); 
					if (is_null($this->strInfo)) $this->info(""); 
					if (is_null($this->iAdmin)) $this->admin("");
					if (is_null($this->strImage)) $this->image("");
					if (is_null($this->iLastUpdate)) $this->lastupdate(owaesTime()); 
				}
			} else {
				if (is_null($this->strNaam)) $this->naam(""); 
				if (is_null($this->strAlias)) $this->alias(""); 
				if (is_null($this->strInfo)) $this->info(""); 
				if (is_null($this->iAdmin)) $this->admin("");
				if (is_null($this->strImage)) $this->image("");
				if (is_null($this->iLastUpdate)) $this->lastupdate(owaesTime()); 
			}
		}
		
		public function update() {
			$arVelden = array(); 
			if (!is_null($this->strNaam)) $arVelden["naam"] = $this->strNaam; 
			if (!is_null($this->strAlias)) $arVelden["alias"] = $this->strAlias; 
			if (!is_null($this->strInfo)) $arVelden["info"] = $this->strInfo; 
			if (!is_null($this->iAdmin)) $arVelden["admin"] = $this->iAdmin; 
			if (!is_null($this->strImage)) $arVelden["img"] = $this->strImage; 
			$arVelden["lastupdate"] = owaesTime(); 

			$oDB = new database(); 
			if ($this->iID == 0) {
				$arFields = array(); 
				$arValues = array(); 
				foreach ($arVelden as $strField=>$strValue) {
					$arFields[] = $strField; 
					$arValues[] = "'" . $oDB->escape($strValue) . "'"; 
				} 
				$oDB->sql("insert into tblGroups (" . implode(",", $arFields) . ") values (" . implode(",", $arValues) . "); ");
				$oDB->execute(); 
				$this->id($oDB->lastInsertID()); 
			} else {
				$arUpdates = array(); 
				foreach ($arVelden as $strField=>$strValue) {
					$arUpdates[] = $strField . " = '" . $oDB->escape($strValue) . "'"; 
				} 
				$oDB->sql("update tblGroups set " . implode(",", $arUpdates) . " where id = " . $this->id() . ";"); 
				$oDB->execute(); 
			}	
		}
		
		public function html($strTemplate = "") {
			$strHTML = content($strTemplate);  
			$arActions = array(); 
			$oRights = $this->userrights();  
			if ($oRights->groupinfo()) $arActions[] = "<a href=\"groupsettings.php?id=" . $this->id() . "\">aanpassen</a>"; 
			$strHTML = str_replace("[naam]", $this->naam(), $strHTML);
			$strHTML = str_replace("[link]", $this->getURL(), $strHTML);
			$strHTML = str_replace("[description]", $this->info(), $strHTML);
			$strHTML = str_replace("[actions]", implode("", $arActions), $strHTML);
			//$strHTML = preg_replace('/\[profileimg\:([0-9]*x[0-9]*)\]/e', '$this->getImage("$1", FALSE)', $strHTML);
			$strHTML = preg_replace_callback('/\[profileimg\:([0-9]*x[0-9]*)\]/', array(&$this, "imageregreplace"), $strHTML); 
			return $strHTML; 
		}
		
		private function imageregreplace(&$matches) { 
			return $this->getImage($matches[1], FALSE);  
		} 

	}
	
	class usergrouprights {
		private $arRights = array(
								"useradd" => NULL, 
								"userdel" => NULL, 
								"userrights" => NULL, 
								"owaesadd" => NULL, 
								"owaesedit" => NULL, 
								"owaesdel" => NULL, 
								"owaesselect" => NULL, 
								"owaespay" => NULL, 
								"groupinfo" => NULL, 
							); 
		private $oGroup = NULL;
		private $iUser = NULL;  
		
		public function usergrouprights($oGroup, $iUser) {
			$this->oGroup = $oGroup; 
			$this->iUser = $iUser;  
			if ($iUser==$oGroup->admin()->id()) { 
				foreach ($this->arRights as $strType=>$bVal) $this->arRights[$strType] = TRUE;  
			} else {
				foreach ($this->arRights as $strType=>$bVal) $this->arRights[$strType] = FALSE;  
				$oDB = new database(); 
				$oDB->sql("select * from tblGroupUsers where user = " . $iUser . " and groep = " . $oGroup->id() . ";"); 
				$oDB->execute(); 
				if ($oDB->record()){ 
					foreach ($this->arRights as $strType=>$bVal) $this->arRights[$strType] = ($oDB->get($strType) == YES); 
				}
			}
		} 
		
		public function admin() {
			return ($this->iUser == $this->oGroup->admin()->id());
		}
		
		public function useradd($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["useradd"] = $bVal; 
			return $this->admin() || $this->arRights["useradd"]; 
		}
		public function userdel($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["userdel"] = $bVal; 
			return $this->admin() || $this->arRights["userdel"]; 
		}
		public function userrights($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["userrights"] = $bVal; 
			return $this->admin() || $this->arRights["userrights"]; 
		}
		public function owaesadd($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["owaesadd"] = $bVal; 
			return $this->admin() || $this->arRights["owaesadd"]; 
		}
		public function owaesedit($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["owaesedit"] = $bVal; 
			return $this->admin() || $this->arRights["owaesedit"]; 
		}
		public function owaesdel($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["owaesdel"] = $bVal; 
			return $this->admin() || $this->arRights["owaesdel"]; 
		}
		public function owaesselect($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["owaesselect"] = $bVal; 
			return $this->admin() || $this->arRights["owaesselect"]; 
		}
		public function owaespay($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["owaespay"] = $bVal; 
			return $this->admin() || $this->arRights["owaespay"]; 
		}
		public function groupinfo($bVal = NULL) {
			if (!is_null($bVal)) $this->arRights["groupinfo"] = $bVal; 
			return $this->admin() || $this->arRights["groupinfo"]; 
		}
		public function right($strKey, $bVal = NULL) {
			if (!is_null($bVal)) $this->arRights[$strKey] = $bVal; 
			return $this->admin() || $this->arRights[$strKey]; 
		}
		
		public function update() {
			$arUpdates = ""; 
			foreach ($this->arRights as $strKey=>$bValue) if (!is_null($bValue)) $arUpdates[] = $strKey . "=" . ($bValue?1:0);
			$oDB = new database(); 
			$oDB->sql("update tblGroupUsers set " . implode(",", $arUpdates) . " where user = " . $this->iUser . " and groep = " . $this->oGroup->id() . ";"); 
			$oDB->execute();  
		}
	}   
	
?>