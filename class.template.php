<?php 
	function template($strContent) {
		return new template($strContent); 
	}
	
	class template { /*
		templates => template("bestand.html") of template("<div>whatever</div>") 
	*/
		private $strHTML = NULL;  
		private $strFolder = NULL; 
		private $arTags = array(); 
		private $arLoops = array(); 
	
		public function template($strContent) { 
			$strFolder = settings("domain", "templatefolder"); 
			$this->strFolder = $strFolder; 
			$this->html( is_file($strFolder.$strContent) ? content($strFolder.$strContent) : $strContent ); 
		}
		
		public function tags($strHTML = NULL) { // returns alle tags die zich in de template bevinden
			if (is_null($strHTML)) $strHTML = $this->html(NULL, FALSE); 
			$arTags = array(); 
			preg_match_all("/\[([^\]\/]+?)\]/", $strHTML, $arResult); 
			if (isset($arResult[1])) foreach ($arResult[1] as $strTag){ 
				$arTagSplit = explode(":", $strTag);  
				switch ($arTagSplit[0])  {
					case "if": 
						$strTag = implode(":", array_slice($arTagSplit,1));  
						break; 	
					case "file": 
						if (count($arTagSplit)==2) $strTag = NULL; 
						break; 
				}
				if (!is_null($strTag)) $arTags[$strTag] = $strTag; 
			}  
			return array_keys($arTags); 
		}
		
		public function loops($strHTML = NULL) { // returns alle tags waarvoor array nodig is die zich in de template bevinden (bv. [for:tag]xx[/for:tag] of [tag:count]
			if (is_null($strHTML)) $strHTML = $this->html(NULL, FALSE); 
			$arLoops = array();  
			preg_match_all("/\[for:([^\]]+?)\]([\s\S]*?)\[\/for:\\1\]/", $strHTML, $arResult);  
			$iHTMLlus = 0; 
			if (isset($arResult[1])) foreach ($arResult[1] as $strTag){ 
				$arTagSplit = explode(":", $strTag);  
				switch(count($arTagSplit)) {
					case 2: 
						if (is_numeric($arTagSplit[1])) $strTag = $arTagSplit[0]; 
						break; 	
					default: 
				}
				if (!isset($arLoops[$strTag])) $arLoops[$strTag] = array();
				$arLoops[$strTag][] = $arResult[2][$iHTMLlus];  
				$iHTMLlus ++; 
			} 
			
			preg_match_all("/\[if:([^\]\/]+?)(>([0-9]+){0,1})\]([\s\S]*?)\[\/if:\\1\\2\]/", $strHTML, $arResult);  
			if (isset($arResult[1])) foreach ($arResult[1] as $strTag){  
				if (!isset($arLoops[$strTag])) $arLoops[$strTag] = array();
				$arLoops[$strTag][] = "";  
			}   
			
			preg_match_all("/\[([^\]\/]+?):count\]/", $strHTML, $arResult); 
			if (isset($arResult[1])) foreach ($arResult[1] as $strTag){  
				if (!isset($arLoops[$strTag])) $arLoops[$strTag] = array();
				$arLoops[$strTag][] = "";  
			}  
			return $arLoops; 
		} 
		
		public function setLoop($strTag, $strVar2 = NULL, $strVar3 = NULL) { /*
			->setLoop("tag", array(1,2,3));  (voor [tag:count]) 
			of ->setLoop("tag", "<i>[r]</i>", array("<i>1</i>","<i>2</i>","<i>3</i>");  (voor [for:tag]<i>[r]</i>[/for:tag])  
			*/ 
			$arLoops = is_null($strVar3) ? $strVar2 : $strVar3; 
			$strHTML = is_null($strVar3) ? "" : $strVar2; 
			if (!isset($this->arLoops[$strTag])) $this->arLoops[$strTag] = array(); 
			$this->arLoops[$strTag][$strHTML] = $arLoops; 
		}
		
		public function tag($strTag, $strValue = NULL) { // get/set een replace-tag
			if (!is_null($strValue)) $this->arTags[$strTag] = $strValue; 
			return $this->arTags[$strTag]; 
		}
		 
		private function includes($strHTML) { /* [file:userprofile.friend.html]  */
			$xReg = "/\[file:([\s\S]*?)\]/"; 
			while (preg_match($xReg, $strHTML)) {
				preg_match_all($xReg, $strHTML, $arResult);  
				for ($i=0;$i<count($arResult[0]);$i++) {   
					$strHTML = str_replace($arResult[0][$i], content($this->strFolder . $arResult[1][$i]), $strHTML);
				} 
			}
			return $strHTML; 
		}
		
		public function specialHTMLtags($strHTML = NULL) { /* [htmlencode]huppeldepup<script>alert("test"); </script>[/htmlencode]  */
			if (is_null($strHTML)) $strHTML = $this->html(); 
			
			preg_match_all("/\[htmlencode\]([\s\S]*?)\[\/htmlencode\]/", $strHTML, $arResult); 
			for ($i=0;$i<count($arResult[0]);$i++) {  
				$strHTML = str_replace($arResult[0][$i], htmlspecialchars($arResult[1][$i]), $strHTML);
			} 
			
			preg_match_all("/\[showurls\]([\s\S]*?)\[\/showurls\]/", $strHTML, $arResult); 
			for ($i=0;$i<count($arResult[0]);$i++) {  
				$strSubHTML = $arResult[1][$i]; 
				$strSubHTML = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$strSubHTML); /*** make sure there is an http:// on all URLs ***/ 
	//			$strSubHTML = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</a>",$strSubHTML); /*** make all URLs links ***/
				$strSubHTML = preg_replace("/(([\w]+:\/\/)([\w-?&;#~=\.\/\@]+[\w\/]))/i","<a target=\"_blank\" href=\"$1\">$3</a>",$strSubHTML); /*** make all URLs links ***/
				$strSubHTML = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<a href=\"mailto:$1\">$1</a>",$strSubHTML);
	
				$strHTML = str_replace($arResult[0][$i], $strSubHTML, $strHTML); 
			} 
			
			$strHTML = str_replace("[currentfile]", filename(TRUE), $strHTML); 
	
			return $strHTML; 
		}
		
		public function queryTags($strHTML = NULL) { /* [htmlencode]huppeldepup<script>alert("test"); </script>[/htmlencode]  */
			if (is_null($strHTML)) $strHTML = $this->html(); 
			
			preg_match_all("/\[qry:([a-zA-Z0-9-_]+)\]/", $strHTML, $arResult);   // bv. [qry:querystring]  
			for ($i=0;$i<count($arResult[1]);$i++) { 
				$strQRY = $arResult[1][$i]; 
				$strHTML = str_replace($arResult[0][$i], (isset($_GET[$strQRY]) ? $_GET[$strQRY] : ""), $strHTML); 
			} 
			
			return $strHTML; 
		}
		
		public function fixedTerms($strHTML) {
			$arFixed = array(
				"credit" => settings("credits", "name", "1"), 
				"credits" => settings("credits", "name", "x"), 
				"Credit" => ucfirst(settings("credits", "name", "1")),  
				"Credits" => ucfirst(settings("credits", "name", "x")),  
				"Creditoverdracht" => ucfirst(settings("credits", "name", "overdracht")),  
			); 
			preg_match_all("/\[\[([\s\S]*?)\]\]/", $strHTML, $arResult);   
			if (isset($arResult[1])) foreach ($arResult[1] as $strTerm){
				if (isset($arFixed[$strTerm])) {
					$strHTML = str_replace("[[$strTerm]]", $arFixed[$strTerm], $strHTML); 
				} else {
					$strHTML = str_replace("[[$strTerm]]", $strTerm, $strHTML);  
				}
			}  
			return $strHTML; 	
		}

		public function html($strHTML = NULL, $bXtraFunctions = TRUE) {
			if (!is_null($strHTML)) {
				$this->strHTML = $strHTML; 
			}
			$strHTML = $this->strHTML; 
			$strHTML = $this->includes($strHTML);
			$strHTML = $this->fixedTerms($strHTML);
			foreach ($this->arLoops as $strTag=>$arLoops) {
				$strHTML = str_replace("[$strTag:count]", count($arLoops[key($arLoops)]), $strHTML);   // [tag:count$
				
				preg_match_all("/\[if:$strTag\]([\s\S]*?)\[\/if:$strTag\]/", $strHTML, $arResult);   // bv. [if:friends]minimum 1 friend[/if:friends]  
				for ($i=0;$i<count($arResult[0]);$i++) { 
					$strHTML = str_replace($arResult[0][$i], (count($arLoops[key($arLoops)])>0) ? $arResult[1][$i] : "", $strHTML); 
				} 
				preg_match_all("/\[if:$strTag(>([0-9]+){0,1})\]([\s\S]*?)\[\/if:$strTag\\1\]/", $strHTML, $arResult);   // bv. [if:friends>3]minimum 4 friends[/if:friends>3]   
				for ($i=0;$i<count($arResult[0]);$i++) { 
					$iCount = $arResult[2][$i];
					$strHTML = str_replace($arResult[0][$i], (count($arLoops[key($arLoops)])>$iCount) ? $arResult[3][$i] : "", $strHTML); 
				} 
				
				preg_match_all("/\[for:$strTag((?::([0-9]+)){0,1})\]([\s\S]*?)\[\/for:$strTag\\1\]/", $strHTML, $arResult);   // bv. [for:friends][name][/for:friends]  of [for:friends:3][name][/for:friends:3] 
				for ($i=0;$i<count($arResult[0]);$i++) { 
					if (isset($arLoops[$arResult[3][$i]])) {
						$iCount = ($arResult[2][$i]=="") ? count($arLoops[$arResult[3][$i]]) : intval($arResult[2][$i]); 
						$strHTML = str_replace($arResult[0][$i], implode("", array_slice($arLoops[$arResult[3][$i]], 0, $iCount)), $strHTML); 
					} else {
						// strHTML is ondertussen gewijzigd :-/
					}
				}  
			} 
			
			foreach ($this->arTags as $strTag=>$strReplace) { 
				preg_match_all("/\[if:$strTag\]([\s\S]*?)(\[else:$strTag\]([\s\S]*?))?\[\/if:$strTag\]/", $strHTML, $arResult);   // bv. [if:firstname]firstname ingevuld en zichtbaar[/if:firstname]   
				for ($i=0;$i<count($arResult[0]);$i++) { 
					switch($strReplace) {
						case "": 
						case NULL: 
						case FALSE: 
							$strHTML = str_replace($arResult[0][$i], $arResult[3][$i], $strHTML); 	
							break; 
						default: 
							$strHTML = str_replace($arResult[0][$i], $arResult[1][$i], $strHTML); 	
					}
				} 
				
				$strHTML = str_replace("[$strTag]", $strReplace, $strHTML); 
			}
			
			if ($bXtraFunctions) {
				$strHTML = $this->specialHTMLtags($strHTML); 
				$strHTML = $this->queryTags($strHTML); 
			}
			return $strHTML; 
		}
		
		public function __toString() {
			return $this->html();
		}
	}
	
	/*

 			preg_match_all("/\[if:([a-zA-Z0-9-_:#]+)\]([\s\S]*?)\[\/if:\\1\]/", $strHTML, $arResult);   // bv. [if:firstname]firstname ingevuld en zichtbaar[/if:firstname]  
			for ($i=0;$i<count($arResult[0]);$i++) {
				$strResult = $this->HTMLvalue($arResult[1][$i]);  
				if (!is_null($strResult)) $strHTML = str_replace($arResult[0][$i], (($strResult == "") ? "" : $arResult[2][$i]), $strHTML); 	
			} 
	*/
  