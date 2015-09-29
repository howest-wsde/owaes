<?php 
	$iID = intval($_GET["id"]); 
	$iW = intval(isset($_GET["w"])?$_GET["w"]:0);  
	$iH = intval(isset($_GET["h"])?$_GET["h"]:0); 
	
	$strFilename = "upload/groups/id/" . $iID . ".png"; 
	if (!file_exists($strFilename)) $strFilename = "upload/groups/noprofileimg.png"; 
	
	$oSource = imagecreatefrompng($strFilename);
	$iProp = imagesx($oSource)/imagesy($oSource);
	
	if ($iW==0) $iW = $iH * $iProp; 
	if ($iH==0) $iH = $iW / $iProp; 
	
	$oThumb = imagecreatetruecolor($iW, $iH);
	imagealphablending($oThumb, false);
	imagesavealpha($oThumb, true);
	
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
	  


	imagecopyresampled($oThumb, $oSource, $iX, $iY, 0, 0, $iW, $iH, imagesx($oSource), imagesy($oSource));
	

	if (isset($_GET["u"])){ 
		$strUserIMG = "upload/profiles/id/" . intval($_GET["u"]) . ".png"; 
		if (file_exists($strUserIMG)) {
			$oUser = imagecreatefrompng($strUserIMG); 
			imagecopyresampled($oThumb, $oUser, round($iW/30), round($iW/30), 0, 0, round($iW/3), round($iH/3), imagesx($oUser), imagesy($oUser));

			/*// bool imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h ,
			int $src_w , int $src_h )

			imagecopyresampled( 			$oThumb, 					$oUser, 		0, 				0, 			0, 				0,		 $iW, 		$iH, 
				imagesx($oUser), imagesy($oUser));
*/
		}

	}

	header('Content-Type: image/png');
	imagepng($oThumb); 
	
?>