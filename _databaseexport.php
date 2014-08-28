<?
	include "inc.default.php"; // should be included in EVERY file 
?><html>
	<head>
    	<title>OWAES database tables</title>
    	<style>
			body {font-family: Tahoma, Geneva, sans-serif; }
			dl {display: inline-block; border: 1px solid black; margin: 10px; vertical-align: top;  }
			dt {border-bottom: 1px solid black; background: #333; color: white; padding: 3px 8px; margin: 0; }
			dd {padding: 3px 8px; margin: 0; padding-left: 30px; }
			dd.PRI {background: url("img/key.gif") no-repeat 7px center;  }
			dd small {color: gray; text-align: right; font-weight: normal; }
			@media print {
				dd.PRI {font-weight: bold; background: none;  }
				dd {padding-left: 0; }
			}
		</style>
    </head>
    <body>
    	<textarea style="width: 100%; height: 100%; border: 0; " ><?
			$arFullExport = array("tblBadges", "tblCategories", "tblCertificates", "tblMarketTags", "tblMarketTypes"); 
		
			$oDB = new database(); 
			
			$oSub = new database(); 
			$oDB->execute("show tables"); 
			while ($oDB->nextRecord()){
				$strTable = $oDB->get(0);
				if (substr($strTable, 0, 1) != "_") {
					
					$oSub->execute("SHOW CREATE TABLE $strTable;"); 
					echo "\n" . $oSub->get(1) . "; \n"; 
					
					if (in_array($strTable, $arFullExport)) {
						$oFields = new database("select * from $strTable;", TRUE);  
						while ($oFields->nextRecord()){
							$arValues = array(); 
							foreach ($oFields->fields() as $strField) {
								$arValues[] = "'" . $oFields->get($strField) . "'"; 
							}
							echo "\nINSERT INTO " . $strTable . " VALUES(" . implode(", ", $arValues) . "); ";
							 
						}	 
					} 
					echo "\n"; 
				}
			}
			
		?></textarea>
	</body>
</html>