<?php 
	class page {   /*
	 $oPage wordt over de hele site geladen als GLOBAL (zit in inc.default.php)
	 TODO: public $iUser moet private worden 
	 */
		private $arMeta = array(); 
		private $strTitle = "OWAES";  // Page title (this value is the default) 
		private $arCSS = array(); 
		private $arJS = array(); 
		private $bLoggedInUser = FALSE; 
		public $iUser = 0; 
		private $strTab = NULL; 
		 
		public function page() {  
			$this->setMeta("author", "HOWEST"); 
			$this->setMeta("description", "OWAES");  
			$this->setMeta("content-language", "NL"); 
			$this->setMeta("content-language", "NL"); 
			$this->setMeta("viewport", "initial-scale=1, maximum-scale=1");
			
			$this->addCSS("style/reset-min.css"); 
            $this->addCSS("style/bootstrap.css");
            $this->addCSS("style/bootswatch.css");
            $this->addCSS("//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css");
			$this->addCSS("style/owaes.css"); 
			// $this->addCSS("style/min1100.css", "only screen and (max-width: 1100px)"); 
            $this->addCSS("style/style.css");
            // $this->addCSS("style/style2.css");
			$this->addCSS("//fonts.googleapis.com/css?family=Titillium+Web:400,700"); 
			$this->addJS("//code.jquery.com/jquery-1.9.1.js");  
            $this->addJS("//code.jquery.com/ui/1.10.3/jquery-ui.min.js");
            $this->addJS("bootstrap.js");
            $this->addJS("script/notify.min.js");
            //$this->addJS("script/bootstrap-datepicker.js");
			
            $this->addJS("script/moment.min.js");
            $this->addJS("script/fullcalendar.min.js"); 
            $this->addCSS("style/fullcalendar.css");
			
			$this->addJS("owaes.js?v5");  
            $this->addJS("main.js?v5");
            $this->addJS("vocabularium.js.php");
            $this->addJS("ckeditor/ckeditor.js");
			
//			if (strrpos("http://nu", settings("domain", "absroot")) === false)  redirect("link");  
			
			switch ($this->filename(FALSE)) {
				case "index.php": 
					if( isset($_GET["t"])) $this->tab("market." . $_GET["t"]);  
					break; 
				case "users.php": 
					$this->tab("users");  
					break; 
				case "settings.php":  
					if ($this->bLoggedInUser) $this->tab("settings");  
					break; 
				case "conversation.php":  
					$this->tab("messages");  
					break; 
			} 
			
		} 
		
		public function getHeader() { // returns all setted inside-<head> information
			global $arConfig;   
			$strHTML = "\n\t\t<title>" . $this->strTitle . "</title>";
			$strHTML .= "\n\t\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />"; 
			$strHTML .= "\n\t\t<link rel=\"shortcut icon\" href=\"" . fixPath("favicon.png") . "\" type=\"image/png\" />";  
			foreach($this->arMeta as $strKey=>$strVal) {
				$strHTML .= "\n\t\t<meta name=\"" . $strKey . "\" content=\"" . $strVal . "\" />"; 
			} 
			foreach($this->arCSS as $strFile=>$arData) {
				$strHTML .= "\n\t\t<link rel=\"stylesheet\" href=\"" . $strFile . "\" type=\"text/css\" media=\"" . $arData["media"] . "\" />"; 
			} 
			foreach($this->arJS as $strFile) {
				$strHTML .= "\n\t\t<script src=\"" . $strFile . "\"></script>"; 
			} 
			$strHTML .= "\n\t\t<script>
				var strRoot = \"" . settings("domain", "root") . "\"; 
			</script>"; 
			
			
			return $strHTML; 
		}
		
		public function setTitle($strTitle) { // overrides default title
			$this->strTitle = $strTitle; 
		}
		
		public function setMeta($strName, $strContent) { // adds new meta-tag or changes existing meta-tag
			$this->arMeta[$strName] = $strContent; 
		}
		public function addCSS($strFile, $strMedia = "all") { // nieuwe CSS-file toevoegen (relatief pad)
			$strFile = fixPath($strFile);  
			if (!isset($this->arCSS[$strFile]))  $this->arCSS[$strFile] = array(
				"media" => $strMedia, 
			); 
		}
		public function addJS($strFile) { // nieuwe javascript-file toevoegen (relatief pad)
			$strFile = fixPath($strFile); 
			if (!in_array($strFile, $this->arJS)) array_push($this->arJS, $strFile); 
		}
		
		public function removeMeta($strName){ // removes setted meta-tag
			unset ($this->arMeta[$strName]); 
		}
		
		public function tab($strTab = NULL) { // get / sets current tab ("opdrachten"/"marktplaats"/"gebruikers"/..)
			if (!is_null($strTab)) $this->strTab = $strTab;  
			return $this->strTab; 
		}
		
		public function startTabs() { // returns HTML-code voor tabbladen "opdrachten", "marktplaats", "profiel" 
			global $oSecurity; 
			$arTabs = array( 
				"home" => array(
					"title" => "Home", 
					"url" => "main.php", 
					"classes" => array("menu-item", "home"), 
				) 
			); 
			$oOwaesTypes = new owaestype(); 
			foreach ($oOwaesTypes->getAllTypes() as $strKey=>$strTitle) {
				$arTabs["market." . $strKey] = array(
					"title" => $strTitle, 
					"url" => "index.php?t=" . $strKey, 
					"classes" => array("menu-item", $strKey), 
				);
			}
			/*$arTabs["users"] = array(
					"title" => "gebruikers", 
					"url" => "users.php", 
					"classes" => array("users", "extratab"), 
				);*/
			if ($this->bLoggedInUser) {
				/*$oInbox = new inbox();
				if (count($oInbox->discussions()) > 0) {
					$arTabs["messages"] = array(
						"title" => "berichten", 
						"url" => "#", 
						"classes" => array("extratab", "mailbox"), 
						"sub" => array(),
					); 
					foreach ($oInbox->discussions() as $iKey=>$arUser) {
						if ($arUser["unread"] > 0) {
							$arTabs["messages"]["sub"][$arUser["names"] . " (" . $arUser["unread"] . ")"] = array("conversation.php?users=" . $arUser["ids"], "conversation unread"); 
						} else {
							$arTabs["messages"]["sub"][$arUser["names"]] = array("conversation.php?users=" . $arUser["ids"], "conversation"); 
						}
					}
				} 
                */
                $arTabs["lijsten"] = array (
                    "title" => "Lijsten", 
					"url" => "#", 
					"classes" => array("dropdown-toggle", "lijsten", "menu-item"), 
					"sub" => array(),
                );
                
                if (user(me())->levelrights("groepslijst")) $arTabs["lijsten"]["sub"]["Groepen"] = array("groups.php", "groups");
                if (user(me())->levelrights("gebruikerslijst")) $arTabs["lijsten"]["sub"]["Gebruikers"] = array("users.php", "gebruikers");
                $arTabs["lijsten"]["sub"]["Vrienden"] = array("friends.php", "friends");
				$arTabs["lijsten"]["sub"]["Badges"] = array("badges.php", "badges");
				if ($oSecurity->admin()) {
					$arTabs["lijsten"]["sub"]["Admin"] = array("admin.php", "admin");
					$arTabs["lijsten"]["sub"]["Reports"] = array("meldingen.php", "meldingen");
					$arTabs["lijsten"]["sub"]["Groepen"] = array("admin.groepen.php", "groups");
				}
                
                $arTabs["account"] = array (
                    "title" => "Account", 
					"url" => "#", 
					"classes" => array("dropdown-toggle", "account", "menu-item"), 
					"sub" => array(),
                );
                $arTabs["account"]["sub"]["Profiel"] = array("profile.php", "profiel");	
				$arTabs["account"]["sub"]["Berichten"] = array("conversation.php", "berichten");	
				$arTabs["account"]["sub"]["Instellingen"] = array("settings.php", "instellingen");
				$arTabs["account"]["sub"]["Paswoord aanpassen"] = array("modal.changepass.php", "paswoord domodal");
				$arTabs["account"]["sub"]["Afmelden"] = array("logout.php", "afmelden");	
                
				/*$arTabs["settings"] = array(
					"title" => "instellingen", 
					"url" => "settings.php", 
					"classes" => array("extratab", "settings"), 
					"sub" => array(),
				);
                
				foreach (user(me())->groups() as $oGroep) {
					$arTabs["settings"]["sub"][$oGroep->naam()] = array("group.php?id=" . $oGroep->id(), "groep"); 
				}
				*/
				//if ($oSecurity->admin()) $arTabs["account"]["sub"]["admin"] = array("admin.php", "admin"); 
				/*
				$arTabs["settings"]["sub"]["instellingen"] = array("settings.php", "settings");
				$arTabs["settings"]["sub"]["profiel"] = array("profile.php", "profile");
				$arTabs["settings"]["sub"]["uitloggen"] = array("logout.php", "login");
                */
			} else {
				$arTabs["login"] = array(
					"url" => "login.php?p=" . urlencode($this->filename()), 
					"classes" => array("menu-item", "login"), 
				); 
			}
			if (isset($arTabs[$this->tab()]["classes"])) $arTabs[$this->tab()]["classes"][] = "active"; 
            
            $strHTML = "";
            $strHTML .= "<nav class=\"navbar navbar-default\">";
            $strHTML .= "<div class=\"container\"><div class=\"row\"><div class=\"navbar-header\">";
            $strHTML .= "<a href=\"main.php\"><h1 class=\"navbar-brand\">OWAES</h1></a>";
            $strHTML .= "<button class=\"navbar-toggle\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navbar-main\"><span class=\"icon-bar\"></span><span class=\"icon-bar\"></span><span class=\"icon-bar\"></span></button>";
			$strHTML .= "</div><div class=\"navbar-collapse collapse\" id=\"navbar-main\"><ul class=\"nav navbar-nav navbar-right\">"; 
			foreach ($arTabs as $strKey => $arDetails) {
				$strTitel = isset($arDetails["title"]) ? $arDetails["title"] : $strKey; 
                if (!isset($arDetails["sub"])){
                    $strHTML .= "<li>";
				    $strHTML .= "<a href=\"" . fixPath($arDetails["url"]) . "\" class=\"" . implode(" ", $arDetails["classes"]) . "\">";
                    $strHTML .= "<span class=\"icon\"></span>";
                    $strHTML .= "<span class=\"title\">$strTitel</span></a>";
                    $strHTML .= "</li>";
                } else{
                    $strHTML .= "<li class=\"dropdown\">";
                    $strHTML .= "<a href=\"" . fixPath($arDetails["url"]) . "\" class=\"" . implode(" ", $arDetails["classes"]) . "\" data-toggle=\"dropdown\">";
                    $strHTML .= "<span class=\"icon\"></span>";
                    $strHTML .= "<span class=\"title\">$strTitel</span> <span class=\"caret\"></span></a>";
                    
                    $strHTML .= "<ul class=\"dropdown-menu\">"; 
					foreach ($arDetails["sub"] as $strSubTitel => $arSubDetails) {
						$strHTML .= "<li><a href=\"" . fixPath($arSubDetails[0]) . "\" class=\"" . $arSubDetails[1] . "\"><span class=\"icon-" . $arSubDetails[1] . "\"></span><span>$strSubTitel</span></a></li>";
					}
					$strHTML .= "</ul>"; 
                    $strHTML .= "</li>";
                }
				
				
			}
			$strHTML .= "</ul></div></div></div></nav>"; 
			//$strHTML .= "<div class=\"clock\">" . clock() . "</div>";   
			/*$strHTML .= "<form class=\"search\" action=\"" . fixPath("search.php") . "\" method=\"get\">
							<input class=\"searchfield\" type=\"text\" name=\"q\" " . (isset($_GET["q"])?("value=\"" . inputfield($_GET["q"]) . "\""):"") . " />
							<input class=\"searchbutton\" type=\"submit\" value=\"zoeken\" />
						</form>";   */
            //$strHTML .= "<ul class=\"popupmessages\"></ul>";   
			if (!$this->bLoggedInUser) { 
				$strHTML .= "<div class=\"loginbar\">
								Log in:  
								<form action=\"" . fixPath("login.php") . "\" method=\"post\">
								<input type=\"hidden\" name=\"from\" id=\"from\" value=\"" . $this->filename(TRUE) . "\" />
								<input type=\"text\" name=\"username\" id=\"username\" />
								<input type=\"password\" name=\"pass\" id=\"pass\" />
								<input type=\"submit\" name=\"dologin\" value=\"inloggen\" />
								</form>
								of <a href=\"" . fixPath("login.php?p=" . urlencode($this->filename())) . "\">registreer</a>
							</div>";
			} 
			//$strHTML .= "<div id=\"ADMIN\">
			//					<ul><a href=\"#\" rel=\"SQL\">show/hide SQL</a></ul>
			//				</div>";
			
			if(settings("analytics")!="") {
				$strHTML = "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '" . settings("analytics") . "', 'auto');
  ga('send', 'pageview');

</script>" . $strHTML; 	
			}
			
			return $strHTML; 
		}
		
		public function endTabs() { // returns closing tags for function startTabs()
			return ""; 
		}
		
		public function filename($bQuery = TRUE) { // returns filename of current script ($bQuerye: enkel filename of ook met querystring?)
			$arFN = explode("/", $_SERVER['SCRIPT_FILENAME']); 
			$strFN = $arFN[count($arFN)-1]; 
			if ($bQuery) {
				$arQRY = array(); 
				foreach ($_GET as  $strKey=>$strVal) {
					array_push($arQRY, $strKey . "=" . urlencode($strVal)); 
				} 
				if (count($arQRY)>0) $strFN .= "?" . implode("&", $arQRY); 
			}
			return $strFN;
		}
		
		public function loggedIn($bLoggedIn = TRUE) { // SETS logged in (TODO: moet dit niet in security?...)
			$this->bLoggedInUser = $bLoggedIn;
		}
		
		public function isLoggedIn() { // GETS logged in (TODO: moet dit niet in security?...)
			return $this->bLoggedInUser;
		}
		
		public function footer(){ // footer tekst  
			return "<section class='block block-block contextual-links-region clearfix'>
                        <div class='container'>
	                        <div class='yellowPart'><span>OWAES &ndash; Online Werk Activatie EcoSysteem &ndash; is een <a href='http://www.esf-agentschap.be/' tabindex='-1'>ESF</a> ondersteund onderzoeksproject onder leiding van Howest.</span></div>
	                        <div class='whitePart'>
								<div class='xtralinks'>
									<a href='" . fixPath("algemenevoorwaarden.php") . "'>- gebruikersvoorwaarden</a>
									<a href='" . fixPath("conversation.php?u=0") . "'>- contacteer OWAES</a>
								</div>
								<div class='logos'>
									<img src='" . fixPath("img/footer/pub_leeuw.png") . "' alt='' />
									<img src='" . fixPath("img/footer/logo_eu_.png") . "' alt='' />
									<a href='http://www.esf-agentschap.be' tabindex='-1' target='_blank'><img class='esfLogoFooter' src='" . fixPath("img/footer/esfLogo.png") . "' alt='ESF' /></a>
								</div>
                            </div>
                        </div>
                     </section>";
		
		}
		 
		
	}
	 
