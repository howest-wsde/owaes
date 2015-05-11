<?php 

	$iID = isset($_GET["id"]) ? intval($_GET["id"]) : -1; 
	$iW = intval(isset($_GET["w"])?$_GET["w"]:0);  
	$iH = intval(isset($_GET["h"])?$_GET["h"]:0); 
	
	$strFilename = "upload/profiles/id/" . $iID . ".png"; 
	if (!file_exists($strFilename)) $strFilename = "upload/profiles/noprofileimg.png"; 
	if ($iID == 0) $strFilename = "upload/profiles/owaes.png"; 
	if ($iID == -1) $strFilename = "upload/profiles/noprofileimg.png"; 
	
	$oSource = imagecreatefrompng($strFilename);
	$iProp = imagesx($oSource)/imagesy($oSource);
	
	if ($iW==0) $iW = $iH * $iProp; 
	if ($iH==0) $iH = $iW / $iProp; 
	
	$oThumb = imagecreatetruecolor($iW, $iH);
	
	if ($iW/$iH > $iProp) {
		$iX = 0;
		$iY = ($iH - ($iW / $iProp))/2;
		$iH = $iW / $iProp; 
	} elseif ($iW/$iH < $iProp) { 
		$iX = ($iW - ($iH * $iProp))/2;
		$iY = 0; 
		$iW = $iH * $iProp;  
	} else {
		$iX = 0;
		$iY = 0; 
	}
	 

	imagealphablending($oThumb, false); 
	imagesavealpha($oThumb, true);
	
	imagecopyresampled($oThumb, $oSource, $iX, $iY, 0, 0, $iW, $iH, imagesx($oSource), imagesy($oSource));
	
	header('Content-Type: image/png');
	imagepng($oThumb); 
	
?>