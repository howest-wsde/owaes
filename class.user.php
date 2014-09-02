<?php
	define ("GETSET", 1); 
	define ("ADD", 2); 
	
	
	define ("VISIBILITY_HIDDEN", 0); 
	define ("VISIBILITY_VISIBLE", 1); 
	define ("VISIBILITY_FRIENDS", 2); 
		
	define ("FRIEND_NOFRIENDS", 0); 
	define ("FRIEND_ASKED", 1); 
	define ("FRIEND_REQUESTED", 2); 
	define ("FRIEND_FRIENDS", 10); 
 
	$ar_GLOBAL_users = array(); 
	function user($strKey = NULL) { // FUNCTION user(5) == CLASS new user(5) 
		global $ar_GLOBAL_users; 
		foreach($ar_GLOBAL_users as $oUser) {
			if (!is_null($strKey)) {
				if ($oUser["id"] == $strKey) return $oUser["object"]; 
				if ($oUser["alias"] == $strKey) return $oUser["object"]; 	
			}
		}
		$oUser = new user($strKey);  
		$ar_GLOBAL_users[] = array(
			"id" => $oUser->id(), 
			"alias" => $oUser->alias(), 
			"object" => $oUser, 
		); 
		return $oUser; 
	}
	
	class user {  
		private $iID = NULL; 
		private $strAlias = NULL;
		private $strLogin = NULL;
		private $strFirstname = NULL;
		private $strLastname = NULL;
		private $strEmail = NULL;
		private $strDescription = NULL;
		private $strIMG = NULL;
		private $strPassword = NULL;  
		private $strLocation = NULL; 
		private $strTelephone = NULL; 
		private $strGender = NULL; 
		private $ibirthdate = NULL;  
		private $iLocationLat = NULL; 
		private $iLocationLong = NULL; 
		private $arBadges = NULL;  
		private $arCertificates = NULL; 
		private $arPayments = NULL;  
		private $iStars = NULL;  
		private $iCredits = NULL;  
		private $bIsCurrentUser; 
		private $iPhysical = NULL;
		private $iMental = NULL;
		private $iEmotional = NULL;
		private $iSocial = NULL;
		private $iLastUpdate = NULL;
		private $arGroups = NULL; 
		private $iPosts = NULL; 
		private $iSubscriptions = NULL; 
		private $arBestanden = NULL; 
		private $strStatus = NULL; 
		private $iLevel = NULL; 
		private $bVisible = NULL; 
		private $oExperience = NULL;
		private $arVisible = array();  
		private $bUnlocked = FALSE; // als user unlocked wordt (->unlock() ) kan e-mailadres en dergelijke ook opgevraagd worden zonder vriend te moeten zijn
		private $arData = array(); 
		private $iFriendStatus = NULL; 
		
		private $bNEW = TRUE; 
		 
		public function user($strKey=NULL) { // $strKey = ID or ALIAS // when not defined: create new user  
			if (!is_null($strKey)) {
				if (is_numeric($strKey)) { 
					$this->id($strKey);
				} else {
					$this->alias($strKey); 
				}
				$this->bNEW = FALSE; 
			} else {
				$this->bNEW = TRUE; 
				$this->id(0); 
			}
			if (isset($_POST["edit-profile"])) { 
				if ($_POST["edit-profile"] == $this->editkey()) {
					$this->savePostData(); 
					$_POST["edit-profile"] = ""; // prevent "save again" with further loads
				}
			} 
		}
		
		private function savePostData() { 
			foreach ($_POST as $strKey=>$strVal) { 
				switch($strKey) {
					case "edit-profile": break; 
					case "firstname": 
						$this->firstname($strVal); 
						break; 	
					case "lastname": 
						$this->lastname($strVal); 
						break; 	
					default: 
						$this->data($strKey, $strVal); 
				}
				$this->update(); 
			}	
		}
		
		public function unlock($bValue = TRUE) { // als een user unlocked is kan ook een niet-aangemelde gebruiker bv. emailadres opvragen of aanpassingen doen (nodig voor "paswoord vergeten")
			$this->bUnlocked = $bValue; 
		}
		
		public function id($iID = NULL) { // get / set ID (enkel set via DB)
			if (!is_null($iID)) $this->iID = $iID; 
			if (is_null($this->iID)) $this->load(); 
			$this->bNEW = ($this->iID == 0);
			return $this->iID; 	
		} 
		
		public function alias($strAlias = NULL, $bCreate = FALSE) { // if bCreate == TRUE -> alias will be set to unique alias
			if (!is_null($strAlias)) {
				if ($bCreate) {
					$strProposition = $strAlias; 
					if ($strProposition == "") $strProposition = trim($this->firstname()) . "_" . trim($this->lastname()); 
					$strProposition = str2url($strProposition); 
					$strProposition = str_replace(".", "-", $strProposition);
					$iTeller = 0; 
					do {
						$strAlias = $strProposition . (($iTeller ++ > 0) ? $iTeller : "" ); 
						$oCount = new database();
						$oCount->execute("select count(id) as aantal from tblUsers where alias = '" . $oCount->escape($strAlias) . "' and id != " . $this->id() . ";");  
					} while ($oCount->get("aantal") > 0);
				}
				$this->strAlias = $strAlias; 
			}
			if (is_null($this->strAlias)) $this->load();  
			return $this->strAlias;  
		}  
		
		public function password($strPassword = NULL, $bEncode = TRUE) { /*
		 get / set password:
		 get: returns md5 password
		 set: if ($bEncode==TRUE) => send MD5-string / $bEncode==FALSe => send user input
		 */
			if (!is_null($strPassword)) $this->strPassword = $bEncode ? md5(trim($strPassword)) : $strPassword; 
			if (is_null($this->strPassword)) $this->load(); 	
			return $this->strPassword; 
		} 
		 

		public function location($strLocation = NULL, $iLocationLat = NULL, $iLocationLong = NULL) { /* get / set location
			set : strlocation en lat + long doorgeven
			get: returns strLocation (voor lat + long: LatLong())
		*/
			if (!is_null($strLocation)) $this->strLocation = $strLocation; 
			if (!is_null($iLocationLat)) $this->iLocationLat = $iLocationLat;
			if (!is_null($iLocationLong)) $this->iLocationLong = $iLocationLong; 
			if (is_null($this->strLocation)) $this->load();
			return $this->visible4me("location") ? $this->strLocation : ""; 
		} 
		public function LatLong() { // returns arra(iLat, iLong)
			if (is_null($this->iLocationLat)||is_null($this->iLocationLong)) $this->load();
			return array($this->iLocationLat, $this->iLocationLong); 	
		}
		
		public function posts() { // returns count posts from this user
			if (is_null($this->iPosts)){
				$oDB = new database(); 
				$strSQL = "select count(id) as aantal from tblMarket where author = '" . $this->id() . "'; "; 
				$oDB->execute($strSQL); 
				$this->iPosts = $oDB->get("aantal"); 	
			}
			return $this->iPosts; 
		}
		
		public function subscriptions() { // returns count subscriptions from this user
			if (is_null($this->iSubscriptions)){
				$oDB = new database(); 
				$strSQL = "select count(distinct market) as aantal from tblMarketSubscriptions where doneby = '" . $this->id() . "'; "; 
				$oDB->execute($strSQL); 
				$this->iSubscriptions = $oDB->get("aantal"); 
			}
			return $this->iSubscriptions; 
		}
		
		public function score() { // 
			return floor($this->posts()/5); 
		}
		
		public function experience() {
			if (is_null($this->oExperience)) $this->oExperience = new experience($this->id());  
			return $this->oExperience; 	
		}
		
		public function isFriend() { // GET boolean
			if (is_null($this->iFriendStatus)) $this->load(); 
			return ($this->id() == me() || $this->iFriendStatus == FRIEND_FRIENDS);  
		}
		
		public function addFriend($bSetFriend = TRUE) { // GET boolean
			$iUser = me(); 
			$iFriend = $this->id(); 
			if (is_null($this->iFriendStatus)) $this->load(); 
			$oDB = new database(); 
			switch($this->iFriendStatus) {
				case FRIEND_FRIENDS:  // had been confirmed by both sides
					$oDB->execute("update tblFriends set confirmed = 0 where user = $iFriend and friend = $iUser; ");
					$oDB->execute("delete from tblFriends where user = $iUser and friend = $iFriend; ");
					$this->iFriendStatus = FRIEND_ASKED; 
					break; 
					
				case FRIEND_REQUESTED: // request had already been made by me
					$oDB->execute("delete from tblFriends where user = $iUser and friend = $iFriend; ");
					$this->iFriendStatus = FRIEND_NOFRIENDS; 
					break; 

				case FRIEND_ASKED: // other party had already asked friendship
					$oDB->execute("insert into tblFriends (user, friend, datum, confirmed) values ($iUser, $iFriend, " . owaestime() . ", 1);");
					$oDB->execute("update tblFriends set confirmed = 1 where user = $iFriend and friend = $iUser; ");
					$oNotification = new notification($iFriend); 
					$oNotification->key("friendship.$iUser.$iFriend"); 
					$oNotification->message(user($iUser)->getName() . " heeft je vriendschapaanvraag bevestigd"); 
					$oNotification->link(user($iUser)->getURL());
					$oNotification->sender($iUser); 
					$oNotification->send(); 
					$this->iFriendStatus = FRIEND_FRIENDS; 
					break; 

				case FRIEND_NOFRIENDS:  // nothing asked yet
				default: 
					$oDB->execute("insert into tblFriends (user, friend, datum) values ($iUser, $iFriend, " . owaestime() . ") ");
					$oNotification = new notification($iFriend); 
					$oNotification->key("friendship.$iUser.$iFriend"); 
					$oNotification->message(user($iUser)->getName() . " heeft een vriendschapaanvraag verstuurd"); 
					$oNotification->link(user($iUser)->getURL());
					$oNotification->sender(0); 
					$oNotification->send(); 
					$this->iFriendStatus = FRIEND_REQUESTED; 
					break; 	
			} 
		}
		
		public function visible4me($oSelector) { /* get visible-value (TRUE/FALSE) van specifiek veld voor actieve gebruiker 
				bv. $oUser->visible4me("firstname") => checkt of actieve gebruiker de voornaam van $oUser kan zien (afhankelijk van settings  en al dan niet friend zijn )
			*/  
			if ($this->bUnlocked) return TRUE; 
			switch($this->visible($oSelector)) {
				case VISIBILITY_HIDDEN:   
					return ($this->id() == me()); 
					break; 
					
				case VISIBILITY_VISIBLE:  
					return TRUE; 
					break; 
					
				case VISIBILITY_FRIENDS:   
					return $this->isFriend(); 
					break; 
					
				default: 
					error("class.user.php line " . __LINE__ . ": '" . $this->visible($oSelector) . "' ongeldige waarde"); 
			} 
		}

		public function visible($oSelector = NULL, $iValue = NULL) { /*
			zichtbaarheid van het profiel get/setten
				->visible(); = get / profiel zichtbaar in het overzicht van gebruikers
				->visible(VISIBILITY_HIDDEN); = set / profiel zichtbaar in het overzicht van gebruikers
				->visible("firstname"); = get / voornaam zichtbaar
				->visible("firstname", VISIBILITY_VISIBLE); = set / voornaam zichtbaar

				VISIBILITY_HIDDEN / VISIBILITY_VISIBLE / VISIBILITY_FRIENDS
		*/
		
			if (is_numeric($oSelector)) $oSelector = intval($oSelector);
			if (is_numeric($iValue)) $iValue = intval($iValue); 
			if (!is_null($oSelector)) { 
				if (is_null($iValue)) { 
					if ($oSelector === 1 || $oSelector === TRUE || $oSelector === VISIBILITY_VISIBLE || $oSelector === "yes") {
							$this->bVisible = TRUE; 
							$this->arVisible["profile"] = VISIBILITY_VISIBLE;  
							return TRUE; 
							
					} else if ($oSelector === 0 || $oSelector === FALSE || $oSelector === VISIBILITY_HIDDEN || $oSelector === "no") { 
							$this->bVisible = FALSE; 
							$this->arVisible["profile"] = VISIBILITY_HIDDEN; 
							return FALSE; 
					}  else if ($oSelector === VISIBILITY_FRIENDS) { 
							$this->arVisible["profile"] = VISIBILITY_FRIENDS; 
							return FALSE; // TODO: should return value in functie van vriend zijn of nie
					
					} else { 
						$oSelector = strtolower($oSelector); 
						switch($oSelector) {
							case "firstname":  
							case "lastname":  
							case "location":  
							case "email":   
							case "profile":
							case "telephone":
							case "gender":
							case "birthdate":   
							case "description":   
							case "img":  
								if (!isset($this->arVisible[$oSelector])) $this->load(); 
								return $this->arVisible[$oSelector]; 
							default: 
								error("class.user.php line " . __LINE__ . ": '" . $oSelector . "' ongeldige waarde"); 
						}
					} 
				} else {
					$this->arVisible[$oSelector] = intval($iValue); 
					return $this->arVisible[$oSelector]; 
				} 
			} else {
				return $this->bVisible; 
			} 
		}
		
		public function showable($oSelector = NULL) { // checkt of gevraagd item "visible" EN ingevuld is
			if ($this->visible($oSelector)) {
				switch(strtolower($oSelector)) {
					case "firstname":  
						return ($this->firstname() != "");
						break; 
					case "lastname":
						return ($this->lastname() != "");
						break;   
					case "location":
						return ($this->location() != "");
						break;   
					case "email":   
						return ($this->email() != "");
						break;  
					case "telephone":
						return ($this->telephone() != "");
						break; 
					case "gender":
						return ($this->gender() != "");
						break; 
					case "birthdate":  
						return ($this->birthdate() != 0);
						break;  
					case "description": 
						return ($this->description() != "");
						break;  
					default: 
						error ("veld niet gedefineerd: class.user.php" . __LINE__ ); 
				}
			} else return FALSE; 
		}


		public function physical($iValue = NULL) {
			if (!is_null($iValue)) $this->iPhysical = $iValue; 
			if (is_null($this->iPhysical)) $this->loadIndicators(); 
			if ($this->iPhysical > 100) return 100; 
			if ($this->iPhysical < 0) return 0; 		
			return $this->iPhysical; 
		}
		
		public function mental($iValue = NULL) {
			if (!is_null($iValue)) $this->iMental = $iValue; 
			if (is_null($this->iMental)) $this->loadIndicators(); 
			if ($this->iMental > 100) return 100; 
			if ($this->iMental < 0) return 0; 	
			return $this->iMental; 
		}
		
		public function emotional($iValue = NULL) {
			if (!is_null($iValue)) $this->iEmotional = $iValue; 
			if (is_null($this->iEmotional)) $this->loadIndicators(); 	
			if ($this->iEmotional > 100) return 100; 
			if ($this->iEmotional < 0) return 0; 
			return $this->iEmotional; 
		}
		
		public function social($iValue = NULL) {
			if (!is_null($iValue)) $this->iSocial = $iValue; 
			if (is_null($this->iSocial)) $this->loadIndicators(); 
			if ($this->iSocial > 100) return 100; 
			if ($this->iSocial < 0) return 0; 
			return $this->iSocial; 
		}
		
		public function data($strKey, $strValue = NULL) {
			if (!is_null($strValue)) {
				if (isset($this->arData[$strKey])) {
					$this->arData[$strKey]["value"] = $strValue; 
				} else {
					$this->arData[$strKey] = array("value" => $strValue); 
				}
			}
			//vardump($this->arData[$strKey]); 
			return (isset($this->arData[$strKey]["value"])) ? $this->arData[$strKey]["value"] : "";  
		}
		
		public function login($strLogin = NULL) { /*
		 get / set login 
		 TODO: checken of login nog niet bestaat! 
		 */
			if (!is_null($strLogin)) {
				$oReturn = $strLogin;
				if ($strLogin == "") $strLogin = randomstring(8); 
				if (!strrpos($strLogin, "@") === false) {
					$strLogin = str_replace("@", "", $strLogin); 
					$oReturn = FALSE; 
				}
				$oDB = new database(); 
				do { 
					$oDB->execute("select count(id) as aantal from tblUsers where login='" . $oDB->escape($strLogin) . "' and id != "  . $this->id() . ";"); 
					if ($oDB->get("aantal") > 0) {
						$strLogin = randomstring(8);
						$oReturn = FALSE; 
					}
				} while ($oDB->get("aantal") > 0); 
				$this->strLogin = $strLogin; 
				return $oReturn; 
			}
			if (is_null($this->strLogin)) $this->load(); 
			return $this->strLogin; 
		}
		
		public function firstname($strFirstname = NULL) { // get / set first name
			if (!is_null($strFirstname)) $this->strFirstname = $strFirstname; 
			if (is_null($this->strFirstname)) $this->load(); 
			return ($this->visible4me("firstname")) ? $this->strFirstname : ""; 
		}
		
		public function lastname($strLastname = NULL) { // get / set last name 
			if (!is_null($strLastname)) $this->strLastname = $strLastname; 
			if (is_null($this->strLastname)) $this->load(); 
			return ($this->visible4me("lastname")) ? $this->strLastname : "";    
		}
		
		public function gender($strGender = NULL) { // get / set gender (string) 
			if (!is_null($strGender)) $this->strGender = $strGender; 
			if (is_null($this->strGender)) $this->load(); 
			return ($this->visible4me("gender")) ? $this->strGender : "";    
		}
		public function telephone($strTelephone = NULL) { // get / set telephone (string) 
			if (!is_null($strTelephone)) $this->strTelephone = $strTelephone; 
			if (is_null($this->strTelephone)) $this->load(); 
			return ($this->visible4me("telephone")) ? $this->strTelephone : "";    
		}
		public function birthdate($ibirthdate = NULL) { // get / set birthdate (integer) 
			if (!is_null($ibirthdate)) $this->ibirthdate = $ibirthdate; 
			if (is_null($this->ibirthdate)) $this->load(); 
			return ($this->visible4me("birthdate")) ? $this->ibirthdate : 0;    
		}
		
		public function description($strDescription = NULL) { // get / set omschrijving 
			if (!is_null($strDescription)) $this->strDescription = $strDescription; 
			if (is_null($this->strDescription)) $this->load(); 
			return ($this->visible4me("description")) ? $this->strDescription : "";  
		}
		
		public function email($strEmail = NULL) { // get / set e-mailadres
			if (!is_null($strEmail)) { 
				$oDB = new database();  
				$oDB->execute("select count(id) as aantal from tblUsers where mail='" . $oDB->escape($strEmail) . "' and id != "  . $this->id() . ";"); 
				if ($oDB->get("aantal") > 0) $strEmail = ""; 
				$this->strEmail = $strEmail; 
				if ($strEmail == "") return FALSE; 
			}
			if (is_null($this->strEmail)) $this->load(); 
			return ($this->visible4me("email")) ? $this->strEmail : ""; 
		}
		
		
		public function img($strIMG = NULL) { // get / set fotolocatie (relatief pad)
			if (!is_null($strIMG)) $this->strIMG = $strIMG; 
			if (is_null($this->strIMG)) $this->load(); 
			return $this->strIMG; 
		}
		
		
		private function load() {
			global $arConfig;  
			$oDB = new database();
			if (!is_null($this->iID)) {
				$strSQL = "select * from tblUsers where id = " . $this->iID . "; "; 
			} else if (!is_null($this->strAlias)) {
				$strSQL = "select * from tblUsers where alias = '" . $oDB->escape($this->strAlias) . "'; "; 
			} else {
				error("Geen ID of alias gedefinieerd (class.user.php)");
				return FALSE; 
			}  
			$oDB->execute($strSQL); 
			if ($oDB->length() == 1) {
				$oDBrecord = $oDB->record(); 
				if (is_null($this->iID)) $this->id($oDBrecord["id"]);
				if (is_null($this->strAlias)) $this->alias($oDBrecord["alias"]);  
				if (is_null($this->strLogin)) $this->login($oDBrecord["login"]);
				if (is_null($this->strFirstname)) $this->firstname($oDBrecord["firstname"]);
				if (is_null($this->strLastname)) $this->lastname($oDBrecord["lastname"]);
				if (is_null($this->strDescription)) $this->description($oDBrecord["description"]);
				if (is_null($this->strEmail)) $this->email($oDBrecord["mail"]);
				if (is_null($this->ibirthdate)) $this->birthdate($oDBrecord["birthdate"]);
				if (is_null($this->strGender)) $this->gender($oDBrecord["gender"]);
				if (is_null($this->strTelephone)) $this->telephone($oDBrecord["telephone"]);
				if (is_null($this->strIMG)) $this->img($oDBrecord["img"]);
				if (is_null($this->bVisible)) $this->visible($oDBrecord["visible"]);
				if ($oDBrecord["data"] != "") foreach (json_decode($oDBrecord["data"], TRUE) as $strK=>$strV) $this->data($strK, $strV["value"]); 
				if (!isset($this->arVisible["firstname"])) $this->visible("firstname", $oDBrecord["showfirstname"]);
				if (!isset($this->arVisible["lastname"])) $this->visible("lastname", $oDBrecord["showlastname"]);
				if (!isset($this->arVisible["email"])) $this->visible("email", $oDBrecord["showemail"]);
				if (!isset($this->arVisible["birthdate"])) $this->visible("birthdate", $oDBrecord["showbirthdate"]);
				if (!isset($this->arVisible["gender"])) $this->visible("gender", $oDBrecord["showgender"]);
				if (!isset($this->arVisible["telephone"])) $this->visible("telephone", $oDBrecord["showtelephone"]);
				if (!isset($this->arVisible["description"])) $this->visible("description", $oDBrecord["showdescription"]);
				if (!isset($this->arVisible["img"])) $this->visible("img", $oDBrecord["showimg"]);
				if (!isset($this->arVisible["location"])) $this->visible("location", $oDBrecord["showlocation"]);
				if (is_null($this->strLocation)) $this->location($oDBrecord["location"], $oDBrecord["location_lat"], $oDBrecord["location_long"]);
				if (is_null($this->strPassword)) $this->password($oDBrecord["pass"], FALSE); 
				// if (is_null($this->iSocial)) $this->social($oDBrecord["social"]);
				// if (is_null($this->iEmotional)) $this->emotional($oDBrecord["emotional"]);
				// if (is_null($this->iPhysical)) $this->physical($oDBrecord["physical"]);
				// if (is_null($this->iMental)) $this->mental($oDBrecord["mental"]);
				if (is_null($this->iLastUpdate)) $this->lastupdate($oDBrecord["lastupdate"]); 
				// nog geen return-> verderlopen in functie voor social en emo enzo  
			} else {
				if (is_null($this->iID)) $this->id(0);
				if (is_null($this->strAlias)) $this->alias("");
				if (is_null($this->strLogin)) $this->login("");
				if (is_null($this->strFirstname)) $this->firstname("");
				if (is_null($this->strLastname)) $this->lastname("");
				if (is_null($this->strDescription)) $this->description("");
				if (is_null($this->strEmail)) $this->email("");
				if (is_null($this->strIMG)) $this->img("");
				if (is_null($this->bVisible)) $this->visible(TRUE);
				if (!isset($this->arVisible["firstname"])) $this->visible("firstname", VISIBILITY_VISIBLE);
				if (!isset($this->arVisible["lastname"])) $this->visible("lastname", VISIBILITY_VISIBLE);
				if (!isset($this->arVisible["email"])) $this->visible("email", VISIBILITY_HIDDEN);
				if (!isset($this->arVisible["description"])) $this->visible("description", VISIBILITY_VISIBLE);
				if (!isset($this->arVisible["img"])) $this->visible("img", VISIBILITY_VISIBLE);
				if (!isset($this->arVisible["location"])) $this->visible("location", VISIBILITY_VISIBLE);
				if (!isset($this->arVisible["gender"])) $this->visible("gender", VISIBILITY_VISIBLE);
				if (!isset($this->arVisible["telephone"])) $this->visible("telephone", VISIBILITY_HIDDEN);
				if (!isset($this->arVisible["birthdate"])) $this->visible("birthdate", VISIBILITY_VISIBLE);
				if (is_null($this->strLocation)) $this->location("", 0, 0);
				if (is_null($this->strPassword)) $this->password(owaesTime()); 
				if (is_null($this->iLastUpdate)) $this->lastupdate(owaesTime());

				if (is_null($this->iSocial)) $this->social($arConfig["startvalues"]["social"]);
				if (is_null($this->iEmotional)) $this->emotional($arConfig["startvalues"]["emotional"]);
				if (is_null($this->iPhysical)) $this->physical($arConfig["startvalues"]["physical"]);
				if (is_null($this->iMental)) $this->mental($arConfig["startvalues"]["mental"]);
 
			}
			 
			$iUser = me(); 
			$iFriend = $this->id(); 
			$oDB->execute("select * from tblFriends where (user = $iUser and friend = $iFriend) or (user = $iFriend and friend = $iUser); ");
			switch($oDB->length()) {
				case 0: 
					$this->iFriendStatus = FRIEND_NOFRIENDS;
					break; 	
				case 1:  
					$this->iFriendStatus = ($oDB->get("user") == $iUser) ? FRIEND_REQUESTED : FRIEND_ASKED; 
					break; 	
				case 2: 
					$this->iFriendStatus = FRIEND_FRIENDS;  
					break; 	
			} 
		}
		
		private function loadIndicators() {
			global $arConfig; 
			$oDB = new database();
			$oDB->execute("select sum(emotional) as emotional, sum(social) as social, sum(physical) as physical, sum(mental) as mental from tblIndicators where user = " . $this->iID . " and actief = 1; "); 
			if ($oDB->record()) {
				//echo $oDB->table(TRUE); 
				if (is_null($this->iSocial)) $this->social($arConfig["startvalues"]["social"] + $oDB->get("social"));
				if (is_null($this->iEmotional)) $this->emotional($arConfig["startvalues"]["emotional"] + $oDB->get("emotional"));
				if (is_null($this->iPhysical)) $this->physical($arConfig["startvalues"]["physical"] + $oDB->get("physical"));
				if (is_null($this->iMental)) $this->mental($arConfig["startvalues"]["mental"] + $oDB->get("mental"));
			} else {
				if (is_null($this->iSocial)) $this->social($arConfig["startvalues"]["social"]);
				if (is_null($this->iEmotional)) $this->emotional($arConfig["startvalues"]["emotional"]);
				if (is_null($this->iPhysical)) $this->physical($arConfig["startvalues"]["physical"]);
				if (is_null($this->iMental)) $this->mental($arConfig["startvalues"]["mental"]);
			}
		}
		
		public function search($arArgs = array(), $bCumulative = TRUE) { /*
			$arArgs = array (key=>value)
			($bCumulative == TRUE) ==> AND / ($bCumulative == FALSE) ==> OR 
		*/
			$oUser = new database();
			$arWhere = array();  
			foreach ($arArgs as $strKey => $strVal) {
				$arWhere[] = " $strKey = '$strVal' "; 
			} 
			if ($bCumulative) {
				$oUser->execute("select * from tblUsers where " . implode(" and ", $arWhere));
			} else {
				$oUser->execute("select * from tblUsers where " . implode(" or ", $arWhere));
			} 
			if ($oUser->length() == 1) {
				$this->id($oUser->get("id")); 
				$this->load(); 
			}
		}
		
		public function update() { 
			if ($this->bUnlocked || $this->bNEW || ($this->id() == me())) {
				$arVelden = array(
					"login" => $this->login(), 
					"firstname" => $this->firstname(), 
					"lastname" => $this->lastname(), 
					"alias" => $this->alias(), 
					"description" => $this->description(), 
					"img" => $this->img(), 
					"mail" => $this->email(), 
					"birthdate" => $this->birthdate(), 
					"gender" => $this->gender(), 
					"telephone" => $this->telephone(), 
					"visible" => ($this->visible()?1:0), 
					"showlastname" => ($this->visible("lastname")),
					"showfirstname" => ($this->visible("firstname")),
					"showdescription" => ($this->visible("description")),
					"showtelephone" => ($this->visible("telephone")),
					"showgender" => ($this->visible("gender")),
					"showbirthdate" => ($this->visible("birthdate")),
					"showemail" => ($this->visible("email")),
					"showimg" => ($this->visible("img")),
					"showlocation" => ($this->visible("location")), 
					"pass" => $this->password(), 
					"lastupdate" => owaesTime(), 
					"location" => $this->location(), 
					"location_lat" => $this->iLocationLat, 
					"location_long" => $this->iLocationLong, 
					"data" => json_encode($this->arData), 
				);  
				$oUser = new database();
				if ($this->bNEW) {
					$arVeldKeys = array(); 
					$arWaarden = array(); 
					foreach ($arVelden as $strVeld=>$strWaarde) {
						$arVeldKeys[] = $strVeld; 
						$arWaarden[] = "'" . $oUser->escape($strWaarde) . "'"; 
					}
					$strSQL = "insert into tblUsers (" . implode(", ", $arVeldKeys) . ") values (" . implode(", ", $arWaarden) . ");"; 
					$oUser->execute($strSQL);  
					$this->id($oUser->lastInsertID()); 
				} else {
					$arUpdates = array(); 
					foreach ($arVelden as $strVeld=>$strWaarde) {
						$arUpdates[] = $strVeld . " = '" . $oUser->escape($strWaarde) . "'"; 
					}
					$strSQL = "update tblUsers set " . implode(", ", $arUpdates) . " where id = " . $this->id() . ";"; 
					$oUser->execute($strSQL); 
				} 
			} else {
				error ("Geen rechten om deze gebruiker aan te passen (class.user, lijn " . __LINE__ . ")"); 	
			}
		}
		
		 
		 
		public function getURL() { // returns pad naar profiel
			return fixPath(($this->alias() != "") ? ($this->alias())  : ("profile.php?id=" . $this->iID)); 
		}
		
		public function getLink($bHTML = TRUE) { // link to article details  (html: "<a href="profiel.html">Voornaam Naam</a>") 
			return "<a href=\"" . $this->getURL() . "\">" . $this->getName() . "</a>"; 
		}
		
		public function getName() { // returns firstname lastname 
			if ($this->visible4me("firstname") && $this->visible4me("lastname")) {
				return $this->firstname() . " " . $this->lastname();
			} else if ($this->visible4me("firstname")) {
				return $this->firstname() ;
			} else if ($this->visible4me("lastname")) {
				return $this->lastname();
			} else return "x";  
		}
		
		public function messageLink($strSubject = "", $bHTML = TRUE) { /*
		returns een link naar de contactpagina van deze user (if $bHTML == TRUE => <a href=link.html>Naam</a> / anders: enkel link.html)
			TODO: er gebeurt niets met $strSubject
		*/
			global $oPage; 
			$strLink = "#";  
			if ($oPage->isLoggedIn()) {
				$strLink = fixpath("conversation.php?users=" .  $this->iID); 
				if ($bHTML) {
					$strHTML = "<a href=\"" . $strLink . "\" class=\"contact\"><img src=\"" . fixpath("img/contact.png") . "\" alt=\"Contacteer " . $this->getName() . "\" /></a>"; 	
				} else {
					$strHTML = $strLink; 	
				}
			} else $strHTML = ""; 
			return $strHTML; 
		}
		
		
		public function donateLink($strSubject = "", $bHTML = TRUE) { /*
		returns een link naar de contactpagina van deze user (if $bHTML == TRUE => <a href=link.html>Naam</a> / anders: enkel link.html)
			TODO: er gebeurt niets met $strSubject
		*/
			global $oPage; 
			$strLink = "#";  
			if ($oPage->isLoggedIn()) {
				$strLink = fixpath("donate.php?users=" .  $this->iID); 
				if ($bHTML) { 
                    //$strHTML = "<a href=\"" . fixPath("owaes-transactie.ajax.php?user=" .  $this->iID) . "\" class=\"transactie\"><img src=\"" . fixPath("img/handshake.png") . "\" alt=\"start transactie\" /></a>";
                    //$strHTML = "<a href='".fixPath("owaes-transactie.ajax.php?user=".$this->iID)."' class='transactie'><img src='".fixPath("img\handshake.png")."' alt='start transactie' /></a>";
                    $strHTML = fixPath("owaes-transactie.ajax.php?user=".$this->iID);
				} else {
					$strHTML = $strLink; 	
				}
			} else $strHTML = ""; 
			return $strHTML; 
		}
		
		public function getImage($strPreset = "thumbnail", $bHTML = TRUE) { /*
		 returns IMG-tag of -locatie van current user (afhankelijk van $bHTML)
		 $strPreset = "thumbnail" / "profile" / "50x60", "100", ...  
		 */
			$iWidth = 50; 
			$iHeight = 50;  
			switch($strPreset) {
				case "profile": 
					$iWidth = 160; 
					$iHeight = 220; 
					break; 
				case "thumbnail":  
					$iWidth = 70; 
					$iHeight = 70;  
				default: // case "50x50": 
					$arSize = explode("x", $strPreset);
					switch(count($arSize)) {
						case 2: 
							$iWidth = (is_numeric($arSize[0])) ? $arSize[0] : ""; 
							$iHeight = (is_numeric($arSize[1])) ? $arSize[1] : "";  
							break; 
						case 1: 
							if (is_numeric($arSize[0])){
								$iWidth = $arSize[0]; 
								$iHeight = $arSize[0]; 
							} 
							break;  
					}
					break;  
			}
			$iID = $this->visible4me("img") ? $this->id() : 0; 
			$strIMG = fixpath("profileimg.php?id=" . $iID . "&w=" . $iWidth . "&h=" . $iHeight . "&v=" . ($this->lastupdate()%500));   
			if ($bHTML) {
				return "<img src=\"$strIMG\" alt=\"" . $this->getName() . "\" width=\"" . $iWidth . "\" height=\"" . $iHeight . "\" />"; 	 
			} else {
				return $strIMG ; 
			} 
		}
		
		private function userbadge() {
			return "<div class=\"userbadge\">
						<span class=\"img\">" . $this->getImage("60x60", TRUE) . "</span>
						<span class=\"rating\" title=\"OWAES ranking\">" . $this->level() . "</span>
						<span class=\"stars\">" . str_repeat("<img src=\"" . fixPath("img/starb.png") . "\" />", $this->stars()) . "</span>
					</div>"; 
		}
		
		private function lastupdate($iLastUpdate = NULL) { 
			if (!is_null($iLastUpdate)) $this->iLastUpdate = $iLastUpdate; 
			return $this->iLastUpdate; 
		}
		
		public function userBox() { // returns html met profielfoto, naam, sterren en credits (TODO: wordt dit gebruikt? )  
			$strHTML = "<div class=\"userbox\"><a href=\"" . $this->getURL() . "\">"; 
			$strHTML .= "<span class=\"img\">" . $this->getImage("50x50") . "</span>"; 
			$strHTML .= "<span class=\"stars\">" . str_repeat("<img src=\"/owaes/img/star.png\" />", $this->stars()) . "</span>";  
			$iWidth = 50; 
			$iPos = intval($iWidth * $this->credits() / 2 / settings("startvalues", "credits")); 
			if ($iPos > 160) $iPos = 160; 
			$strHTML .= "<span class=\"credits\" style=\"width: " . $iPos . "px; \"></span>"; 
			$strHTML .= "<span class=\"name\">" . $this->getName() . "</span>";  
			$strHTML .= "</a></div>"; 
			return $strHTML; 
		}
		
		public function isCurrentUser() { // returns TRUE or FALSE
			if (!isset($this->bIsCurrentUser)) {
				global $oPage; 
				$this->bIsCurrentUser = ($this->iID == $oPage->iUser); 
			}
			return $this->bIsCurrentUser; 
		}
		
		public function payments($strType = NULL) {
			if (is_null($this->arPayments)) {
				$arPayments = array(
					"all" => array(), 
					"sent" => array(), 
					"received" => array(), 
				); 
				$oDB = new database();
				$oDB->sql("select * from tblPayments where (sender = '" . $this->id() . "' or receiver = '" . $this->id() . "') and actief = 1"); 
				$oDB->execute(); 
				while ($oDB->nextRecord()) {
					$oPayment = new payment(array(
						"sender" => $oDB->get("sender"), 
						"credits" => $oDB->get("credits"), 
						"receiver" => $oDB->get("receiver"), 
						"market" => $oDB->get("market"), 	
						"id" => $oDB->get("id"), 	
					));  
					$arPayments["all"][] = $oPayment; 
					if ($oDB->get("sender") == $this->id()) $arPayments["sent"][] = $oPayment;
					if ($oDB->get("receiver") == $this->id()) $arPayments["received"][] = $oPayment;
				}
				$this->arPayments = $arPayments; 
			}
			switch(strtolower($strType)) {
				case "all": 
				case "sent": 
				case "received":
					return $this->arPayments[strtolower($strType)]; 
					break; 	
				default:
					return $this->arPayments; 
					break; 	
			} 
		}
		 
		public function getBadges() { // returns Array (key => details) met badges van deze gebruiker 
			if (is_array($this->arBadges)) return $this->arBadges; 
			$arBadges = array(); 
			$oBadges = new database("select b.* from tblUserBadges u inner join tblBadges b on u.badge = b.id where u.user = " . $this->iID . ";", TRUE); 
			while ($oBadges->nextRecord()) {
				$arBadges[$oBadges->get("mkey")] = array(
					"img" => $oBadges->get("img"), 
					"info" => $oBadges->get("info"), 
					"title" => $oBadges->get("title"),  
				);
			} 
			$this->arBadges = $arBadges; 
			return $arBadges; 
		}
		
		function addBadge($strBadge) {
			$arBadges = $this->getBadges(); 
			if (!isset($arBadges[$strBadge])) {
				$oDB = new database(); 
				$oDB->execute("select * from tblBadges where mkey = '" . $oDB->escape($strBadge) . "'; "); 
				if ($oDB->record()) {
					$arBadges[$strBadge] = array(
						"img" => $oDB->get("img"), 
						"info" => $oDB->get("info"), 
						"title" => $oDB->get("title"), 
					);
					$oDB->sql("insert into tblUserBadges (user, badge, date) values ('" . $this->id() . "', '" . $oDB->get("id") . "', '" . owaesTime() . "'); "); 	
					$oDB->execute(); 
				}
			}
		}
		
		public function getCertificates() { // returns array (key => details) met certificaten van deze gebruiker 
			if (is_array($this->arCertificates)) return $this->arCertificates; 
			$arCertificates = array(); 
			$oCertificates = new database("select c.* from tblUserCertificates u inner join tblCertificates c on u.certificate = c.id where u.user = " . $this->iID . ";", TRUE);
			while ($oCertificates->nextRecord()) {
				$arCertificates[$oCertificates->get("mkey")] = array(
					"img" => $oCertificates->get("img"), 
					"info" => $oCertificates->get("info"), 
					"title" => $oCertificates->get("title"), 
				);
			} 
			$this->arCertificates = $arCertificates; 
			return $arCertificates; 
		}
		
		public function badgelist($arBadges, $iSize = 57) { // returns HTML met <ul> met badges
			$strHTML = "<ul class=\"badges\">";  
			foreach ($arBadges as $strKey => $arBadge) {  
				$strHTML .= "<li><img src=\"img/badges/" . $arBadge["img"] . "\" data-original-title=\"" . $arBadge["title"] . "\" alt=\"" .  $arBadge["title"] . "\" data-content=\"" . $arBadge["info"] . "\" width=\"$iSize\" height=\"$iSize\" /></li>"; 
			}
			$strHTML .= "</ul>"; 
			return $strHTML; 
		}
		
		public function credits() {
			if (is_null($this->iCredits)) {
				global $arConfig; 
				$iCredits = $arConfig["startvalues"]["credits"]; 
				$oDB = new database(); 
				$oDB->sql("select * from tblPayments where sender = '" . $this->id() . "' or receiver = '" . $this->id() . "' and actief = 1; "); 	
				$oDB->execute(); 
				while ($oDB->nextRecord()) {
					if ($oDB->get("receiver") == $this->id()) $iCredits += $oDB->get("credits"); 
					if ($oDB->get("sender") == $this->id()) $iCredits -= $oDB->get("credits"); 
				}
				$this->iCredits = $iCredits; 
			}	
			return $this->iCredits; 
		}
		
		public function creditsBox($iWidth = 150) { // returns HTML box-code met credits, stars en transactioncount   
			$strHTML = "<div class=\"creditsbox\">";  
			$iPos = intval($iWidth * $this->credits() / (settings("startvalues", "credits")*2)); 
			if ($iPos > 160) $iPos = 160; 
			$strHTML .= "<span class=\"credits\" style=\"width: " . $iPos . "px; \"></span>"; 
			global $oPage; 
			if ($this->iID == $oPage->iUser) {
				$strHTML .= "<span class=\"creditsshow\">" . $this->credits() . " credits</span>";  
			}
			$strHTML .= "<span class=\"stars\">" . str_repeat("<img src=\"/owaes/img/star.png\" />", $this->stars()) . "</span>"; 
			$strHTML .= "<span class=\"transactions\">" . count($this->payments("all")) . " transactie" . ((count($this->payments("all"))!=1)?"s":"") . "</span>"; 
			$strHTML .= "</div>"; 
			return $strHTML; 
		}
        
        public function userCredits() {   
			$strHTML = "0";
            global $oPage; 
			if ($this->iID == $oPage->iUser) {
                $credits = $this->credits();
                $creditsIndicator = "black";
                $strHTML = "<span class=\"title\">" . $this->credits() . "</span>";
                
                switch (true){
                    case $credits < 960:
                        $creditsIndicator = "red";
                        break;
                    case $credits < 2880:
                        $creditsIndicator = "orange";
                        break;
                    case $credits < 6720:
                        $creditsIndicator = "green";
                        break;
                    case $credits < 8640:
                        $creditsIndicator = "orange";
                        break;
                    case $credits >= 8640:  //9600
                        $creditsIndicator = "red";
                        break;
                    DEFAULT:
                        $creditsIndicator = "black";
                        break;
                }
                
                $strHTML .= "<span class=\"icon icon-credits icon-credits-" . $creditsIndicator . "\"></span>";
                //<span class="title">[credits]</span><span class="icon-credits"></span>
			}
			return $strHTML; 
		}
        
        public function userStars(){
			$strHTML = "<span class=\"stars\">" . str_repeat("<img src=\"" . fixPath("img/starb.png") . "\" />", $this->stars()) . "</span>";
			return $strHTML;
        }
         
        public function userLevel(){ // TODO wordt niet meer gebruikt
            $strHTML = "0";
            global $oPage; 
			if ($this->iID == $oPage->iUser) {
                $strHTML = $this->level();
			}
			return $strHTML; 
        }
        
		
		public function stars() {
			if (is_null($this->iStars)) { 
				$oDB = new database(); 
				$oDB->sql("select count(id) as aantal, sum(stars) as som from tblStars where receiver = '" . $this->id() . "' and actief = 1; "); 	
				$oDB->execute(); 
				$this->iStars = ($oDB->get("som") > 15) ? $oDB->get("som") / $oDB->get("aantal") : 0;
			}	
			return round($this->iStars); 
		}
		
		public function status() {
			switch($this->level()) {
				case 1: 
					return "Level 1: starter";  
					break; 	
				case 2: 
					return "Level 2: begonnen";  
					break; 	
				case 3: 
					return "Level 3: goed bezig";  
					break; 	
				default: 
					return "Level x";  
			} 
		}
		
		public function level() {
			if (is_null($this->iLevel)) $this->iLevel = $this->experience()->level(); 
			return $this->iLevel; 
		}
		
		private function developmentBoxesHeight($iValue) {
			// 3 tot 32
			$iMin = 3;
			$iMax = 32;
			return intval($iMax - ($iValue/100*($iMax-$iMin))); 
		}
		public function developmentBoxes() {
			$strHTML = "<ul class=\"development\">";
			$strHTML .= "<li class=\"physical\" title=\"Fysiek: " . $this->physical() . "%\" style=\"background-position: center " . $this->developmentBoxesHeight($this->physical()) . "px; \"><span></span></li>"; 
			$strHTML .= "<li class=\"mental\" title=\"Kennis: " . $this->mental() . "%\" style=\"background-position: center " . $this->developmentBoxesHeight($this->mental()) . "px; \"><span></span></li>"; 
			$strHTML .= "<li class=\"emotional\" title=\"Welzijn: " . $this->emotional() . "%\" style=\"background-position: center " . $this->developmentBoxesHeight($this->emotional()) . "px; \"><span></span></li>"; 
			$strHTML .= "<li class=\"social\" title=\"Sociaal: " . $this->social() . "%\" style=\"background-position: center " . $this->developmentBoxesHeight($this->social()) . "px; \"><span></span></li>"; 
			$strHTML .= "</ul>"; 
			return $strHTML;  
		}
         
	
		public function HTML($strTemplate = "", $bFile = TRUE) { // vraagt pad van template (of HTML if bFile==FALSE) en returns de html met replaced [tags] 
			$strHTML = $bFile ? content($strTemplate) : $strTemplate;  
			
			
			preg_match_all("/\[if:friends\]([\s\S]*?)\[\/if:friends\]/", $strHTML, $arResult);   // bv. [?data:facebook]heeft FB[/?data:facebook]   
			for ($i=0;$i<count($arResult[0]);$i++) {
				if (count($this->friends())>0) {
					$strHTML = str_replace($arResult[0][$i], $arResult[1][$i], $strHTML);
				} else {
					$strHTML = str_replace($arResult[0][$i], "", $strHTML);
				} 
			} 
			preg_match_all("/\[friends((?::([0-9]+)){0,1})\]([\s\S]*?)\[\/friends\\1\]/", $strHTML, $arResult);   // bv. [friends]loop[/friends] 
			for ($i=0;$i<count($arResult[1]);$i++) { 
				$strFriends = ""; 
				$iTeller = 0; 
				$iMax = intval($arResult[2][$i]); 
				foreach ($this->friends() as $oFriend) {  
					if ($iMax == 0 || ++$iTeller <= $iMax) $strFriends .= $oFriend->html($arResult[3][$i], FALSE);
				}
				$strHTML = str_replace($arResult[0][$i], $strFriends, $strHTML); 
			} 
			
			$strHTML = str_replace("[id]", $this->id(), $strHTML);
			$strHTML = str_replace("[firstname]", $this->firstname(), $strHTML);
			$strHTML = str_replace("[lastname]", $this->lastname(), $strHTML);
			$strHTML = str_replace("[name]", $this->getName(), $strHTML);
			$strHTML = str_replace("[username]", $this->login(), $strHTML);
			$strHTML = str_replace("[key]", (($this->alias() == "")?$this->iID:$this->alias()), $strHTML); // of id als er geen username is
			$strHTML = str_replace("[email]", $this->email(), $strHTML);
			$strHTML = str_replace("[description]", $this->description(), $strHTML);
			$strHTML = str_replace("[link]", $this->getURL(), $strHTML);
			$strHTML = str_replace("[url]", $this->getURL(), $strHTML);
			$strHTML = str_replace("[img]", $this->getImage("profile"), $strHTML); 
			$strHTML = str_replace("[img:src:70x70]", $this->getImage("70x70", FALSE), $strHTML); 
			$strHTML = str_replace("[userbadge]", $this->userbadge(), $strHTML); 
			$strHTML = str_replace("[development]", $this->developmentBoxes(), $strHTML);  
			if ($this->id() == me()) {   
				$strHTML = filterTag("me", $strHTML, TRUE); 
				$strHTML = filterTag("notme", $strHTML, FALSE);  
				$strHTML = filterTag("nofriend", $strHTML, FALSE); 
				$strHTML = filterTag("nofriend:asked", $strHTML, FALSE); 
				$strHTML = filterTag("nofriend:requested", $strHTML, FALSE); 
				$strHTML = filterTag("nofriend:noconnection", $strHTML, FALSE); 
				$strHTML = filterTag("friend", $strHTML, FALSE);   
				$strHTML = str_replace("[link:addfriend]", "#", $strHTML); 
			} else {
				$strHTML = filterTag("me", $strHTML, FALSE); 
				$strHTML = filterTag("notme", $strHTML, TRUE);  
				
				switch($this->iFriendStatus) {
					case FRIEND_FRIENDS: 
						$strHTML = filterTag("friend", $strHTML, TRUE);   
						break; 
					case FRIEND_ASKED: 
						$strHTML = filterTag("nofriend", $strHTML, TRUE);  
						$strHTML = filterTag("nofriend:asked", $strHTML, TRUE); 
						break; 
					case FRIEND_REQUESTED:  
						$strHTML = filterTag("nofriend", $strHTML, TRUE);  
						$strHTML = filterTag("nofriend:requested", $strHTML, TRUE); 
						break; 
					case FRIEND_NOFRIENDS: 
					default:  
						$strHTML = filterTag("nofriend", $strHTML, TRUE);  
						$strHTML = filterTag("nofriend:noconnection", $strHTML, TRUE);  
						break; 
				}  
				$strHTML = str_replace("[link:addfriend]", fixPath("addfriend.php?u=" . $this->id()), $strHTML);
				// overschot invisible
				$strHTML = filterTag("friend", $strHTML, FALSE); 
				$strHTML = filterTag("nofriend", $strHTML, FALSE);  
				$strHTML = filterTag("nofriend:asked", $strHTML, FALSE); 
				$strHTML = filterTag("nofriend:requested", $strHTML, FALSE); 
				$strHTML = filterTag("nofriend:noconnection", $strHTML, FALSE);    
			}
			
			$strHTML = preg_replace_callback('/\[profileimg\:([0-9]*x[0-9]*)\]/', array(&$this, "imageregreplace"), $strHTML);  
			
			// data-values
			preg_match_all("/\[data:([a-zA-Z0-9-]+)\]/", $strHTML, $arResult);   // bv. [data:facebook]
			if (isset($arResult[1])) foreach ($arResult[1] as $strK){
				$strHTML = str_replace("[data:$strK]", $this->data($strK), $strHTML); 
			} 
			preg_match_all("/\[if:data:([a-zA-Z0-9-]+)\]([\s\S]*?)\[\/if:data:\\1\]/", $strHTML, $arResult);   // bv. [?data:facebook]heeft FB[/?data:facebook]  
			for ($i=0;$i<count($arResult[0]);$i++) {
				if ($this->data($arResult[1][$i]) != "") {
					$strHTML = str_replace($arResult[0][$i],$arResult[2][$i], $strHTML); 					
				} else {
					$strHTML = str_replace($arResult[0][$i], "", $strHTML); 
				}
			} 
			
			if (instr("[friends:count]", $strHTML)) $strHTML = str_replace("[friends:count]", count($this->friends()), $strHTML);  
			if (instr("[friends:url]", $strHTML)) $strHTML = str_replace("[friends:url]", fixPath("friends.php?u=" . $this->id()), $strHTML);   
			if (instr("[badges]", $strHTML)) $strHTML = str_replace("[badges]", $this->badgelist($this->getBadges()), $strHTML);  
            if (instr("[badges:count]", $strHTML)) $strHTML = str_replace("[badges:count]", count($this->getBadges()), $strHTML);  
			if (instr("[certificates]", $strHTML)) $strHTML = str_replace("[certificates]", $this->badgelist($this->getCertificates()), $strHTML);  
			if (instr("[certificates:count]", $strHTML)) $strHTML = str_replace("[certificates:count]", count($this->getCertificates()), $strHTML); 
			if (instr("[badges:30]", $strHTML)) $strHTML = str_replace("[badges:30]", $this->badgelist($this->getBadges(), 30), $strHTML);  
			if (instr("[certificates:30]", $strHTML)) $strHTML = str_replace("[certificates:30]", $this->badgelist($this->getCertificates(), 30), $strHTML);  
			if (instr("[badges:20]", $strHTML)) $strHTML = str_replace("[badges:20]", $this->badgelist($this->getBadges(), 20), $strHTML);  
			if (instr("[certificates:20]", $strHTML)) $strHTML = str_replace("[certificates:20]", $this->badgelist($this->getCertificates(), 20), $strHTML);  
			if (instr("[transactions]", $strHTML)) $strHTML = str_replace("[transactions]", $this->creditsBox(), $strHTML);  
            if (instr("[#credits]", $strHTML)) $strHTML = str_replace("[#credits]", $this->credits(), $strHTML);   
            if (instr("[credits]", $strHTML)) $strHTML = str_replace("[credits]", $this->userCredits(), $strHTML);   
            if (instr("[level]", $strHTML)) $strHTML = str_replace("[level]", $this->level(), $strHTML); 
            if (instr("[experience]", $strHTML)) $strHTML = str_replace("[experience]", $this->experience()->total(FALSE), $strHTML); 
            if (instr("[experience:confirmed]", $strHTML)) $strHTML = str_replace("[experience:confirmed]", $this->experience()->total(FALSE), $strHTML); 
            if (instr("[experience:all]", $strHTML)) $strHTML = str_replace("[experience:all]", $this->experience()->total(TRUE), $strHTML); 
            if (instr("[leveltreshold]", $strHTML)) $strHTML = str_replace("[leveltreshold]", $this->experience()->leveltreshold(), $strHTML); 
            if (instr("[levelpercentage]", $strHTML)) $strHTML = str_replace("[levelpercentage]", floor(100 * $this->experience()->total() / $this->experience()->leveltreshold()), $strHTML); 
            if (instr("[social]", $strHTML)) $strHTML = str_replace("[social]", $this->social(), $strHTML); 
            if (instr("[physical]", $strHTML)) $strHTML = str_replace("[physical]", $this->physical(), $strHTML); 
            if (instr("[mental]", $strHTML)) $strHTML = str_replace("[mental]", $this->mental(), $strHTML); 
            if (instr("[emotional]", $strHTML)) $strHTML = str_replace("[emotional]", $this->emotional(), $strHTML); 
            if (instr("[location]", $strHTML)) $strHTML = str_replace("[location]", $this->location(), $strHTML); 
            if (instr("[stars]", $strHTML)) $strHTML = str_replace("[stars]", $this->userStars(), $strHTML);   
			
			
            if (instr("[editprofile-key]", $strHTML)) $strHTML = str_replace("[editprofile-key]", "<input type=\"hidden\" name=\"edit-profile\" value=\"" . $this->editkey() . "\" />", $strHTML);  
			 
			$strHTML = str_replace("[contact]", $this->isCurrentUser() ? "" : $this->messageLink() , $strHTML);
			$strHTML = str_replace("[donate]", $this->isCurrentUser() ? "" : $this->donateLink() , $strHTML);
			$strHTML = str_replace("[link:contact]", $this->isCurrentUser() ? "#" : $this->messageLink("", FALSE) , $strHTML);
			$strHTML = str_replace("[link:credits]", $this->isCurrentUser() ?"Je kan geen credits aan jezelf schenken." : $this->donateLink("",true),$strHTML);

		//	$strEXPid = "userexp" . $this->id() . "-" . rand(0,9999); 
		//	$strHTML .= "<p id=\"$strEXPid\">Experience: <a href=\"experience-confirm.ajax.php\" class=\"ajax\" rel=\"$strEXPid\">" . $this->experience()->total(FALSE) . "/" . $this->experience()->total(TRUE) . "</a></p>"; 


			return $strHTML; 
		} 
		private function imageregreplace(&$matches) { 
			return $this->getImage($matches[1], FALSE);  
		} 
		
		private function editkey() {
			if ($this->id() == me()) { // indien rechten: vaste key
				return md5($_SERVER['REMOTE_ADDR'] . $this->id()); 
			} else { 
				return md5($_SERVER['REMOTE_ADDR'] . time() . rand() . $this->id()); // altijd verschillende key
			}
		}
		
		public function groups($iGroup = NULL) { // if iGroup is specified: return group, otherwise return array with groups
			if (is_null($this->arGroups)) {
				$oGroepen = new grouplist();  
				$oGroepen->user($this->id()); 
				$this->arGroups = $oGroepen->getList(); 
			}
			if (is_null($iGroup)) {
				return $this->arGroups; 
			} else {
				foreach ($this->arGroups as $oGroup) if ($oGroup->id() == $iGroup) return $oGroup; 
				return FALSE; 
			}
		}
		
		public function friends() {
			$oList = new userlist(); 
			$oList->filter("friends", $this->id()); 
			return $oList->getList(); 	
		}
		

	}
	
?>