<?php
	include "inc.default.php"; // should be included in EVERY file 

	$oSecurity = new security(TRUE); 
	

	define('CHUNK_SIZE', 1024*1024); // Size (in bytes) of tiles chunk
 
	function readfile_chunked($filename, $retbytes = TRUE) {
		$buffer = '';
		$cnt =0;
		// $handle = fopen($filename, 'rb');
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, CHUNK_SIZE);
			echo $buffer;
			ob_flush();
			flush();
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
	 
	//$oLog = new log("download", array("url" => $oPage->filename())); 
	$strFilePath = FALSE; 
	$strFileName = "unknown.file"; 

	if (isset($_GET["u"])) {
		$iUser = intval($_GET["u"]); 
		$strFile = $_GET["f"]; 
		
		$oUser = user($iUser);  
		$arBestand = $oUser->files($strFile); 

		if ($arBestand && file_exists($arBestand["location"])) { 
			$strFilePath = $arBestand["location"];  
			$strFileName = $arBestand["filename"];
		}  else {
			redirect($oUser->getURL());
		}
	}
	if (isset($_GET["m"])) {
		$strFilePath = "upload/market/" . md5($_GET["f"]); 
		$arFile = explode(".", $_GET["f"], 2);  
		$strFileName = $arFile[1]; 
	} 
	 
	if ($strFilePath && file_exists($strFilePath)) {
		$mimetype = 'application/force-download';
		header('Content-Type: ' . $mimetype );
		header('Content-Disposition: attachment; filename="' . $strFileName . '"'); 
		readfile_chunked($strFilePath);	
	}  else {
		echo ("bestand niet gevonden"); 
	} 