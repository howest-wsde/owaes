<?php
 
	$ar_GLOBAL_categorien = array(); 
	function categorie($iID) { // FUNCTION user(5) == CLASS new user(5) 
		global $ar_GLOBAL_categorien;  
		if (isset($ar_GLOBAL_categorien[$iID])) return $ar_GLOBAL_categorien[$iID]; 
		$oCat = new categorie($iID); 
		$ar_GLOBAL_categorien[$iID] = $oCat; 
		return $oCat; 	 
	}
	
	class categorie { 
		private $iID = NULL; 
		private $strTitel = NULL; 
		private $strIcon = NULL;  
		 
		public function categorie($iID) { // $strKey = ID or ALIAS // when not defined: create new user   
			$oDB = new database("select * from tblCategories where id = $iID; ", TRUE);
			if ($oDB->record()) {
				$this->iID = $iID; 
				$this->strTitel = $oDB->get("titel");
				$this->strIcon = $oDB->get("icon");
			} else {
				$this->iID = 0; 
				$this->strTitel = "";
				$this->strIcon = "";	
			} 
		}
		
		public function titel() {
			return $this->strTitel; 
		}
		public function icon() {
			return fixPath("img/categorien/" . $this->strIcon); 
		}
		 

	}
	
?>