<style>
* {margin: 0px; padding: 0; font-family: Tahoma, Geneva, sans-serif; font-size: 14px;  }
body {margin: 20px; }
hr {margin: 20px 0; }
div.file {font-size: 12px; margin-top: 20px;}
div.class { }
div.function {margin-left: 20px; margin-bottom: 15px; margin-top: 5px; }
div.rem {margin-left: 18px; font-style: italic; }
.class .rem {border: 1px solid gray; margin: 10px 0; padding: 10px; }
h2 {font-size: 16px; }
h3 {font-size: 15px; }
</style><?
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	include "inc.functions.php"; 
	
	$strClass = ""; 

	if ($handle = opendir('.')) { 
		while (false !== ($strFile = readdir($handle))) {
			if (!is_dir($strFile) && (substr($strFile, 0, 1) != "_")) {
				$strHTML = content($strFile); 
				$arLines = explode("\n", $strHTML); 
				for ($iLine = 0; $iLine < count($arLines); $iLine++) {
					$strLine = $arLines[$iLine]; 
					// foreach ($arLines as $strLine) {  
					$arWords = preg_split("/[^a-zA-Z0-9\/\{=,$*]+/", trim(strtolower($strLine)));
					$arUWords = preg_split("/[^a-zA-Z0-9\/\{=,$\*]+/", trim($strLine));
					if ($arWords[0] == "class") {
						echo "<div class=\"file\">file: $strFile</div>"; 
						echo "<div class=\"class\">"; 
						$strClass = $arWords[1]; 
				 		echo "<h2>Class " . $strClass . "</h2>"; 
						if (in_array("//", $arWords)) {  
							echo "<div class=\"rem\">";
							echo html(substr($strLine, (strrpos($strLine, "//")+2))) . "<br />"; 
							echo "</div>"; 
						}
						if (in_array("/*", $arWords)) { 
							echo "<div class=\"rem\">";
							$iSublines = 0;
							do {
								$strSubLine = $arLines[$iLine + ($iSublines++)]; 
								$i1 = strrpos($strSubLine, "/*") ? (strrpos($strSubLine, "/*")+2) : 0; 
								$i2 = strrpos($strSubLine, "*/") ? (strrpos($strSubLine, "*/") - $i1) : 9999;  
								$strShow = substr($strSubLine, $i1, $i2); 
								if (trim($strShow) != "") echo html($strShow) . "<br />"; 
							} while (strrpos($strSubLine, "*/") === false); 
							echo "</div>"; 
						}
						echo "</div>"; 
					}
					if (($arWords[0] == "public")&&($arWords[1] == "function")) {
						echo "<div class=\"function\">";   
						$strArgs = ""; 
						if (in_array("{", $arWords)) { 
							$arArgs = $arUWords; 
							array_splice($arArgs, 0, 3);
							array_splice($arArgs, array_search("{", $arArgs));
							$strArgs = implode(" ", $arArgs); 
						} 
						$strFunction = $arUWords[2];
						if ($strClass == $strFunction) {
					 		echo "<h3>new " . $strFunction .  " (" . $strArgs . ")</h3>"; 
						} else {
					 		echo "<h3>-&gt;" . $strFunction .  " (" . $strArgs . ")</h3>"; 
						}
						if (in_array("//", $arWords)) {  
							echo "<div class=\"rem\">";
							echo html(substr($strLine, (strrpos($strLine, "//")+2))) . "<br />"; 
							echo "</div>"; 
						}
						if (in_array("/*", $arWords)) { 
							echo "<div class=\"rem\">";
							$iSublines = 0;
							do {
								$strSubLine = $arLines[$iLine + ($iSublines++)]; 
								$i1 = strrpos($strSubLine, "/*") ? (strrpos($strSubLine, "/*")+2) : 0; 
								$i2 = strrpos($strSubLine, "*/") ? (strrpos($strSubLine, "*/") - $i1) : 9999;  
								$strShow = substr($strSubLine, $i1, $i2); 
								if (trim($strShow) != "") echo html($strShow) . "<br />"; 
							} while (strrpos($strSubLine, "*/") === false); 
							echo "</div>"; 
						}

						echo "</div>"; 
					}
				} 
			}
		} 
		closedir($handle);
	}
	 
?>