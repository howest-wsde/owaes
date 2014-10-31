<?php 
	class action {
		private $strHREF = NULL; 
		private $arClasses = array(); 
		private $iCreated = NULL; 
		private $iTodoDate = NULL; 
		private $strType = NULL; 
		private $arData = NULL; 
		
		public function link($strHREF = NULL) { 
			if (!is_null($strHREF)) $this->href($strHREF);  
		}
		
		public function href($strHREF = NULL) { 
			if (!is_null($strHREF)) $this->strHREF = $strHREF; 
			return $this->strHREF;
		}
		
		public function addClass($strClass) {
			if (!in_array($strClass, $this->arClasses)) $this->arClasses[] = $strClass; 
		}
		 
		public function __toString() {
			return $this->html();
		} 
	}
