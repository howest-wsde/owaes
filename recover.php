<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(FALSE); 
	
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$strPage = "RECOVER"; 
 
	if (isset($_POST["recover"])) {
		$oUser = new user(); 
		$oUser->search(array("mail" => $_POST["search"], "login" => $_POST["search"]), false);  
		if ($oUser->id() != 0) {
			$oUser->unlocked(TRUE); // als e-mailadres hidden staat kan deze anders niet gezien worden
			$strPass = uniqueKey(); 
			$iExpires = (owaesTime()+(60*60*24)); 
			$oDB = new database();  
			$oDB->execute("insert into tblUserRecover (user, timeasked, timeexpires, ipasked, conf, passcode) values ('" . $oUser->id() . "', '" . owaesTime() . "', '" . $iExpires . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $_SERVER['HTTP_USER_AGENT'] . "', '" . $strPass . "'); "); 
			$oMail = new email(); 
				$oMail->setTo($oUser->email(), $oUser->getName());
				$strMail = $oUser->HTML("mail.recoverpassword.html"); 
				$strLink = fixPath("login.php?recover=" . urlencode($strPass), TRUE); 
				$strMail = str_replace("[recoverurl]", $strLink, $strMail); 
				$strMail = str_replace("[recoverexpirydate]", str_date($iExpires, "j M"), $strMail);
				$strMail = str_replace("[recoverexpirytime]", str_date($iExpires, "H:i"), $strMail); 
				$oMail->setBody($strMail);  
				$oMail->setSubject("OWAES Wachtwoord reset"); 
			$oMail->send();  
			$strPage = "MAILED"; 
		} else {
			$strPage = "NOTFOUND"; 
		}
	} 
	
	if (isset($_POST["code"])) {
		$strPass = $_POST["code"]; 
		$oDB = new database();
		$oDB->sql("select * from tblUserRecover where passcode = '" . $oDB->escape($strPass) . "' and timeexpires > " . owaesTime() . "; "); 
		if ($oDB->execute() == 1) {
			$strPage = "CHANGEPASSWORD"; 
		} else {
			$strPage = "INVALIDCODE"; 
		}
	}
	
	if (isset($_POST["changepass"])) {
		$strCode = $_POST["code"]; 
		if ($_POST["pass1"] == $_POST["pass2"]) {
			$oDB = new database();
			$oDB->sql("select * from tblUserRecover where passcode = '" . $oDB->escape($strCode) . "' and timeexpires > " . owaesTime() . "; ");
			if ($oDB->execute() == 1) {
				$oUser = user($oDB->get("user")); 
				$oUser->unlocked(TRUE); 
				$oUser->password($_POST["pass1"]); 
				$oUser->update(); 
				$strPage = "PASSCHANGED"; 
			//	$bResult = $oSecurity->doLogin($oUser->email(), $_POST["pass1"]); 
			//	if ($bResult == TRUE) {
			//		$strPage = "PASSCHANGED"; 
			//	} else {
			//		$strPage = "PASSNOCHANGED"; 
			//		$strError = $oSecurity->errorMessage() ; 
			//	}
			} else vardump($oDB); 
		} else $strPage = "PASSESNOMATCH"; 
	}
	
	
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	<h4 class="modal-title">Wachtwoord vergeten</h4>
</div>
<?php
	switch($strPage) {
		case "MAILED": 
			?>
				<div class="modal-body">
					<p>Er werd een link gestuurd naar uw e-mailadres. Via deze link kunt u uw wachtwoord aanpassen. De link blijft 24 uur geldig. </p> 
				</div>
				<div class="modal-footer"> 
					<button type="button" class="btn btn-default" id="btn-ok-pwd" data-dismiss="modal">Ok</button>
				</div>
			<?	
			break; 
		case "INVALIDCODE": 
		case "PASSCHANGED": 
		case "PASSNOCHANGED": 
			switch($strPage) {
				case "INVALIDCODE": 
					$strZin = "Er werd een ongeldige code doorgestuurd of de tijd is verstreken. De 'paswoord-vergeten'-link blijft slechts 24 uur geldig."; 
					break; 
				case "PASSCHANGED": 
					$strZin = "Je paswoord werd aangepast. "; 
					break; 
				case "PASSNOCHANGED": 
					$strZin = "Er is een probleem opgetreden waardoor je paswoord niet aangepast kon worden. ($strError)"; 
					break; 
			} 
			?>
				<div class="modal-body">
					<p><?php echo $strZin; ?></p>
				</div>
				<div class="modal-footer"> 
					<button type="button" class="btn btn-default" id="btn-ok-pwd" data-dismiss="modal">Ok</button>
				</div> 
			<?	
			break; 
		case "CHANGEPASSWORD": 	
		case "PASSESNOMATCH": 	
			$strZin = ($strPage == "PASSESNOMATCH") ? 
				"Beide ingevulde paswoorden moeten gelijk zijn. Probeer opnieuw. "
				: "Geef een nieuw wachtwoord in:"; 
			?>
				<div class="modal-body">
					<p><?php echo $strZin; ?></p>
                    <input type="hidden" name="code"  value="<?php echo $_POST["code"]; ?>" />
                    <input type="hidden" name="changepass"  value="y" /> 
					<div class="form-group"> <div class="col-lg-12">
                  	  <input type="password" name="pass1" class="form-control" id="pass1" placeholder="paswoord" /> 
                    </div></div>
                    <div class="form-group"><div class="col-lg-12">
                  	  <input type="password" name="pass2" class="form-control" id="pass2" placeholder="herhaal paswoord" />  
                    </div>
                    </div>
				</div> 
				<div class="modal-footer"> 
					<input type="submit" class="btn btn-default" id="btn-paswoord" value="Opslaan" />
				</div>
			<?php
			break;  
		
		case "NOTFOUND": 
		default: 
			$strZin = ($strPage == "NOTFOUND") ? 
				"De ingevulde gegevens werden niet gevonden. Probeer opnieuw. "
				: "Geef je mailadres of inlognaam in"; 
			?>
				<div class="modal-body">
					<p><?php echo $strZin; ?></p>
                    <input type="hidden" name="recover" value="y" />
					<div class="form-group"><div class="col-lg-12">
						<input type="text" name="search" class="username form-control" id="mailpaswoordlost" placeholder="E-mailadres of gebruikersnaam" autofocus />
					</div></div>
				</div> 
				<div class="modal-footer"> 
					<input type="submit" class="btn btn-default" id="btn-paswoord" value="Aanvragen" />
				</div>
			<?php 
	}
?> 