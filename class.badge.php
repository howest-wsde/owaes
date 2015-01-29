<?php  
	class badge { 
		private $strbadge = NULL; 
		private $arData = array(); 
		
		public function badge($strBadge = NULL) { 
			if (!is_null($strBadge)) $this->load($strBadge); 
			//$this->user( (is_null($iUser)) ? me() : $iUser ); 
		}
		
		public function load($strBadge = NULL) { 
			if (!is_null($strBadge)) $this->strBadge = $strBadge; 
			if (!is_null($this->strBadge)) {
				$oDB = new database(); 
				$oDB->execute("select * from tblBadges where mkey = '" . $oDB->escape($this->strBadge) . "'; "); 
				if ($oDB->length() == 1) {
					$this->arData = array(
						"img" => $oDB->get("img"), 
						"title" => $oDB->get("title"),
						"info" => $oDB->get("info"),  
					); 
				} 
			} 
		}
		
		
		private function HTMLvalue($strTag) {
			switch($strTag) { 
				case "key": 
					return $this->strBadge; 
				case "img": 
					return fixPath("img/badges/" . $this->arData["img"]); 
				case "title": 
				case "info": 
					return html($this->arData[$strTag]);  
				default: 
					$arTag = explode(":", $strTag, 2); 
					switch($arTag[0]) {
						case "badge": 
							return $this->htmlValue($arTag[1]); 
							break;  
					} 
					return NULL; 
			}
		}
		
		public function HTML($strTemplate = "") { // vraagt pad van template (of HTML if bFile==FALSE) en returns de html met replaced [tags] 
			$oHTML = template($strTemplate);  

			foreach ($oHTML->tags() as $strTag) {
				$strResult = $this->HTMLvalue($strTag);  
				if (!is_null($strResult)) $oHTML->tag($strTag, $strResult); 
			}

			return $oHTML;   
		} 
		
		
		 
	} 
	 
	