<?php 
	$i_GLOBAL_userid = 0; 
	function me($iID = NULL) { // me() returns current user ID and is available throughout the whole application (set in class.security)
		global $i_GLOBAL_userid; 
		if (!is_null($iID)) $i_GLOBAL_userid = $iID; 
		return $i_GLOBAL_userid; 
	}
	 
	function owaesTime() {
		$iSpeed = settings("date", "speed"); 
		$iStart = settings("date", "start");
		$iDiff = settings("date", "servertime");
		
		$iTime = ((time()-$iStart)*$iSpeed) + $iStart + $iDiff; 
		return $iTime; 	
	}
	
	function filterTag($strTag, $strHTML, $bShow = FALSE) {
		if ($bShow) {
			$strHTML = str_replace("[$strTag]", "", $strHTML);  
			$strHTML = str_replace("[/$strTag]", "", $strHTML);  
		} else {
			$strHTML = preg_replace("/\[$strTag\][\s\S]*?\[\/$strTag\]/", "", $strHTML);  
		}
		return $strHTML; 
	}
	
	function createProfilePicture($strLocation, $iUserID, $bKeepOld = FALSE) {
		$arLocation = explode(".", $strLocation); 
		$strExt = strtolower($arLocation[count($arLocation)-1]);  
		switch($strExt) {
			case "jpg": 
			case "jpeg": 
			case "gif": 
			case "png": 
				switch($strExt) {
					case "jpg": 
					case "jpeg": 
						$oProfileIMG = imagecreatefromjpeg($strLocation);
						break; 
					case "gif": 
						$oProfileIMG = imagecreatefromgif($strLocation);
						break; 
					case "png": 
						$oProfileIMG = imagecreatefrompng($strLocation);
						break; 
				}
				$iX = imagesx($oProfileIMG); 
				$iY = imagesy($oProfileIMG); 
				$iX2 = 0; 
				if ($iX > $iY) {
					if ($iX > 300) {
						$iY2 = 300 / ($iX/$iY); 
						$iX2 = 300; 
					} 
				} else {
					if ($iY > 300) {
						$iX2 = 300 / ($iY/$iX); 
						$iY2 = 300; 
					}	
				}
				if ($iX2 != 0) {
					$oThumb = imagecreatetruecolor($iX2, $iY2);
					imagecopyresampled($oThumb, $oProfileIMG, 0, 0, 0, 0, $iX2, $iY2, $iX, $iY);
					imagepng($oThumb, "upload/profiles/id/" . $iUserID . ".png");
					imagedestroy($oThumb); 
				} else {
					imagepng($oProfileIMG, "upload/profiles/id/" . $iUserID . ".png");
				}
				imagedestroy($oProfileIMG); 
				if (!$bKeepOld) unlink($strLocation); 	
				return TRUE; 
				break;  
			default: 
				return FALSE; 	
		}
	}
	/*
		

			$arIMG = getimagesize($_FILES["img"]["tmp_name"]); 
			$strImage = uniqueKey() . "." . extentie($_FILES["img"]["name"]);  
			switch($arIMG["mime"]) {
				case "image/jpeg": 
				case "image/gif": 
				case "image/png": 
					move_uploaded_file($_FILES["img"]["tmp_name"], "upload/profiles/" . $strImage); 
					$oProfile->img($strImage); 
					$bImageUploaded = TRUE;
					switch($arIMG["mime"]) {
						case "image/jpeg": 
							$oProfileIMG = imagecreatefromjpeg("upload/profiles/" . $strImage);
							break; 
						case "image/gif": 
							$oProfileIMG = imagecreatefromgif("upload/profiles/" . $strImage);
							break; 
						case "image/png": 
							$oProfileIMG = imagecreatefrompng("upload/profiles/" . $strImage);
							break; 
					}
					$iX = imagesx($oProfileIMG); 
					$iY = imagesy($oProfileIMG); 
					$iX2 = 0; 
					if ($iX > $iY) {
						if ($iX > 300) {
							$iY2 = 300 / ($iX/$iY); 
							$iX2 = 300; 
						} 
					} else {
						if ($iY > 300) {
							$iX2 = 300 / ($iY/$iX); 
							$iY2 = 300; 
						}	
					}
					if ($iX2 != 0) {
						$oThumb = imagecreatetruecolor($iX2, $iY2);
						imagecopyresampled($oThumb, $oProfileIMG, 0, 0, 0, 0, $iX2, $iY2, $iX, $iY);
						imagepng($oThumb, "upload/profiles/id/" . $oProfile->id() . ".png");
						imagedestroy($oThumb); 
					} else {
						imagepng($oProfileIMG, "upload/profiles/id/" . $oProfile->id() . ".png");
					}
					imagedestroy($oProfileIMG); 
					break;  
				default: 
 					// echo $arIMG["mime"];   // niet ondersteund



	}
	*/
	/*
	function HTMLelement($strTag, $arAttributes = array(), $oContent = NULL) { // $oContent kan string zijn, of array met strings of andere HTMLelements
		$strHTML = "<$strTag"; 	 
		foreach ($arAttributes as $strAtt=>$strVal) {
			$strHTML .= " $strAtt=\"" . str_replace('"', "&quot;", $strVal) . "\""; 
		}
		if (is_null($oContent)) {
			$strHTML .= " />"; 
		} else {
			$strHTML .= ">"; 
			if (is_array($oContent)) foreach ($oContent as $strChild) {
				$strHTML .= $strChild; 
			} else $strHTML .= $oContent;
			$strHTML .= "</$strTag>"; 
		}
		return $strHTML; 
	}
	*/
	
	function showDropdown($strName, $iValue) { // de dropdown bij instellingen en profile-page waar kan ingesteld worden wie wat ziet
		$strHTML = "<select name=\"$strName\" class=\"form-control\">"; 
		$strHTML .= "<option value=\"" . VISIBILITY_VISIBLE . "\"" . ((intval($iValue)==VISIBILITY_VISIBLE) ? " selected=selected" : "") . ">Zichtbaar voor iedereen</option>"; 
		$strHTML .= "<option value=\"" . VISIBILITY_FRIENDS . "\"" . ((intval($iValue)==VISIBILITY_FRIENDS) ? " selected=selected" : "") . ">Enkel zichtbaar voor vrienden</option>"; 
		$strHTML .= "<option value=\"" . VISIBILITY_HIDDEN . "\"" . ((intval($iValue)==VISIBILITY_HIDDEN) ? " selected=selected" : "") . ">Verborgen</option>"; 
		$strHTML .= "</select>"; 
		return $strHTML; 
	}
	
	function clock($iTime = NULL) {
		if (is_null($iTime)) $iTime = owaesTime(); 
		return str_date($iTime, "j M y H:i"); 
	}
	
	function admin(){
		$bAdmin = FALSE; 
		try {
			global $oSecurity;  
			$bAdmin = ($oSecurity->admin());
		} catch (Exception $e) { }
		return $bAdmin; 
	}
	
	function vardump($oVar, $oVar2 = NULL, $oVar3 = NULL) { // var_dump with layout  
		$strTitle = ""; 
		foreach($GLOBALS as $var_name => $value) {
			if ($value === $oVar) {
				$strTitle = "title = \"$" . $var_name . "\""; 
			}
		}
		echo ("<pre class=\"vardump\" $strTitle >"); 
		var_dump($oVar);  
		echo ("</pre>");  			
		if (!is_null($oVar2)) {
			echo ("<pre class=\"vardump\" $strTitle >"); 
			var_dump($oVar2);  	
			echo ("</pre>");  			
		}	
		if (!is_null($oVar3)) {
			echo ("<pre class=\"vardump\" $strTitle >"); 
			var_dump($oVar3);  
			echo ("</pre>");  		
		} 
		echo " "; 
	}
	
	function javatime($iTime) {
		return $iTime * 1000; 	
	}
	
	function settings($strA, $strB = NULL, $strC = NULL) {
		global $arConfig; 
		if (isset($strC)) return $arConfig[$strA][$strB][$strC]; 
		if (isset($strB)) return $arConfig[$strA][$strB]; 
		return $arConfig[$strA]; 
	}
	
	
	function json($strFile, $oJSON = NULL) { 
		if (is_null($oJSON)) {
			if (file_exists($strFile)) {
				$strJSON = @file_get_contents($strFile, TRUE); 
				if ($strJSON) {
					$oJSON = json_decode($strJSON, TRUE); 
				} else $oJSON = array(); 
			} else $oJSON = array(); 
		} else {
			$fh = fopen($strFile, 'w') or die("can't open file"); 
			fwrite($fh, json_encode($oJSON));
			fclose($fh); 	
		}
		return $oJSON; 
	}
	  

	function getXY($strSearch) {
		// $strKey = "ABQIAAAA_CS-CjEGYWpUz_Xfzu7K6xRbHvlh9iWZpZGiNwuSfYnCrohmjRQLa5XQUQeO-tVVQN2YVKmVcCg7mA" ; 
		$strURL = "http://maps.googleapis.com/maps/api/geocode/json" .
			"?address=" . urlencode($strSearch) . 
			"&sensor=false"; 
		$strResult = file_get_contents(cache($strURL));
		$arResult = json_decode($strResult, TRUE); 
 
		$arReturn = array();   
		if (isset($arResult["results"][0]["geometry"]["location"]["lng"])) $arReturn["longitude"] = $arResult["results"][0]["geometry"]["location"]["lng"];
		if (isset($arResult["results"][0]["geometry"]["location"]["lat"])) $arReturn["latitude"] = $arResult["results"][0]["geometry"]["location"]["lat"];
 
		return $arReturn; 
			 
	}
		
	function logo($iSize) {
		$strIMG = ($iSize > 70)?fixPath("img/owaes.png"):fixPath("img/owaes2.png");
		return "<img src=\"" . $strIMG . "\" alt=\"OWAES\" style=\"width: " . $iSize . "px; height: " . $iSize . "px;\" />"; 
	}

	function error($strMessage) {
		echo $strMessage; 
		exit(); 
	}
	
	 
	function owaes_error_handler($number, $message, $file, $line, $vars) {
		if (settings("debugging", "showwarnings")) {
			switch($number) {
				case E_NOTICE:  
				case E_WARNING:  
					$bSerious = FALSE; 
					break; 
				default: 
					$bSerious = TRUE; 
			}
			$strMelding = "<h4>" . ($bSerious?"Error":"Warning") . "</h4>
				<p><i>$file<br />line: $line</i></p>
				<p>$message </p>";
			echo ("<style>
				div.phperror {display: block; width: 400px; z-index: 999; background: white; position: absolute; border: 2px solid red; padding: 8px; cursor: pointer; position: absolute; z-index: 999;  }
				div.phperror div.erroricon {background: url(\"" . fixPath("img/error.png") . "\") no-repeat top " . ($bSerious?"right":"left") . "; width: 60px; height: 60px; float: left; }
				div.phperror div.errormelding  {float: left; width: 320px; margin-left: 20px; }
				div.phperror h4 {font-weight: bold; }  
			</style><div class=\"phperror\" onclick=\"$(this).hide();\"><div class=\"erroricon\"></div><div class=\"errormelding\">" . $strMelding . ($bSerious?"<p><i>Deze melding werd gemaild naar benedikt.beun@howest.be</i></p>":"") . "</div></div>");  
				 
			$strMelding .= "<pre>" . print_r($vars, 1) . "</pre>";
			 
			$strHeaders = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			  
			if ($bSerious) error_log($strMelding, 1, 'benedikt.beun@howest.be', $strHeaders);
			
			if ( ($number !== E_NOTICE) && ($number < 2048) ) {
				die("There was an error. Please try again later.");
			}
		}
	}	 
	
	function cache($strURL, $strExt = NULL, $iHours = -1) {
		if (is_null($strExt)) {
			$arURL = explode("?", $strURL); 
			$arURL = explode(".", $arURL[0]); 
			$strExt = $arURL[count($arURL)-1];  
			if (strlen($strExt) > 4) $strExt = "file";
		}
		$strCache = "cache/" . md5($strURL) . "." . $strExt; 
		if (file_exists($strCache)) { 
			if (($iHours == -1) || (filemtime($strCache)>owaesTime()-(60-60*$iHours))) return $strCache; 
		}
		copy($strURL, $strCache);	
		return $strCache; 
	}

	function content($fn) {
		$handle = fopen($fn, "r");
		$contents = fread($handle, filesize($fn));
		fclose($handle);
		return $contents; 
	}
	
	function save($strFile, $strTekst){
		$fh = fopen($strFile, 'w') or die("can't open file");
		fwrite($fh, $strTekst);
		fclose($fh);
	}
	
	function redirect($strURL) {
		header("Location: " . $strURL); 
		exit(); 
	}
	
	function fixPath($strURL, $bAbsolute = FALSE) {
		global $arConfig;  
		if ((strrpos($strURL, "://") === false) && (substr($strURL, 0, 2)!="//")) {  // relatief pad 
			if (substr($strURL, 0, 1) == "/"){ 
				return ($bAbsolute ? $arConfig["domain"]["absroot"] : $arConfig["domain"]["root"]) . substr($strURL, 1); 
			} else { 
				return ($bAbsolute ? $arConfig["domain"]["absroot"] : $arConfig["domain"]["root"]) . $strURL; 
			}
		} else { // absoluut pad
			return $strURL;  
		}
	}
	
	function uniqueKey() {
		return md5(owaesTime() . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . rand(0,100000)); 	
	} 
	
	function extentie($strFN) {
		$arExt = explode(".", strtolower($strFN));
		if (count($arExt) > 0) return $arExt[count($arExt)-1]; 
	}
	
	function inputfield($strTekst) {
		return str_replace('"', '&quot;', $strTekst); 
	}
	function textarea($strTekst) {
		return str_replace('<', '&lt;', $strTekst); 
	} 
	
	function str2url($str, $strExt=""){
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		$str = str_replace($a, $b, $str); 
		$str = strtolower(preg_replace(array('/[^a-zA-Z0-9 -_]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $str));
		if ($str == "") $str = "nieuw"; 
		if (strlen($str) > 30) $str = substr($str, 0, 30); 
		return $str . (($strExt!="")?("." . $strExt):""); 
	}
	
	
	function shorten($strText, $iLength = 100, $bJS = false) {
		if (strlen($strText) > $iLength+60) {
			$strShortened = substr($strText, 0, $iLength);  
			while ((strrpos(" .,!?:;)", substr($strShortened, -1)) === false) && (strlen($strShortened) > ($iLength/2))) $strShortened = substr($strShortened, 0, -1); 
			if ($bJS) { 
				$strResult = $strShortened . "<span class=\"moreA\">... <span>[lees meer]</span></span><span class=\"moreB\">" . substr($strText, strlen($strShortened)-strlen($strText)) . "</span>";  
			} else {
				$strResult = $strShortened . " ...";  
			}
			return $strResult; 
		} else {
			return $strText; 
		}
	}
	 
	 
	function instr($strSearch, $strSubject) { 
		return (strrpos($strSubject, $strSearch) !== false); 
	}
	
	function str_date($dDate, $strFormat = "") {
		if ($dDate == 0) return ""; 
		$arDagen = array("", "ma", "di", "woe", "do", "vr", "zat", "zon");
		$arDagenFull = array("", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag", "zondag");
		$arMaanden = array("", "januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"); 
		$iDag = date("j", $dDate); 
		$strDagKort = $arDagen[date("N", $dDate)] . "."; 
		$strDagFull = $arDagenFull[date("N", $dDate)]; 
		$strJaar = date("'y", $dDate); 
		$strMaand = $arMaanden[date("n", $dDate)]; 
		$strTijd = date("H:i", $dDate); 
		 
		switch($strFormat) {
			case "datum": 
				if (date("j M y", $dDate) == date("j M y", owaesTime())){ // vandaag 
					return "vandaag";
				} else if ($dDate < owaesTime()) { // geschiedenis
					$iDagen = (owaesTime() - $dDate)/60/60/24;
					if (date("j M y", $dDate) == date("j M y", owaesTime()-(24*60*60))){ // gisteren
						return "gisteren";
					} else if ($iDagen < 60) { // max. 60 dagen geleden
						return "$strDagKort $iDag $strMaand";
					} else return "$strDagKort $iDag $strMaand $strJaar";  
				} else { // toekomst
					$iDagen = ($dDate - owaesTime())/60/60/24;
					if (date("j M y", $dDate) == date("j M y", owaesTime()+(24*60*60))){ // morgen
						return "morgen ($iDag $strMaand)";
					} else if ($iDagen < 60) { // max. 60 dagen 
						return "$strDagKort $iDag $strMaand";
					} else return "$strDagKort $iDag $strMaand $strJaar"; 
				}
				break; 
			case "datumtijd": 
			case "": 
				if (date("j M y", $dDate) == date("j M y", owaesTime())){ // vandaag 
					return "vandaag om $strTijd";
				} else if ($dDate < owaesTime()) { // geschiedenis
					$iDagen = (owaesTime() - $dDate)/60/60/24;
					if (date("j M y", $dDate) == date("j M y", owaesTime()-(24*60*60))){ // gisteren
						return "gisteren om $strTijd";
					} else if ($iDagen < 6) { // max. 6 dagen geleden
						return "$strDagFull om $strTijd";
					} if ($iDagen < 60) { // max. 60 dagen geleden
						return "$strDagKort $iDag $strMaand om $strTijd";
					} else return "$strDagKort $iDag $strMaand $strJaar";  
				} else { // toekomst
					$iDagen = ($dDate - owaesTime())/60/60/24;
					if (date("j M y", $dDate) == date("j M y", owaesTime()+(24*60*60))){ // morgen
						return "morgen ($iDag $strMaand) om $strTijd";
					} else if ($iDagen < 6) { // max. 6 dagen 
						return "$strDagFull om $strTijd";
					} if ($iDagen < 60) { // max. 60 dagen 
						return "$strDagKort $iDag $strMaand om $strTijd";
					} else return "$strDagKort $iDag $strMaand $strJaar"; 
				}
				break; 
			default: 
				return date($strFormat, $dDate);	
		} 
		 
	}
	 
	function hhmmTOminutes($strTijd) {
		$arTime = preg_split("/[^0-9]+/", $strTijd);
		$iTime = 0;   
		switch(count($arTime)) { 
			case 1:  
				$iTime = intval($arTime[0])*60;
				break; 	
			case 2: 
				$iTime = (intval($arTime[0])*60) + intval($arTime[1]);
				break; 
		}  
		return $iTime; 
	}
	
	function minutesTOhhmm($iTijd) { 
		if ($iTijd > 0) {
			$iUren = floor($iTijd/60);  
			return ($iUren%24) . "u" . substr("00" . ($iTijd-($iUren*60)), -2); 
		} else return ""; 
		return $iTijd; 
	}
	
	function ddmmyyyyTOdate($strDate) { 
		$arDate = preg_split("/\D+/", $strDate);
		$iDate = 0;   
		$arDate2 = array(); // om d'een of d'andere reden wordt een gewone string opgesplitst in Array(2) {"", ""}
		foreach($arDate as $iVal) {
			if (is_numeric($iVal)) $arDate2[] = $iVal; 	
		}
		$arDate = $arDate2;  
		switch(count($arDate)) {
			case 2: 
				$iDate = mktime(0, 0, 0, intval($arDate[1]), intval($arDate[0]), date("Y")); 
				break; 	
			case 3: 
				$iDate = mktime(0, 0, 0, intval($arDate[1]), intval($arDate[0]), intval($arDate[2]));  
				break; 		
			case 4: 
				$iDate = mktime(intval($arDate[3]), 0, 0, intval($arDate[1]), intval($arDate[0]), intval($arDate[2])); 
				break; 		
			case 5: 	
			case 6: 
				$iDate = mktime(intval($arDate[3]), intval($arDate[4]), 0, intval($arDate[1]), intval($arDate[0]), intval($arDate[2]));  
				break; 	
		}  
 
		return $iDate; 
	}
	
	function filename($bQuery = TRUE) { // returns filename of current script ($bQuerye: enkel filename of ook met querystring?)
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
	
	function randomstring($iLength = 20, $strChars = "azertyupsdfghjkmwxcvbnAZERTYUPQSDFGHJKLMWXCVBN23456789") {
		$strResult = ""; 
		for ($i=0; $i<$iLength; $i++){
			$strResult .= substr($strChars, rand(0, strlen($strChars)), 1); 
		}
		return $strResult; 
	}
    
    function console($origin, $data) {
        if (is_array($data)){
            $output = "<script>console.log('PHP: [" . $origin . "] " . implode(',', $data) . "');</script>";
        } else {
            $output = "<script>console.log('PHP: [" . $origin . "] " . $data . "');</script>";
        }   
        echo $output;
    }
	
	function html($strTxt) {
		return htmlspecialchars($strTxt); 
	}
	
	function javascriptSafe($strTxt) {
		$strTxt = str_replace("'", "&acute;", $strTxt); 
		$strTxt = str_replace('"', "&quot;", $strTxt); 
		return $strTxt; 
	}
	
?>