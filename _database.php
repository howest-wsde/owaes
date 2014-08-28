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
    	<?
			$oDB = new database(); 
			$oDB->execute("show tables"); 
			while ($oDB->nextRecord()){
				$strTable = $oDB->get(0);
				if (substr($strTable, 0, 1) != "_") {
					echo "<dl>";
					echo "<dt>" . $strTable . "</dt>";
					$oSub = new database(); 
					$oSub->execute("SHOW COLUMNS FROM $strTable;");
					while ($oSub->nextRecord()){
						$arClasses = array(); 
						if ($oSub->get(3) != "") $arClasses[] = $oSub->get(3); 
						echo "<dd class=\"" . implode(" ", $arClasses) . "\">" . $oSub->get(0) . " <small>" . $oSub->get(1) . "</small></dd>";
					}
					echo "</dl>";
				}
			}
			
		?>
       <p> <a href="_databaseexport.php">Get database SQL</a></p>
	</body>
</html>