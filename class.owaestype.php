<?php
	define ("DIRECTION_SPEND", 0); 
	define ("DIRECTION_EARN", 1); 
	
	function owaestype($vKey = NULL) { // FUNCTION owaestype(5) == CLASS new owaestype(5)  
		return new owaestype($vKey); 
	}
 
	class owaestype {  
		private $arTypes; 
		private $iType = 0; 
		public function owaestype($vKey = NULL) { 
			$this->arTypes = array(
				1 => array(
					"key" => "ervaring", 
					"title" => "Werkervaring", 
					"iconclass" => "icon-werkervaring", 
					"direction" => DIRECTION_SPEND, 
					"minimumlevel" => 4, 
				), 
				2 => array(
					"key" => "opleiding", 
					"title" => "Opleiding", 
					"iconclass" => "icon-opleiding", 
					"direction" => DIRECTION_EARN,
					"minimumlevel" => 3, 
				), 
				3 => array(
					"key" => "infra", 
					"title" => "Delen",  
					"iconclass" => "icon-delen", 
					"direction" => DIRECTION_EARN,
					"minimumlevel" => 2, 
				), 
			); 	
			foreach ($this->arTypes as $iKey=>$arType) {
				if ($vKey == $iKey) $this->iType = $iKey; 
				if ($vKey == $arType["key"]) $this->iType = $iKey;  
			}  
		}
		
		public function getAllTypes() {
			$arTypes = array(); 
			foreach ($this->arTypes as $iID=>$arType) $arTypes[$arType["key"]] = $arType["title"];
			return $arTypes;
		}
		
		public function task() {
			return ($this->arTypes[$this->iType]["direction"] == DIRECTION_SPEND); 
		}
		public function key() {
			return $this->arTypes[$this->iType]["key"]; 
		}
		public function title() {
			return $this->arTypes[$this->iType]["title"]; 
		}
		public function minimumlevel() {
			return $this->arTypes[$this->iType]["minimumlevel"]; 
		}
		
		public function __toString() {
			return $this->arTypes[$this->iType]["title"]; 
		}
		
		public function id() {
			return $this->iType; 
		}
		public function direction() {
			return $this->arTypes[$this->iType]["direction"]; 
		}
		public function iconclass() {
			return $this->arTypes[$this->iType]["iconclass"]; 
		}
	}
	
