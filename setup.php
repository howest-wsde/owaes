<?php
	include "inc.default.php"; // should be included in EVERY file  
	
	$strDomein = strtolower($_SERVER['HTTP_HOST']); 
	$strLoc = $_SERVER["REQUEST_URI"]; 
	$arLoc = explode("setup.php", $strLoc); 
	$strRoot = $arLoc[0]; 
	$strHTTP = isset($_SERVER["HTTPS"]) ? (($_SERVER["HTTPS"]=="on")?"https":"http") : "http"; 
	$arSetup = array(
		"domain" => array(
			"name" => $strDomein, 
			"root" => $strRoot, 
			"absroot" => "$strHTTP://$strDomein$strRoot", 
		)
	); 
	
	if (isset($_POST["domainname"])) {
		$oDB = new database(); 
		$oDB->execute("update `tblConfig` set value = '" . $oDB->escape(json_encode($_POST["domainname"])) . "' where `key` = 'domain.name' and `value` is NULL; "); 
		$oDB->execute("update `tblConfig` set value = '" . $oDB->escape(json_encode($_POST["domainroot"])) . "' where `key` = 'domain.root' and `value` is NULL; "); 
		$oDB->execute("update `tblConfig` set value = '" . $oDB->escape(json_encode($_POST["domainabsroot"])) . "' where `key` = 'domain.absroot' and `value` is NULL; "); 
		redirect("login.php"); 
	}
		
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?> 
    </head>
    <body id="settings"> 
         <nav class="navbar navbar-default">
            <div class="container">
                <div class="row">
                    <div class="navbar-header">
                        <a href=""><h1 class="navbar-brand">OWAES</h1></a>
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="navbar-collapse collapse" id="navbar-main"><ul class="nav navbar-nav navbar-right"></ul></div>
                </div>
            </div>
        </nav>
    	<div class="body content content-account-settings container"> 
            <div class="container sideleftcenter">
                <form method="post" name="frmprofile" id="frmprofile" class="form-horizontal" enctype="multipart/form-data">  
                    <fieldset>
                        <legend>Setup</legend>
                        <div class="form-group">
                            <label for="alias" class="control-label col-lg-2">Domain name:</label>
                            <div class="col-lg-10">
                                <input type="text" name="domainname" class="alias form-control" id="domainname" placeholder="Alias" value="<?php echo inputfield($arSetup["domain"]["name"]); ?>" />
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label for="alias" class="control-label col-lg-2">Website root:</label>
                            <div class="col-lg-10">
                                <input type="text" name="domainroot" class="alias form-control" id="domainroot" placeholder="Alias" value="<?php echo inputfield($arSetup["domain"]["root"]); ?>" />
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label for="alias" class="control-label col-lg-2">Absolute path:</label>
                            <div class="col-lg-10">
                                <input type="text" name="domainabsroot" class="alias form-control" id="domainabsroot" placeholder="Alias" value="<?php echo inputfield($arSetup["domain"]["absroot"]); ?>" />
                            </div> 
                        </div>  
                        <div class="form-group">
                            <div class="col-lg-12">
                                <input type="submit" value="Gegevens opslaan" id="save" class="btn btn-default pull-right" name="profile" />
                            </div>
                        </div> 
                    </fieldset>
                </form>
            </div>  
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
