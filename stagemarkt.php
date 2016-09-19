<?php
	include "inc.default.php"; // should be included in EVERY file


	$oSecurity = new security(FALSE);
	$oSecurity->doLogout(FALSE);

	$strLogin = "";

	$oUser = new user();

	if (isset($_GET["p"])) {
		$strRedirect = $_GET["p"];
	} else if (isset($_POST["from"])) {
		$strRedirect = $_POST["from"];
	} else $strRedirect = "main.php?start";

	if (isset($_GET["demo"]) && settings("debugging", "demo")) {
		if ($oSecurity->doLogin("demo")) redirect($strRedirect);
	}

	if (isset($_POST["dosignup"])) {
	//	$bResult = $oSecurity->doLogin($_POST["email"], $_POST["pass"]);
	//	if ($bResult == TRUE) {
	//		//redirect($strRedirect);
	//		//exit();
	//	}

		$arErrors = array();

		//if (!$oUser->login($_POST["username"])) $arErrors["username"] = "De gekozen loginnaam is ongeldig of bestaande. Een andere werd voorgesteld ";
		$oUser->login("");
		$oUser->firstname($_POST["firstname"]);
		$oUser->lastname($_POST["lastname"]);
		//$oUser->dienstverlener($_POST["dienstverlener"]);
		if ($_POST["email"] == "") {
			$arErrors["email"] = "E-mailadres is verplicht";
		} else if (!validEmail($_POST["email"])) {
			$arErrors["email"] = "Ongeldig e-mailadres";
		} else {
			if (!$oUser->changeEmail($_POST["email"])) $arErrors["email"] = "Dit e-mailadres bestaat reeds in het systeem";
		}
		$oUser->alias("", TRUE);
		$oUser->password($_POST["pass"]);
		$oUser->algemenevoorwaarden(settings("startvalues", "algemenevoorwaarden"));
		$oUser->visible(settings("startvalues", "visibility"));

		if ($_POST["pass"] == "") $arErrors["password"] = "Wachtwoord is verplicht";
		if ($_POST["pass"] != $_POST["pass-repeat"]) $arErrors["pass-repeat"] = "Wachtwoord komt niet overeen";

		if (count($arErrors) == 0)  {
			$oUser->update();
			me($oUser->id()); // SET me
			$oUser->changeEmail($_POST["email"], TRUE);

			$oLog = new log("user aangemaakt", array(
												"id" => $oUser->id(),
												"naam" => $oUser->login(),
												"login" => $oUser->getName(),
												"email" => $oUser->email(),
												"postvalues" => $_POST,
											));
			$bResult = $oSecurity->doLogin($oUser->login(), $_POST["pass"]);

			if ($_FILES["logo"]["error"] == 0){
				$strLogo = "upload/tmp/" . time() . "." . $_FILES["logo"]["name"];
				move_uploaded_file($_FILES["logo"]["tmp_name"], $strLogo);
			} else $strLogo = "";

 			$oDB = new database();
			$oDB->execute("insert into tblStagemarkt (user, groepsnaam, description, interesse, logo, website) values ('" . $oUser->id() . "', '" . $oDB->escape($_POST["bedrijf"]) . "', '" . $oDB->escape($_POST["bedrijfsinfo"]) . "', '" . $oDB->escape($_POST["bedrijfsdoel"]) . "', '" . $oDB->escape($strLogo) . "', '" . $oDB->escape($_POST["website"]) . "'); ");

			$oUser->data("stagemarkt", $oDB->lastInsertID());
			$oUser->update();

			redirect("stagemarkt.inschrijving.php");
			exit();
		}
	}

    //if (isset($_POST["dologin"])) {
    //    $bResult = $oSecurity->doLogin($_POST["username"], $_POST["pass"]);
    //    if ($bResult == TRUE) {
    //        header("Location: " . $strRedirect);
    //        exit();
    //    } else {
    //        echo $oSecurity->errorMessage();
    //    }
    //}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader();
		?>
        <script>
            $(document).ready(function () {

			});
		</script>
    </head>
    <body id="login">
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

    	<div class="body container content content-login">
            <div class="row">
            <div class="signup col-lg-12">
            <div class="well">
            <form method="post" class="form-horizontal" enctype="multipart/form-data">
                	<fieldset>
                        <legend>Inschrijven voor Werkplekleren- en Stagemarktevent donderdag 22 september 2016</legend>

 <?php /*
                     <p>Inschrijven is helaas niet meer mogelijk</p>

                    */ ?>

                    <input type="hidden" name="from" id="from" value="index.php" />
                    <div class="form-group">
                        <label for="bedrijf" class="control-label col-lg-3">Bedrijfsnaam:</label>
                        <div class="col-lg-9">
                            <input type="text" name="bedrijf" class="bedrijf form-control" id="bedrijf" placeholder="Bedrijfsnaam" value="<? echo (isset($_POST["bedrijf"])?$_POST["bedrijf"]:""); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="control-label col-lg-3">Uw voornaam:</label>
                        <div class="col-lg-9">
                            <input type="text" name="firstname" class="firstname form-control" id="firstname" placeholder="Voornaam" value="<?php echo inputfield($oUser->firstname()); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label col-lg-3">Uw familienaam:</label>
                        <div class="col-lg-9">
                            <input type="text" name="lastname" class="lastname form-control" id="lastname" placeholder="Familienaam" value="<?php echo inputfield($oUser->lastname()); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label col-lg-3">E-mailadres:</label>
                        <div class="col-lg-9">
                            <input type="email" name="email" class="email form-control" id="username" placeholder="E-mailadres" value="<?php echo inputfield($oUser->email()); ?>" />
                            <?php
                        	    if (isset($arErrors["email"])) echo ("<strong class=\"text-danger\">" . $arErrors["email"] . "</strong>");
						    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pass" class="control-label col-lg-3">Wachtwoord:</label>
                        <div class="col-lg-9">
                            <input type="password" name="pass" class="pass form-control" id="pass" placeholder="Wachtwoord" />
                            <?php
                        	    if (isset($arErrors["password"])) echo ("<strong class=\"text-danger\">" . $arErrors["password"] . "</strong>");
						    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pass-repeat" class="control-label col-lg-3">Wachtwoord herhalen:</label>
                        <div class="col-lg-9">
                            <input type="password" name="pass-repeat" class="pass-repeat form-control" id="pass-repeat" placeholder="Wachtwoord herhalen" />
                            <?php
                            	if (isset($arErrors["pass-repeat"])) echo ("<strong class=\"text-danger\">" . $arErrors["pass-repeat"] . "</strong>");
						    ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bedrijfsinfo" class="control-label col-lg-3">Bedrijfsinfo:</label>
                        <div class="col-lg-9">
                            <textarea name="bedrijfsinfo" class="bedrijfsinfo form-control" id="bedrijfsinfo" placeholder="Geef meer informatie over uw bedrijf"><? echo (isset($_POST["bedrijfsinfo"])?$_POST["bedrijfsinfo"]:""); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bedrijfsdoel" class="control-label col-lg-3">Interesse:</label>
                        <div class="col-lg-9">
                            <textarea name="bedrijfsdoel" class="bedrijfsdoel form-control" id="bedrijfsdoel" placeholder="Naar wie is uw bedrijf op zoek?"><? echo (isset($_POST["bedrijfsdoel"])?$_POST["bedrijfsdoel"]:""); ?></textarea>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="logo" class="control-label col-lg-3">Logo:</label>
                        <div class="col-lg-9">
                            <input type="file" name="logo" ext="jpg,jpeg,gif,bmp,png" class="img image form-control" id="logo" placeholder="" value="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="website" class="control-label col-lg-3">Website:</label>
                        <div class="col-lg-9">
                            <input type="text" name="website" class="website form-control" id="website" placeholder="http://www.uwbedrijf.be" value="<? echo (isset($_POST["website"])?$_POST["website"]:""); ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                             <div class="col-lg-3"></div>
                             <div class="col-lg-6"><a href="modal.voorwaarden.php" class="domodal">gebruikersvoorwaarden</a></div>
                       		 <div class="col-lg-3">
                                <button type="submit" name="dosignup" class="btn btn-default pull-right">Registreren</button>
                            </div>
                    </div>
                    </fieldset>
                </form>
                </div>
            </div>
            </div>
        </div>
		<div class="modal fade" id="paswoordvergeten">
            <form method="post" class="form-horizontal form-recoverpw">
              <div class="modal-dialog">
                <div class="modal-content" id="requestwachtwoordbody">
                  <div class="modal-header">
                  	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Wachtwoord vergeten</h4>
                  </div>
                  <div class="modal-body">
                    <p>Geef uw e-mailadres of gebruikersnaam op om een nieuw wachtwoord aan te vragen</p>
                    <div class="form-group">
                    <!-- <label for="username" class="col-lg-3 control-label">Gebruikersnaam</label> -->
                    <div class="col-lg-12">
                    	<input type="hidden" name="recover" value="y" />
	                    <input type="text" name="search" class="search form-control" id="mailpaswoordlost" placeholder="E-mailadres of gebruikersnaam" autofocus value="<?php echo inputfield($strLogin); ?>" />
                    </div>
                    </div>

                  </div>
                  <div class="modal-footer">
                    <input type="submit" class="btn btn-default" id="btn-paswoord" value="Aanvragen" />
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
         </form>
        </div><!-- /.modal -->
        <div class="footer">
        </div>
    </body>
</html>
