<?php
	define ("PAGE", 0); 
	define ("SCRIPT", 1); 
	define ("AJAX", 2);  
	
	$o_GLOBAL_security = NULL; 
	
	function security($bHasToBeLoggedIn = TRUE, $iType = PAGE) {  
		global $o_GLOBAL_security; 
		if (is_null($o_GLOBAL_security)) $o_GLOBAL_security = new security($bHasToBeLoggedIn = TRUE, $iType = PAGE);  
		return $o_GLOBAL_security; 
	}
	
	class security {   /*
	wordt aangeroepen vanaf elke pagina, sessie wordt aangemaakt / opgeroepen
	- inloggen van de bezoeker
	- opvragen of de gebruiker bepaalde rechten heeft 
	- de gebruiker uitloggen
	*/
		private $bLoggedIn = FALSE; 
		private $bAdmin = FALSE; 
		private $strError = ""; 
		private $iUser = 0; 
		private $oUser;  
		private $iType = PAGE;  
		 
		public function security($bHasToBeLoggedIn = TRUE, $iType = PAGE) { /* 
			- optional $bHasToBeLoggedIn : indien TRUE kan de huidige pagina enkel geopend worden door ingelogde gebruikers, indien niet ingelogd wordt de gebruiker doorgestuurd naar loginscherm, indien FALSE; pagina kan door iedereen / alles gelezen worden
			- optional $iType : PAGE / SCRIPT / AJAX : nodig om te weten met welke code de gebruiker doorgestuurd wordt (indien niet ingelogd)
			- indien niet ingelogd maar wel nodig: afhankelijk van $iType wordt code / redirect gestuurd + EXIT()
		*/
			//ini_set('session.use_cookies', 0);
			//ini_set('session.use_only_cookies', 0);
			//ini_set('session.use_trans_sid', 1);
			
			session_start();
			$this->setLoggedIn(FALSE); 
			$this->iType = $iType; 
			if(isset($_SESSION['session']) || isset($_COOKIE['session'])) {
				$iUser = isset($_SESSION['userid'])?$_SESSION['userid']:(isset($_COOKIE['user'])?$_COOKIE['user']:0);
				$strSession = isset($_SESSION['session'])?$_SESSION['session']:(isset($_COOKIE['session'])?$_COOKIE['session']:""); 
				$iTime = owaesTime();  
				$oDBrecord = new database("select s.*, u.admin, u.deleted from tblUserSessions s 
												inner join tblUsers u on s.user = u.id 
												where s.user = $iUser 
													and s.sessionpass = '$strSession' 
													and s.start <= $iTime 
													and s.stop >= $iTime 
													and s.active = 1
												; ", true);  
				if ($oDBrecord->length() > 0) {
					// maybe a check for IP (if session is copied)

					$this->setLoggedIn($oDBrecord->get("deleted")==0); 
					$this->setUserID($iUser);  
					$this->admin($oDBrecord->get("admin")==1); 
				} 
			}

			if ($bHasToBeLoggedIn && !$this->bLoggedIn) { 
				global $oPage;  
				switch($iType) {
					case AJAX:  
						echo ("<a href=\"/owaes/login.php\">U bent niet meer ingelogd. Klik hier om terug in te loggen. </a>"); 
						break; 
					case SCRIPT: 
						echo ("document.location.href = '/owaes/login.php'; ");
						break; 
					case PAGE: 
					default: 
						redirect("login.php?p=" . urlencode($oPage->filename())); 
						break; 	
				}  
				exit();  
			}
			
			return $this->bLoggedIn; 
		}
		
		public function ingelogd() { // returns boolean of gebruiker ingelogd is
			return $this->bLoggedIn;
		}
		
		public function user($iUser = NULL) { /* SETS / GETS current user 
			TODO: zou private moeten zijn (enfin, SET-gedeelte)
		*/
			if (!is_null($iUser)) {
				$this->iUser = $iUser; 
				me($iUser); 
			} 
			return user($this->iUser); 	
		}
		
		
		private function setLoggedIn($bValue) { 
			$this->bLoggedIn = $bValue; 
			global $oPage; 
			$oPage->loggedIn($bValue); 
		}
		
		public function admin($bValue = NULL) { // returns boolean: admin-rechten ?
			if (!is_null($bValue)) $this->bAdmin = $bValue; 
			return $this->bAdmin; 
		}
		
		public function doLogin($strUser, $strPass = NULL) {
            //echo "doLogin"; 
			$this->setLoggedIn(FALSE); 
			$this->strError = ""; 
			$strUser = mysql_escape_string($strUser); 
			$strPass = is_null($strPass) ? NULL : md5(trim($strPass)); 
			$strSession = uniqueKey(); 
			
			$arWhere = array("deleted = 0"); 
			$arWhere[] = (strrpos($strUser, "@") === false) 
				? "login = '" . $strUser . "'" 
				: "mail = '" . $strUser . "'"; 
			$oDBquery = new database("select * from tblUsers where " . implode(" and ", $arWhere) . " limit 0, 1; ");  
			if ($oDBquery->execute() > 0) {
				if (($oDBquery->get("actief")==0)||($oDBquery->get("deleted")==1)) {
					$this->strError = "Deze account werd geblokkeerd"; 
					return false; 
				} else {
					if (($oDBquery->get("pass") == $strPass) || (is_null($strPass)))  {
						session_start();
						$iUser = $oDBquery->get("id");  
						$_SESSION['userid'] = $iUser; 
						$_SESSION['session'] = $strSession;
						
						$iStart = owaesTime(); 
						$iStop = owaesTime() + 60*60*24*14; // session valid for 2 weeks 
						$strIP = $_SERVER['REMOTE_ADDR']; 
						$strConf = mysql_escape_string($_SERVER['HTTP_USER_AGENT']); 
						$oDBinsert = new database("insert into tblUserSessions (user, start, stop, sessionpass, active, ip, conf) values ($iUser, $iStart, $iStop, '$strSession', 1, '$strIP', '$strConf'); ", true); 
						$this->setLoggedIn(TRUE); 
						setcookie("user", $iUser, $iStop);
						setcookie("session", $strSession, $iStop);
						return true; 
					} else {
						// echo $oDBquery->table(); 
						// echo $strPass; 
						console("class.security", "dologin() - Incorrect password.");
						$this->strError = "Je gaf een verkeerd wachtwoord in"; 
						return false; 
					}
				}
			} else {
                console("class.security", "dologin() - Incorrect username.");
				$this->strError = "Deze gebruikersnaam werd niet herkend. Gebruik je loginnaam of e-mailadres. ";
				return false; 
			} 
		}
		
		public function doLogout($bResult = TRUE) { // forceert een logout.  ($bResult : indien TRUE: deze functie zorgt voor redirect of HTML / bij FALSE moet afhandeling in pagina zelf gedaan worden. 
			if(isset($_SESSION['session'])) {
				$iUser = $_SESSION['userid']; 
				$strSession = $_SESSION['session']; 
				$iTime = owaesTime();
				$oDBlogout = new database("update tblUserSessions set active = 0, stop = $iTime where user = $iUser and sessionpass = '$strSession'; ", true);
			}
			session_destroy();
			$this->setUserID(0); 
			$this->setLoggedIn(FALSE); 
			if (isset($_COOKIE["user"])) setcookie("user", "", owaesTime()-3600);
			if (isset($_COOKIE["session"])) setcookie("session", "", owaesTime()-3600);
			 
			if ($bResult) {
				switch($this->iType) {
					case AJAX: 
						echo ("<a href=\"/owaes/login.php\">U bent niet meer ingelogd. Klik hier om terug in te loggen. </a>"); 
						break; 
					case SCRIPT: 
						echo ("document.location.href = '/owaes/login.php'; ");
						break; 
					case PAGE: 
					default: 
						redirect("login.php"); 
						break; 	
				}   
			}
			return TRUE;  
		}
		
		public function errorMessage() { // geeft foutmelding na ongeldige inlogpoging op doLogin (bv. ongelidg paswoord of ongeldige login) 
			return $this->strError; 
		}
		
		public function getUserID() { // TODO: zou weg moeten  
			return $this->iUser; 	
		} 
		
		private function setUserID($iUser) { // TODO: zou weg moeten  
			$this->user($iUser); 
//			$this->iUser = $iUser; 
			global $oPage; 
			$oPage->iUser = $iUser; 
		}
		
		public function me() { /* geeft ID van huidige gebruiker terug. 
			TODO: zou weg moeten want er bestaat een GLOBAL function me()
			*/
			if (!isset($this->oUser)) $this->oUser = user($this->iUser); 
			return $this->oUser; 
		}
		 
	}
	