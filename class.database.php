<?php  
	try {
		$dbPDO = new PDO("mysql:host=" . settings("database", "host") . ";dbname=" . settings("database", "name"), settings("database", "user"), settings("database", "password"));
		$arConfig["database"]["loaded"] = TRUE; 
	} catch( PDOException $Exception ) {
		$arConfig["database"]["loaded"] = FALSE; 
	}  

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
		
		public function active() {
			global $dbPDO; 
			var_dump($dbPDO); 
			return FALSE; 
		}
		
		public function sql($strSQL = NULL) { // sets of gets de SQL-query
			if (!is_null($strSQL)) $this->strSQL = $strSQL; 
			return $this->strSQL; 
		}
		 
		
		public function execute($strSQL = NULL){ /* executes DB-query, returns number of records (length)
			optional parameter $strSQL: eerst SQL-aanpassen, anders wordt deze gebruikt die geset werd met sql("..")
		*/
			global $dbPDO; 
			$this->iRecord = -1;  
			if (!is_null($strSQL)) $this->sql($strSQL);
			$strSQL = $this->sql(); 
			//echo "[$strSQL]<br>"; 
			$iStartQuery = time();  
			$arFieldNames = array(); 
			$oResult = NULL; 
			try {  
				$this->iQueryTime = time() - $iStartQuery; 
				$this->arResult = array();  
				$this->iInsertedID = 0; 
				$this->iLength = 0; 
				
				$arSQL = explode(" ", $strSQL); 
				switch(strtolower($arSQL[0])) {
					case "select":   
						foreach($dbPDO->query($strSQL) as $oRow) {
							$arRow = array(); 
							if (count($arFieldNames)==0) {
								foreach ($oRow as $strCol=>$strVal) {
									if (!is_numeric($strCol)) $arFieldNames[] = $strCol; 
								}
								$this->arFieldNames = $arFieldNames; 
							}
							foreach ($oRow as $strCol=>$strVal) {
								$arRow[$strCol] = $strVal; 
							}
							$this->arResult[] = $arRow; 
						}
						$this->iLength = count($this->arResult); 
						break; 
					case "insert": 
						$oResult = $dbPDO->exec($strSQL);  
						$this->iInsertedID = $dbPDO->lastInsertId();   
						break; 
					case "delete": 
					case "update": 
					case "create": 
						$oResult = $dbPDO->exec($strSQL);  
						break; 
					default: 
						echo ('<div style="border: 2px solid red; padding: 10px; margin: 10px 0; "><div style="color: red; font-weight: bold; ">DB command? : </div><div style="margin: 10px 0; color: gray; font-style: italic; ">' . $this->sql() . '</div><div>Niet uitgevoerd (class.database, lijn ' . __LINE__ . '</div></div>');  
				} 
				 
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
			} catch(PDOException $ex) {
				echo ('<div style="border: 2px solid red; padding: 10px; margin: 10px 0; "><div style="color: red; font-weight: bold; ">Error: </div><div style="margin: 10px 0; color: gray; font-style: italic; ">' . $this->sql() . '</div><div>' . $ex->getMessage() . '</div></div>');  
				return FALSE; 
			}
		} 
		
		public function table($bShowSQL = FALSE) { // voor debugging-purposes, returns DB-result als <table>
			if (is_null($this->arFieldNames)) {
				$strResult = "Database: geen results " . ($bShowSQL?" (SQL: " . $this->sql() . ")":""); 
			} else {
				$strResult = "<style>table.databaseview {border-collapse; background: white;  } table.databaseview td, table.databaseview th {border: 1px solid black; padding: 3px; } table.databaseview th {font-weight: bold; }</style><table class=\"databaseview\">"; 
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
			if (is_null($arRecord[$strID])) return NULL; 
			return (isset($arRecord[$strID])) ? $arRecord[$strID] : NULL; 
		} 
		
		public function escape($strTekst, $bQuotes = FALSE) { // mysql_real_escape_string (bquotes sets ' around)
			//echo ($strTekst); 
			//echo "<br />" . mysql_real_escape_string($strTekst); 	 
			// global $dbPDO; 
			if ($bQuotes) {
				if (is_null($strTekst)) return "NULL"; 
			}
			$strTekst = str_replace('"', "\\\"", $strTekst);
			$strTekst = str_replace("'", "\\'", $strTekst);
			
			return ($bQuotes) ? "'$strTekst'" : $strTekst;  // $dbPDO->quote($strTekst); // mysql_real_escape_string($strTekst); 	
		}
		
		public function fields() {
			return $this->arFieldNames; 
		}
		 
	  
	}
	 
	
	