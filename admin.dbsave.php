<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	if (!$oSecurity->admin()) stop("admin"); 
	
	
	$oDB = new database(); 

	$arFields = array(); 
	foreach ($_POST as $strKey=>$strVal) {
		$arKey = explode("_", $strKey); 
		if (count($arKey)==3){
			echo ("$(\"#" . $strKey . "\").removeClass(\"saving\").removeClass(\"changed\").attr(\"orig\", $(\"#" . $strKey . "\").find(\":input\").val()); $(\"#" . $strKey . "\").find(\":input\").remove();\n"); 
			if (!isset($arFields[$arKey[0]])) $arFields[$arKey[0]] = array(); 
			if (!isset($arFields[$arKey[0]][$arKey[1]])) $arFields[$arKey[0]][$arKey[1]] = array(); 
			$arFields[$arKey[0]][$arKey[1]][] = $arKey[2] . " = '" . $oDB->escape($strVal) . "'"; 
		}
	}
	
	foreach ($arFields as $strTable=>$arRecords) {
		foreach ($arRecords as $iID=>$arFields) {
			$strSQL = "update " . $strTable . " set " . implode(",", $arFields) . " where id = " . $iID . ";"; 
			$oDB->sql($strSQL); 
			$oDB->execute(); 
		}	
	} 
?>