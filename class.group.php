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
		private $strWebsite = NULL;  
		private $strInfo = NULL;  
		private $iAdmin = NULL;  
		private $arUsers = NULL;  
		private $strImage = NULL;
		private $iLastUpdate = NULL; 
		private $arUserRights = array(); 
		private $bDeleted = FALSE; 
		private $bIsDienstverlener = NULL; 
		 
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
			if (is_null($this->iAdmin)) $this->load();
			return user($this->iAdmin); 
		}
		
		public function naam($strNaam = NULL) {
			if (!is_null($strNaam)) $this->strNaam = $strNaam; 
			if (is_null($this->strNaam)) $this->load();
			return $this->strNaam; 
		}
		
		public function website($strWebsite = NULL) {
			if (!is_null($strWebsite)) $this->strWebsite = $strWebsite; 
			if (is_null($this->strWebsite)) $this->load();
			$strURL = $this->strWebsite;  
			if (strrpos($strURL, "://") === false) { 
				if (preg_match("/[a-z0-9\-]\.[a-z0-9\-]/i", $strURL)) $strURL = "http://" . $strURL; 
				//if (strrpos($strURL, ".") !== false) $strURL = "http://" . $strURL; 
			}
			
			return $strURL; 
		}
		
		public function alias($strAlias = NULL) {
			if (!is_null($strAlias)) $this->strAlias = $strAlias; 
			if (is_null($this->strAlias)) $this->load();
			return $this->strAlias; 
		}
		
		public function info($strInfo = NULL) {
			if (!is_null($strInfo)) $this->strInfo = $strInfo; 
			return $this->strInfo; 
		}
		
		public function image($strImage = NULL) {
			if (!is_null($strImage)) $this->strImage = $strImage; 
			if (is_null($this->strImage)) $this->load();
			return $this->strImage; 
		}
		
		public function isDienstverlener($bIsDienstverlener = NULL) {
			if (!is_null($bIsDienstverlener)) $this->bIsDienstverlener = $bIsDienstverlener; 
			if (is_null($this->bIsDienstverlener)) $this->load();
			return $this->bIsDienstverlener; 
		}
		
		public function delete($bValue = NULL) {
			if (!is_null($bValue)) if (user(me())->admin()) $this->bDeleted = $bValue; 
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
			if (is_null($this->iLastUpdate)) $this->load();
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
				if (!$this->users($iUser)) {
					$this->arUsers = NULL; 
					$oDB = new database(); 
					
					if (user(me())->admin()) {
						$strSQL = "insert into tblGroupUsers (invitedby, groep, user, confirmed) values (" . me() . ", " . $this->id() . ", " . $iUser . ", 1); "; 
						$oDB->execute($strSQL); 
						
						$oNotification = new notification($iUser, "group." . $this->id()); 
						$oNotification->message("Je werd toegevoegd aan de groep '" . $this->naam() . "'"); 
						$oNotification->sender(me()); 
						$oNotification->link($this->getURL()); 
						$oNotification->send(); 
						 
						$oExperience = new experience($iUser);  
						$oExperience->detail("reason", "added to group");  
						$oExperience->sleutel("group." . $this->id());   
						$oExperience->add(50);  
						
					} else {
						$strSQL = "insert into tblGroupUsers (invitedby, groep, user, confirmed) values (" . me() . ", " . $this->id() . ", " . $iUser . ", 0); "; 
						$oDB->execute($strSQL); 
						
						$oNotification = new notification($iUser, "group." . $this->id()); 
						$oNotification->message(user(me())->getName() . " heeft je uitgenodigd lid te worden van de groep '" . $this->naam() . "'"); 
						$oNotification->sender(me()); 
						$oNotification->link($this->getURL()); 
						$oNotification->send(); 
					}
					return TRUE; 
				} else return FALSE; 
			} else return FALSE; 
		}
		
		public function removeUser($iUser) {
			if (($iUser==me()) || $this->userrights(me())->userdel()) {
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
					if (is_null($this->strWebsite)) $this->website($oDB->get("website")); 
					if (is_null($this->strAlias)) $this->alias($oDB->get("alias")); 
					if (is_null($this->strInfo)) $this->info($oDB->get("info")); 
					if (is_null($this->iAdmin)) $this->admin($oDB->get("admin")); 
					if (is_null($this->strImage)) $this->image($oDB->get("img")); 
					if (is_null($this->bIsDienstverlener)) $this->isDienstverlener($oDB->get("isdienstverlener")==1); 
					if (is_null($this->iLastUpdate)) $this->lastupdate($oDB->get("lastupdate")); 
				} else {
					if (is_null($this->strNaam)) $this->naam(""); 
					if (is_null($this->strNaam)) $this->website(""); 
					if (is_null($this->strAlias)) $this->alias(""); 
					if (is_null($this->strInfo)) $this->info(""); 
					if (is_null($this->iAdmin)) $this->admin("");
					if (is_null($this->strImage)) $this->image("");
					if (is_null($this->bIsDienstverlener)) $this->isDienstverlener(FALSE);
					if (is_null($this->iLastUpdate)) $this->lastupdate(owaesTime()); 
				}
			} else {
				if (is_null($this->strNaam)) $this->naam(""); 
				if (is_null($this->strNaam)) $this->website(""); 
				if (is_null($this->strAlias)) $this->alias(""); 
				if (is_null($this->strInfo)) $this->info(""); 
				if (is_null($this->iAdmin)) $this->admin("");
				if (is_null($this->strImage)) $this->image("");
				if (is_null($this->bIsDienstverlener)) $this->isDienstverlener(FALSE);
				if (is_null($this->iLastUpdate)) $this->lastupdate(owaesTime()); 
			}
		}
		
		public function update() {
			$arVelden = array(); 
			if (!is_null($this->strNaam)) $arVelden["naam"] = $this->strNaam; 
			if (!is_null($this->strWebsite)) $arVelden["website"] = $this->strWebsite; 
			if (!is_null($this->strAlias)) $arVelden["alias"] = $this->strAlias; 
			if (!is_null($this->strInfo)) $arVelden["info"] = $this->strInfo; 
			if (!is_null($this->iAdmin)) $arVelden["admin"] = $this->iAdmin; 
			if (!is_null($this->strImage)) $arVelden["img"] = $this->strImage; 
			if (!is_null($this->bIsDienstverlener)) $arVelden["isdienstverlener"] = $this->bIsDienstverlener ? 1 : 0; 
			$arVelden["deleted"] = $this->bDeleted ? 1 : 0;  
			$arVelden["lastupdate"] = $this->lastupdate(owaesTime()); 

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
			$strHTML = template($strTemplate);
			
	/* START LUSSEN [friends]xxx[/friends] */ 
			$arLoopStrings = array("members", "market");
			foreach ($arLoopStrings as $strLoop) {
				$arCheckRegXs = array(
					"/\[if:$strLoop\]([\s\S]*?)\[\/if:$strLoop\]/", // bv. [if:friends]<div><h1>Vrienden</h1><ul>....</ul></div>[/if:friends]
					"/\[if:$strLoop(>([0-9]+){0,1})\]([\s\S]*?)\[\/if:$strLoop\\1\]/", // bv. [if:friends>3]<div><h1>Vrienden</h1><ul>....</ul></div>[/if:friends>3]  
					"/\[$strLoop((?::([0-9]+)){0,1})\]([\s\S]*?)\[\/$strLoop\\1\]/",  // bv. [friends]loop[/friends] 
					"/\[$strLoop:count\]/" // bv. [friends:count]
				); 
				$bSet = FALSE; 
				foreach ($arCheckRegXs as $strCheckRX) { 
					if(preg_match($strCheckRX, $strHTML)) $bSet = TRUE;  
				} 
				if ($bSet)  { 
					switch($strLoop) {
						case "members": 
							$arList = array(); 
							foreach ($this->users() as $oUser) if ($oUser->visible()) $arList[] = $oUser; 
							break;  
						case "market":  
							$oList = new owaeslist();   
							$oList->filterByGroup($this->id()); 
							$arList = $oList->getList();   
							break; 
						default: 
							$arList = array(); 
					}  
					
					preg_match_all("/\[$strLoop:count\]/", $strHTML, $arResult);   // bv. [data:facebook] 
					for ($i=0;$i<count($arResult[0]);$i++) { // [friends:count]  
						$strHTML = str_replace($arResult[0][$i], count($arList), $strHTML);  
					} 
					
					preg_match_all("/\[if:$strLoop\]([\s\S]*?)\[\/if:$strLoop\]/", $strHTML, $arResult);  // regex opnieuw runnen want kan aangepast zijn in vorige loop
					for ($i=0;$i<count($arResult[0]);$i++) { // run trough [if:friends]<div><h1>Vrienden</h1><ul>....</ul></div>[/if:friends]  
						if (count($arList)>0) {
							$strHTML = str_replace($arResult[0][$i], $arResult[1][$i], $strHTML);
						} else {
							$strHTML = str_replace($arResult[0][$i], "", $strHTML);
						} 
					} 
					
					preg_match_all("/\[if:$strLoop(\>([0-9]+)){0,1}\]([\s\S]*?)\[\/if:$strLoop\\1\]/", $strHTML, $arResult); 
					for ($i=0;$i<count($arResult[0]);$i++) { // run trough [if:friends>3]<a href="loadmore">meer...</a>[/if:friends>3]  
						if (count($arList)>intval($arResult[2][$i])) {
							$strHTML = str_replace($arResult[0][$i], $arResult[3][$i], $strHTML);
						} else {
							$strHTML = str_replace($arResult[0][$i], "", $strHTML);
						} 
					}  
					 
					preg_match_all("/\[$strLoop((?::([0-9]+)){0,1})\]([\s\S]*?)\[\/$strLoop\\1\]/", $strHTML, $arResult);   // regex opnieuw runnen want kan aangepast zijn in vorige loop
					for ($i=0;$i<count($arResult[1]);$i++) { // run trough [friends]loop[/friends] 
						$strSubHTML = ""; 
						$iTeller = 0; 
						$iMax = intval($arResult[2][$i]); 
						foreach ($arList as $oItem) {  
							if ($iMax == 0 || ++$iTeller <= $iMax) {
								switch($strLoop) {
									case "members": 
										$strSubHTML .= $oItem->html($arResult[3][$i]);
										break;  
									case "market": 
										$strSubHTML .= $oItem->html($arResult[3][$i]);
										break;  
									default: 
										$strSubHTML .= $arResult[3][$i]; 
										break; 
								}
							} 
						}
						$strHTML = str_replace($arResult[0][$i], $strSubHTML, $strHTML); 
					}  

				}
			}
			/* EIND LUSSEN [friends]xxx[/friends] */  
			
			/* LEDEN - START 
			preg_match_all("/\[if:members\]([\s\S]*?)\[\/if:members\]/", $strHTML, $arResult);   // bv. [if:friends]<div><h1>Vrienden</h1><ul>....</ul></div>[/if:friends]   
			for ($i=0;$i<count($arResult[0]);$i++) {
				if (count($this->users())>0) {
					$strHTML = str_replace($arResult[0][$i], $arResult[1][$i], $strHTML);
				} else {
					$strHTML = str_replace($arResult[0][$i], "", $strHTML);
				} 
			} 
			preg_match_all("/\[members((?::([0-9]+)){0,1})\]([\s\S]*?)\[\/members\\1\]/", $strHTML, $arResult);   // bv. [friends]loop[/friends] 
			for ($i=0;$i<count($arResult[1]);$i++) { 
				$strMembers = ""; 
				$iTeller = 0; 
				$iMax = intval($arResult[2][$i]); 
				foreach ($this->users() as $oMember) {  
					if ($iMax == 0 || ++$iTeller <= $iMax) $strMembers .= $oMember->html($arResult[3][$i], FALSE);
				}
				$strHTML = str_replace($arResult[0][$i], $strMembers, $strHTML); 
			}  
			/* LEDEN - END */
			
			
			/* MARKET - START  
			preg_match_all("/\[if:market\]([\s\S]*?)\[\/if:market\]/", $strHTML, $arResult);   // bv. [if:friends]<div><h1>Vrienden</h1><ul>....</ul></div>[/if:friends]   
			for ($i=0;$i<count($arResult[0]);$i++) { 
				$oOwaesList = new owaeslist();   
				$oOwaesList->filterByGroup($this->id()); 
				if (count($oOwaesList->getList())>0) {
					$strHTML = str_replace($arResult[0][$i], $arResult[1][$i], $strHTML);
				} else {
					$strHTML = str_replace($arResult[0][$i], "", $strHTML);
				} 
			} 
			preg_match_all("/\[market((?::([0-9]+)){0,1})\]([\s\S]*?)\[\/market\\1\]/", $strHTML, $arResult);   // bv. [friends]loop[/friends] 
			for ($i=0;$i<count($arResult[1]);$i++) { 
				$strMarket = ""; 
				$iTeller = 0; 
				$iMax = intval($arResult[2][$i]); 
				$oOwaesList = new owaeslist();   
				$oOwaesList->filterByGroup($this->id()); 
				foreach ($oOwaesList->getList() as $oItem) {  
					if ($iMax == 0 || ++$iTeller <= $iMax) $strMarket .= $oItem->html($arResult[3][$i], FALSE);
				}
				$strHTML = str_replace($arResult[0][$i], $strMarket, $strHTML); 
			}  
			/* MARKET - END */
			 
			preg_match_all("/\[([a-zA-Z0-9-_:#]+)\]([\s\S]*?)\[\/\\1\]/", $strHTML, $arResult); // [tag]...[/tag]
			for ($i=0;$i<count($arResult[1]);$i++) { 
				$strResult = $this->HTMLvalue($arResult[1][$i], $arResult[2][$i]); 
				if (!is_null($strResult)) $strHTML = str_replace($arResult[0][$i], $strResult, $strHTML);
			}  
			 
			$strHTML = preg_replace_callback('/\[profileimg\:([0-9]*x[0-9]*)\]/', array(&$this, "imageregreplace"), $strHTML); 
			
 			preg_match_all("/\[if:([a-zA-Z0-9-_:#]+)\]([\s\S]*?)\[\/if:\\1\]/", $strHTML, $arResult);   // bv. [if:naam]naam ingevuld en zichtbaar[/if:naam]  
			for ($i=0;$i<count($arResult[0]);$i++) {
				$strResult = $this->HTMLvalue($arResult[1][$i]);  
				if (!is_null($strResult)) $strHTML = str_replace($arResult[0][$i], (($strResult == "") ? "" : $arResult[2][$i]), $strHTML); 	
			} 
			preg_match_all("/\[([a-zA-Z0-9-_:#]+)\]/", $strHTML, $arResult);   // alle tags (zonder whitespace)
			if (isset($arResult[1])) foreach ($arResult[1] as $strTag){ 
				$strResult = $this->HTMLvalue($strTag);  
				if (!is_null($strResult)) $strHTML = str_replace("[$strTag]", $strResult, $strHTML); 
			} 
			
			return specialHTMLtags($strHTML);  
		}
		
		public function HTMLvalue($strTag, $strTemplate = NULL) {
			switch($strTag) { 
				case "id": 
					return $this->id(); 
				case "naam":  
				case "name": 
					return html($this->naam()); 
				case "website": 
					return $this->website(); 
				case "link": 
					return $this->getURL(); 
				case "description": 
					return html($this->info(), array("p", "a", "em", "strong", "br"));  // html($this->info()) . "<hr>" . 
				case "description:short": 
					return shorten(html($this->info())); 
				case "members:count": 
					return count($this->users());
				case "market:count": 
					$oOwaesList = new owaeslist();   
					$oOwaesList->filterByGroup($this->id()); 
					return count($oOwaesList->getList()); 
				case "actions":  
					$arActions = array(); 
					$oRights = $this->userrights();  
					if ($oRights->groupinfo()) $arActions[] = "<a href=\"admin.groepusers.php?group=" . $this->id() . "\">aanpassen</a>"; 
					return implode("", $arActions); 
				case "editlink": 
					return fixPath("admin.groepusers.php?group=" . $this->id()); 
				case "if:rights:editpage": 
					return $this->userrights()->editpage() ? $strTemplate : ""; 
				case "admin":  
					return $this->admin()->html($strTemplate);  
			}
		}
		
		private function imageregreplace(&$matches) { 
			return $this->getImage($matches[1], FALSE);  
		} 

	}
	
	class usergrouprights {
		private $arBooleans = array(
								"useradd" => NULL, 
								"userdel" => NULL, 
								"userrights" => NULL, 
								"owaesadd" => NULL, 
								"owaesedit" => NULL, 
								"owaesdel" => NULL, 
								"owaesselect" => NULL, 
								"owaespay" => NULL, 
								"groupinfo" => NULL, 
								"confirmed" => NULL,  
							);  
		private $arValues = array(
								"invitedby" => NULL, 
							); 
		private $oGroup = NULL;
		private $iUser = NULL;  
		
		public function usergrouprights($oGroup, $iUser) {
			$this->oGroup = $oGroup; 
			$this->iUser = $iUser;  
			if ($iUser==$oGroup->admin()->id()) { 
				foreach ($this->arBooleans as $strType=>$bVal) $this->arBooleans[$strType] = TRUE;  
			} else {
				foreach ($this->arBooleans as $strType=>$bVal) $this->arBooleans[$strType] = NULL;  
				$oDB = new database(); 
				$oDB->sql("select * from tblGroupUsers where user = " . $iUser . " and groep = " . $oGroup->id() . ";"); 
				$oDB->execute(); 
				if ($oDB->record()){ 
					foreach ($this->arBooleans as $strType=>$bVal) $this->arBooleans[$strType] = ($oDB->get($strType) == YES); 
					foreach ($this->arValues as $strKey=>$bVal) $this->arValues[$strKey] = $oDB->get($strKey); 
				}
			}
		} 
		
		public function admin() {
			return user($this->iUser)->admin() || ($this->iUser == $this->oGroup->admin()->id());
		}
		
		public function useradd($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["useradd"] = $bVal; 
			return $this->admin() || $this->arBooleans["useradd"]; 
		}
		public function userdel($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["userdel"] = $bVal; 
			return $this->admin() || $this->arBooleans["userdel"]; 
		}
		public function userrights($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["userrights"] = $bVal; 
			return $this->admin() || $this->arBooleans["userrights"]; 
		}
		public function owaesadd($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["owaesadd"] = $bVal; 
			return $this->admin() || $this->arBooleans["owaesadd"]; 
		}
		public function owaesedit($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["owaesedit"] = $bVal; 
			return $this->admin() || $this->arBooleans["owaesedit"]; 
		}
		public function owaesdel($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["owaesdel"] = $bVal; 
			return $this->admin() || $this->arBooleans["owaesdel"]; 
		}
		public function owaesselect($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["owaesselect"] = $bVal; 
			return $this->admin() || $this->arBooleans["owaesselect"]; 
		}
		public function owaespay($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["owaespay"] = $bVal; 
			return $this->admin() || $this->arBooleans["owaespay"]; 
		}
		public function groupinfo($bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans["groupinfo"] = $bVal; 
			return $this->admin() || $this->arBooleans["groupinfo"]; 
		}
		public function right($strKey, $bVal = NULL) {
			if (!is_null($bVal)) $this->arBooleans[$strKey] = $bVal; 
			return $this->admin() || $this->arBooleans[$strKey]; 
		}
		public function value($strKey, $bVal = NULL) {
			if (isset($this->arBooleans[$strKey])) {
				if (!is_null($bVal)) $this->arBooleans[$strKey] = $bVal; 
				return $this->arBooleans[$strKey]; 
			}
			if (isset($this->arValues[$strKey])) {
				if (!is_null($bVal)) $this->arValues[$strKey] = $bVal; 
				return $this->arValues[$strKey]; 
			}
		}
		public function editpage(){ // heeft de gebruiker rechten op iets? 
			$bRechten = FALSE; 
			$arCheck = array(
				"useradd",
				"userdel",
				"userrights",
				"groupinfo",
			); 
			foreach ($arCheck as $strKey) if ($this->right($strKey)) $bRechten = TRUE; 
			return $bRechten; 
		}
		
		public function update() {
			$arUpdates = array(); 
			$oDB = new database(); 
			foreach ($this->arBooleans as $strKey=>$bValue) if (!is_null($bValue)) $arUpdates[] = $strKey . "=" . ($bValue?1:0);
			foreach ($this->arValues as $strKey=>$strValue) if (!is_null($strValue)) $arUpdates[] = $strKey . "='" . $oDB->escape($strValue) . "'";
			$oDB->sql("update tblGroupUsers set " . implode(",", $arUpdates) . " where user = " . $this->iUser . " and groep = " . $this->oGroup->id() . ";"); 
			$oDB->execute();  
		}
	}   
	
