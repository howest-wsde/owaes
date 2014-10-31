<?
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
	 
	$oLog = new log("download", array("url" => $oPage->filename())); 

	$iUser = intval($_GET["u"]); 
	$strFile = $_GET["f"]; 
	 
	$oUser = user($iUser);  
	$arBestand = $oUser->files($strFile); 
	
	if ($arBestand && file_exists($arBestand["location"])) { 
		$mimetype = 'application/force-download';
		header('Content-Type: ' . $mimetype );
		header('Content-Disposition: attachment; filename="' . $arBestand["filename"] . '"'); 
		readfile_chunked($arBestand["location"]);	
	}  else {
		redirect($oUser->getURL());
	}
?>