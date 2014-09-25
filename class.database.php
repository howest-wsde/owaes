<?php 

	mysql_connect($arConfig["database"]["host"], $arConfig["database"]["user"], $arConfig["database"]["password"]);
	mysql_select_db($arConfig["database"]["name"]);
	
	$ar_GLOBAL_queries = array(); 

	class database { // wordt gebruikt om SQL-queries uit te voeren, enkel gebruiken vanuit andere classes, niet in 'gewone' php-pages 
			
		private $strSQL = ""; 
		private $iQueryTime = 0;  
		private $iLength = 0; 
		private $oRecord = 0;  
		private $arResult; 
		private $iRecord = 0; 
		private $iInsertedID = 0; 
		private $arFieldNames = NULL;  

		public function database($strSQL = NULL, $bExecute = false) {   /*
			optional parameters: $strSQL : Query direct setten (sql("..") ); 
			$bExecute: query onmiddelijk (execute(); )
		*/
			if (!is_null($strSQL)) {
				$this->sql($strSQL); 
				if ($bExecute) $this->execute(); 
			} 
		}
		
		public function sql($strSQL = NULL) { // sets of gets de SQL-query
			if (!is_null($strSQL)) $this->strSQL = $strSQL; 
			return $this->strSQL; 
		}
		
		public function execute($strSQL = NULL){ /* executes DB-query, returns number of records (length)
			optional parameter $strSQL: eerst SQL-aanpassen, anders wordt deze gebruikt die geset werd met sql("..")
		*/
		
			$this->iRecord = -1;  
			if (!is_null($strSQL)) $this->sql($strSQL);
			$strSQL = $this->sql(); 
			//echo "[$strSQL]<br>"; 
			$iStartQuery = time();  
			$arFieldNames = array(); 
			$oResult = NULL; 
			$oResult = mysql_query($strSQL) or die ('<div style="border: 2px solid red; padding: 10px; margin: 10px 0; "><div style="color: red; font-weight: bold; ">Error: </div><div style="margin: 10px 0; color: gray; font-style: italic; ">' . $this->sql() . '</div><div>' . mysql_error () . '</div></div>');
			$this->iQueryTime = time() - $iStartQuery; 
			$this->arResult = array(); 
			if (!is_resource($oResult)){
				$this->iLength = 0; 
				$this->iInsertedID = mysql_insert_id();  
			} else {  
				$this->iLength = @mysql_num_rows($oResult);
				if ($this->iLength > 0) {
					for ($i=0; $i < mysql_num_fields($oResult); $i++) $this->arFieldNames[] = mysql_fetch_field($oResult, $i)->name;  
					while($r = mysql_fetch_array($oResult,MYSQL_BOTH)) $this->arResult[] = $r;
				} else {
					$this->iInsertedID = mysql_insert_id();  
				}
			} 
			// echo "<div class=\"ADMIN SQL\">SQL: <code>" . $strSQL . "</code></div>"; 
//			if ($this->getTime() >= 1) {
//				vardump($this->sql() . " : " . $this->getTime());  
//			}
			if ($this->getTime() > 2) { 
				$oLog = new log("trage query", array(
					"url" => filename(), 
					"sql" => $this->sql(), 
					"tijd" => $this->getTime(), 
				)); 
			}
			
			/* START LOG DB */
			$strDBlog = "cache/dbqueries.json";
			$arQueries = json($strDBlog); 
			if (isset($arQueries)) foreach ($arQueries as $strKey=>$arQRY) {
				if ($arQRY["date"] < time()-60*60*3) unset ($arQueries[$strKey]); 
			}
			while (count($arQueries)>500) $xDel = array_shift($arQueries);
			global $ar_GLOBAL_queries; 
			if (!isset($ar_GLOBAL_queries[$this->sql()])) $ar_GLOBAL_queries[$this->sql()]=0; 
			$arQueries[time() . "." . rand(0,9999)] = array(
				"date" => time(), 
				"url" => filename(), 
				"sql" => $this->sql(), 
				"tijd" => $this->getTime(), 
				"ip" => $_SERVER['REMOTE_ADDR'],
				"user" => me(),
				"sessie" => session_id(), 
				"count" => ++$ar_GLOBAL_queries[$this->sql()], 
			); 
			json($strDBlog, $arQueries); 
			/* END LOG DB */
			
			return $this->iLength;
		} 
		
		public function table($bShowSQL = FALSE) { // voor debugging-purposes, returns DB-result als <table>
			if (is_null($this->arFieldNames)) {
				$strResult = "Database: geen results " . ($bShowSQL?" (SQL: " . $this->sql() . ")":""); 
			} else {
				$strResult = "<table class=\"database\">"; 
				if ($bShowSQL) $strResult .= "<tr><th colspan=\"" . count($this->arFieldNames) . "\">" . $this->sql() . "</th></tr>"; 
				$strResult .= "<tr>";   
				foreach ($this->arFieldNames as $strField) {
					$strResult .= "<th>$strField</th>"; 
				}
				$strResult .= "</tr>";  
				foreach($this->arResult as $oResult) {
					$strResult .= "<tr>"; 
					foreach ($this->arFieldNames as $strField) { 
						$strResult .= "<td>" . ((strlen($oResult[$strField]) > 30) ? (substr($oResult[$strField], 0, 27)." ...") : $oResult[$strField]) . "</td>"; 
					}
					$strResult .= "</tr>"; 
				}
				$strResult .= "</table>"; 
			}
			return $strResult; 
		}
		
		public function lastInsertID() { // na insert kun je hier de ID opvragen
			return $this->iInsertedID; 
		}
		
		public function getTime() { // returns de tijd die nodig was de query uit te voeren (sec.) 
			return $this->iQueryTime; 
		}
		
		public function length() { // returns aantal records
			return $this->iLength; 
		}
		
		public function nextRecord() {  // sets pointer to next record, returns TRUE if ok, FALSE if no further records
			$this->iRecord ++; 
			return ($this->iRecord < $this->iLength); 
		}
		
		public function records(){ // returns all records als Array
			return $this->arResult; 
		}
		
		public function record() { // returns current record (returns FIRST record if function "nextRecord" hasn't been used OR has been used 1 time) / returns FALSE if no records
			$iRecord = $this->iRecord; 
			if ($iRecord < 0)$iRecord = 0; 
			
			return (count($this->arResult)>0) ? $this->arResult[$iRecord] : FALSE; 
		}
		
		public function get($strID) { // used to get record values (bv. $iID = $oDB->get("id"); )
			$arRecord = $this->record(); 
			return $arRecord[$strID];
		} 
		
		public function escape($strTekst) { // mysql_real_escape_string
			//echo ($strTekst); 
			//echo "<br />" . mysql_real_escape_string($strTekst); 	
			return mysql_real_escape_string($strTekst); 	
		}
		
		public function fields() {
			return $this->arFieldNames; 
		}
	  
	}
	 
	
?>