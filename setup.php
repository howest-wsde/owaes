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
		), 
		"database" => array( 
			"host" => "localhost", 
			"name" => NULL, 
			"user" => NULL, 
			"password" => NULL,  
		), 
	);  
	
	$strConfigFile = '<?php 
$arConfig["database"] = array(
	"host" => "HOST",
	"name" => "NAME",
	"user" => "USER",
	"password" => \'PASS\', 
);'; 
	
	if (isset($_POST["domainname"])) {
		$oDB = new database(); 
		$oDB->execute("CREATE TABLE IF NOT EXISTS `tblConfig` (`id` bigint(20) NOT NULL AUTO_INCREMENT, `sleutel` varchar(255) NOT NULL, `waarde` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;"); 
		$arUpdates = array(
			"domain.name" => $_POST["domainname"], 
			"domain.root" => $_POST["domainroot"], 
			"domain.absroot" => $_POST["domainabsroot"], 
		); 
		foreach ($arUpdates as $strKey=>$strVal) { 
			$oDB->execute("INSERT INTO tblConfig (`sleutel`, `waarde`) VALUES('$strKey', '" . $oDB->escape(json_encode($strVal)) . "') ON DUPLICATE KEY UPDATE `sleutel`=VALUES(`sleutel`), `waarde`=VALUES(`waarde`);"); 
		} 
		redirect("login.php"); 
	} 
	
	if (settingsOK()) redirect("login.php"); 
	
		
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?> 
        <script>
			$(document).ready(function(e) {
                $(":input.db").keyup(function(){
					setVal(); 
				}); 
				setVal(); 
            });
			function setVal() {
				strVal = "<?php echo str_replace("\n", '\n', str_replace('"', '\"', $strConfigFile)); ?>"; 
				strVal = strVal.split("HOST").join($("#host").val()); 
				strVal = strVal.split("NAME").join($("#name").val()); 
				strVal = strVal.split("USER").join($("#user").val()); 
				strVal = strVal.split("PASS").join($("#password").val()); 
				$("#configfile").val(strVal); 	
			}
		</script>
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
                	<?php 
						$arFolders = array("upload", "cache", "settings"); 
						$arNotOK = array(); 
						foreach ($arFolders as $strFolder) {
							if (!is_writable("$strFolder")) $arNotOK[] = $strFolder; 
						}
						
						if (count($arNotOK)>0) {
							echo ("<fieldset>
									<legend>Folders</legend> 
									<p>Please make folder(s) <strong>" . implode("</strong>, <strong>", $arNotOK) . "</strong> write enabled</p>
								</fieldset>"); 
						} 
						
						if (ini_get("short_open_tag") == "Off") {
							echo ("<fieldset>
									<legend>php.ini</legend> 
									<p>Please change the value of 'short_open_tag' to 'On'</p>
								</fieldset>"); 	
						}
						 
					?>
                	<?php if (!file_exists("inc.config.db.php") || !settings("database", "loaded")) { ?> 
                        <fieldset>
                            <legend>Database</legend>
                            <p>Please create a mySQL-database and enter your settings: </p>
                            <div class="form-group">
                                <label for="alias" class="control-label col-lg-2">Database host:</label>
                                <div class="col-lg-10">
                                    <input type="text" name="host" class="db form-control" id="host" placeholder="host" value="<?php echo inputfield($arSetup["database"]["host"]); ?>" />
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label for="alias" class="control-label col-lg-2">Database name:</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name" class="db form-control" id="name" placeholder="name" value="<?php echo inputfield($arSetup["database"]["name"]); ?>" />
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label for="alias" class="control-label col-lg-2">User:</label>
                                <div class="col-lg-10">
                                    <input type="text" name="user" class="db form-control" id="user" placeholder="user" value="<?php echo inputfield($arSetup["database"]["user"]); ?>" />
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label for="alias" class="control-label col-lg-2">Password:</label>
                                <div class="col-lg-10">
                                    <input type="text" name="password" class="db form-control" id="password" placeholder="password" value="" />
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label for="alias" class="control-label col-lg-2">inc.config.db.php:</label>
                                <div class="col-lg-10">
                                	<p style="padding: 10px 0 0; ">Save this code as <strong>inc.config.db.php</strong> and reload this page</p>
                                    <textarea class="form-control" id="configfile" style="height: 200px; "><?php echo $strConfigFile; ?></textarea> 
                                </div> 
                            </div>   
                        </fieldset>
                    <?php } else if (!(settings("domain", "name") && settings("domain", "root") && settings("domain", "absroot"))) { ?> 
                        <fieldset>
                            <legend>Login</legend>
                            <div class="col-lg-22"></div> 
                            <div class="form-group col-lg-10">
                            	<p>Er werd automatisch een eerste gebruiker / administrator gemaakt met login = "owaes", paswoord = "owaes". </p>
                                <p><strong>Let op!</strong> Wijzig deze login en paswoord om misbruik te voorkomen. </p>
                            </div>   
						</fieldset>
                        
                        <fieldset>
                            <legend>Domeinnaam</legend>
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
                        
                    <?php } ?> 
                </form>
            </div>  
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
