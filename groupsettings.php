<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
    $iID = intval($_GET["id"]); 
	$oGroup = new group($iID); 
	$oRights = $oGroup->userrights();  
	// if (!$oRights->groupinfo()) $oSecurity->doLogout(); 
	
	if ($oRights->groupinfo() && isset($_POST["group"])) {
		$oGroup->naam($_POST["naam"]);  
		$oGroup->info($_POST["info"]);  
		if ($_FILES["img"]["error"] == 0){ 
			$arIMG = getimagesize($_FILES["img"]["tmp_name"]); 
			$strImage = uniqueKey() . "." . extentie($_FILES["img"]["name"]);  
			switch($arIMG["mime"]) {
				case "image/jpeg": 
				case "image/gif": 
				case "image/png": 
					move_uploaded_file($_FILES["img"]["tmp_name"], "upload/groups/" . $strImage); 
					$oGroup->image($strImage); 
					switch($arIMG["mime"]) {
						case "image/jpeg": 
							$oGroupIMG = imagecreatefromjpeg("upload/groups/" . $strImage);
							break; 
						case "image/gif": 
							$oGroupIMG = imagecreatefromgif("upload/groups/" . $strImage);
							break; 
						case "image/png": 
							$oGroupIMG = imagecreatefrompng("upload/groups/" . $strImage);
							break; 
					}
					$iX = imagesx($oGroupIMG); 
					$iY = imagesy($oGroupIMG); 
					$iX2 = 0; 
					if ($iX > $iY) {
						if ($iX > 300) {
							$iY2 = 300 / ($iX/$iY); 
							$iX2 = 300; 
						} 
					} else {
						if ($iY > 300) {
							$iX2 = 300 / ($iY/$iX); 
							$iY2 = 300; 
						}	
					}
					if ($iX2 != 0) {
						$oThumb = imagecreatetruecolor($iX2, $iY2);
						imagecopyresampled($oThumb, $oGroupIMG, 0, 0, 0, 0, $iX2, $iY2, $iX, $iY);
						imagepng($oThumb, "upload/groups/id/" . $oGroup->id() . ".png");
						imagedestroy($oThumb); 
					} else {
						imagepng($oGroupIMG, "upload/groups/id/" . $oGroup->id() . ".png");
					}
					imagedestroy($oGroupIMG); 
					break;  
				default: 
 					// echo $arIMG["mime"];   // niet ondersteund
			} 
			
			 
		}

		$oGroup->update(); 
	} 
	
	$arRights = array(
		"useradd" => "Add user", 
		"userdel" => "Del user", 
		"userrights" => "User rights", 
		"owaesadd" => "Add Owaes", 
		"owaesedit" => "Edit Owaes", 
		"owaesdel" => "Del Owaes", 
		"owaesselect" => "Select users", 
		"owaespay" => "Pay users", 
		"groupinfo" => "Edit group", 
	); 
	if ($oRights->userrights() && isset($_POST["rights"])) {
		foreach ($oGroup->users() as $oUser){  
			$oRechten = $oGroup->userrights($oUser->id());  
			foreach ($arRights as $strKey=>$strTitle) {
				$strField = $strKey . "_" . $oUser->id(); 
				$oRechten->right($strKey, ((isset($_POST[$strField])?$_POST[$strField]:0)==1));
			}
			$oRechten->update(); 
		} 
	}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="settings">
    	<div class="header">
        	<a href="main.php"><img src="img/logo.png" /></a>
        </div>
    	<div class="body">
        	<? echo $oPage->startTabs(); ?>
                <div class="sideleftcenter">
                	<? if ($oRights->groupinfo()) { ?>
                        <form method="post" name="frmgroup" id="frmgroup" enctype="multipart/form-data"> 
                            <dl>
                                <dt>Naam:</dt>
                                <dd><input type="text" name="naam" id="naam" value="<? echo inputfield($oGroup->naam()); ?>" /></dd> 
                                <dt>Info: </dt>
                                <dd><textarea name="info" id="info"><? echo textarea($oGroup->info()); ?></textarea></dd>
                                <dt>Foto: </dt>
                                <dd><input type="file" name="img" id="img" value="" class="image" /></dd>
                                <dd><? echo $oGroup->getImage(); ?></dd>
                            </dl> 
                            <input type="submit" value="save" id="group" name="group" />
                        </form>
                        <hr />
                   	<? } ?>
                    <? if ($oRights->userrights()) { ?> 
                    	<form method="post" >
                        	<table>
                            	<tr>
                                	<th>Naam</th>
                                    <?
                                    	foreach ($arRights as $strKey=>$strTitle) echo ("<th>" . $strTitle . "</th>")
									?> 
                                </tr>
                            	<? 
									foreach ($oGroup->users() as $oUser){ 
										$oRechten = $oGroup->userrights($oUser->id()); 
										echo ("<tr>"); 
										echo ("<td>" . $oUser->getName() . "</td>");
										foreach ($arRights as $strKey=>$strTitle){ 
											 echo ("<td><input type=\"checkbox\" name=\"" . $strKey . "_" . $oUser->id() . "\" value=\"1\" " . ($oRechten->right($strKey)?"checked=\"checked\"":"") . " /></td>");
										} 
										echo ("</tr>"); 
									}
								?> 
                            </table>
                            <input type="submit" value="save" id="rights" name="rights" />
                        </form>
                   	<? } ?>
                </div>
                <div class="sideright">
                    
                </div>
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
