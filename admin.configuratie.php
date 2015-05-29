<?php
	include "inc.default.php"; // should be included in EVERY file

	$oSecurity = new security(TRUE);

	if (!$oSecurity->admin()) stop("admin");

	$oPage->addJS("script/admin.js");
	$oPage->addCSS("style/admin.css");

	$oPage->addCSS("style/configuratie.css");
	$oPage->addJS("script/confVal.js");

	function periodInSeconds($rb, $value) {
		$period = $rb;
		$result = intval($value) * 24 * 3600;

		if ($period == "week") $result = intval($value) * 168 * 3600;

		return $result;
	}

	function getPeriod($seconds, $from) {
		$hours = $seconds / 3600;
		$days = $hours / 24;
		$weeks = $days / 7;

		$test = "";

		if ($from == "day" && !is_int($weeks)) $test = "checked='checked'";
		if ($from == "week" && is_int($weeks)) $test = "checked='checked'";

		return $test;
	}

	function convertSeconds($seconds) {
		$hours = $seconds / 3600;
		$days = $hours / 24;
		$weeks = $days / 7;

		return (is_int($weeks)) ? $weeks : $days;
	}

	function addressToCoordinates($address) {
		$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=" . $address;
		$response = file_get_contents($url);
		$json = json_decode($response, TRUE);

		$coord = array(
			"latitude" => $json["results"][0]["geometry"]["location"]["lat"],
			"longitude" => $json["results"][0]["geometry"]["location"]["lng"]
		);

		return $coord;
	}

	function coordinatesToAddress($lat, $lon) {
		$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&latlng=" . $lat . "," . $lon;
		$response = file_get_contents($url);
		$json = json_decode($response, TRUE);

		$address = $json["results"][0]["address_components"][2]["long_name"];

		return $address;
	}
	
	function encryptor($text) {
		$key = pack("H*", "cf372282683d4802ee035e793218e2e4a8a8eb4f6a1d5675b6a6a289c860abde");
		$key_size = strlen($key);

		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

		$block = mcrypt_get_block_size("rijndael_256", "cbc");
		$pad = $block - (strlen($text) % $block);
		$text .= str_repeat(chr($pad), $pad);

		$cipherText = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_CBC, $iv);
		$cipherText = $iv . $cipherText;

		$encodeCipher = base64_encode($cipherText);

		return $encodeCipher;
	}

	function prepareAndExecuteStmt($key, $val) {
		$query = "UPDATE `tblConfig` SET `value` = '" . json_encode($val) . "' WHERE `key` LIKE '" . $key . "';";

		$oDB = new database();
		$oDB->execute($query);
	}

	function issetAndNotEmpty($field) {
		$test = false;

		if (isset($field) && !empty($field)) {
			$test = true;
		}

		return $test;
	}

	if (isset($_POST["btnOpslaan"])) {
		/* Startwaarden */
		if (issetAndNotEmpty($_POST["txtTemplateFolder"])) prepareAndExecuteStmt("domain.templatefolder", $_POST["txtTemplateFolder"]);

		$test = FALSE;
		if (isset($_POST["chkAlgemenevoorwaarden"])) $test = TRUE;

		prepareAndExecuteStmt("startvalues.algemenevoorwaarden", $test);

		$test = FALSE;
		if (isset($_POST["chkVisibility"])) $test = TRUE;

		prepareAndExecuteStmt("startvalues.visibility", $test);

		if (isset($_POST["txtAnalytics"])) prepareAndExecuteStmt("analytics", $_POST["txtAnalytics"]);

		/* ------------- */

		/* Debugging */
		$test = FALSE;
		if (isset($_POST["chkShowwarnings"])) $test = TRUE;

		prepareAndExecuteStmt("debugging.showwarnings", $test);

		$test = FALSE;
		if (isset($_POST["chkDemo"])) $test = TRUE;

		prepareAndExecuteStmt("debugging.demo", $test);

		/* ------------- */

		/* Verzekeringen */
		if (issetAndNotEmpty($_POST["txtV1"])) prepareAndExecuteStmt("verzekeringen.1", $_POST["txtV1"]);
		if (issetAndNotEmpty($_POST["txtV2"])) prepareAndExecuteStmt("verzekeringen.2", $_POST["txtV2"]);

		/* ------------- */

		/* Tijdzone en lokatie */
		prepareAndExecuteStmt("date.timezone", $_POST["lstTimezone"]);

		if (isset($_POST["txtLokatie"])) {
			$coord = addressToCoordinates($_POST["txtLokatie"]);

			prepareAndExecuteStmt("geo.latitude", $coord["latitude"]);
			prepareAndExecuteStmt("geo.longitude", $coord["longitude"]);
		}

		/* ------------- */

		/* Credits */
		if (isset($_POST["txtStart"])) prepareAndExecuteStmt("startvalues.credits", intval($_POST["txtStart"]));
		if (isset($_POST["txtMin"])) prepareAndExecuteStmt("credits.min", intval($_POST["txtMin"]));
		if (isset($_POST["txtMax"])) prepareAndExecuteStmt("credits.max", intval($_POST["txtMax"]));
		if (issetAndNotEmpty($_POST["txtEenheid"])) prepareAndExecuteStmt("credits.name.1", $_POST["txtEenheid"]);
		if (issetAndNotEmpty($_POST["txtMeervoud"])) prepareAndExecuteStmt("credits.name.x", $_POST["txtMeervoud"]);
		if (issetAndNotEmpty($_POST["txtOverdracht"])) prepareAndExecuteStmt("credits.name.overdracht", $_POST["txtOverdracht"]);

		/* ------------- */

		/* Mail */
		$test = FALSE;
		if (isset($_POST["chkSMTP"])) $test = TRUE;

		prepareAndExecuteStmt("mail.smtp", $test);

		if (isset($_POST["txtHost"])) prepareAndExecuteStmt("mail.Host", $_POST["txtHost"]);
		
		$test = FALSE;
		if (isset($_POST["chkAuth"])) $test = TRUE;

		prepareAndExecuteStmt("mail.SMTPAuth", $test);

		if (isset($_POST["txtSecure"])) prepareAndExecuteStmt("mail.SMTPSecure", $_POST["txtSecure"]);
		if (isset($_POST["txtPort"])) prepareAndExecuteStmt("mail.Port", intval($_POST["txtPort"]));
		if (isset($_POST["txtUsername"])) prepareAndExecuteStmt("mail.Username", $_POST["txtUsername"]);

		$pwd = null;

		if (issetAndNotEmpty($_POST["txtPasswd"])) {
			$pwd = $_POST["txtPasswd"];

			if ($pwd != settings("mail", "Password")) {
				$pwd = encryptor($pwd);
			}
		}

		prepareAndExecuteStmt("mail.Password", $pwd);

		/* ------------- */

		/* Facebook loginapp */
		if (isset($_POST["txtFbId"])) prepareAndExecuteStmt("facebook.loginapp.id", $_POST["txtFbId"]);
		if (isset($_POST["txtFbSecret"])) prepareAndExecuteStmt("facebook.loginapp.secret", $_POST["txtFbSecret"]);

		/* ------------- */

		/* Mail alert */
		if (isset($_POST["rbNMwhen"]) && isset($_POST["txtNewMessage"])) prepareAndExecuteStmt("mailalert.newmessage", periodInSeconds($_POST["rbNMwhen"], $_POST["txtNewMessage"]));

		if (isset($_POST["rbNSwhen"]) && isset($_POST["txtNewSub"])) prepareAndExecuteStmt("mailalert.newsubscription", periodInSeconds($_POST["rbNSwhen"], $_POST["txtNewSub"]));

		if (isset($_POST["txtPlatform"])) prepareAndExecuteStmt("mailalert.platform", intval($_POST["txtPlatform"]));

		if (isset($_POST["rbRSwhen"]) && isset($_POST["txtRemSub"])) prepareAndExecuteStmt("mailalert.remindersubscription", periodInSeconds($_POST["rbRSwhen"], $_POST["txtRemSub"]));

		if (isset($_POST["rbRUwhen"]) && isset($_POST["txtRemUnread"])) prepareAndExecuteStmt("mailalert.reminderunread", periodInSeconds($_POST["rbRUwhen"], $_POST["txtRemUnread"]));

		/* ------------- */

		redirect(filename());
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<? echo $oPage->getHeader(); ?>
	</head>
	<body id="index">
		<? echo $oPage->startTabs(); ?>
		<div class="body content">
			<div class="container">
				<div class="row">
					<? echo $oSecurity->me()->html("user.html"); ?>
				</div>
				<div class="main market admin">
					<? include "admin.menu.xml"; ?>
					<div id="inhoud">
						<h1>Configuratie paneel</h1>
						<div class="errors"></div>
						<form name="frmConfig" id="frmConfig" method="POST">
							<fieldset>
								<legend>Startwaarden</legend>
								<p>
									<label for="txtTemplateFolder">Template map:</label><br/>
									<input type="text" class="form-control" name="txtTemplateFolder" id="txtTemplateFolder" value="<? echo settings("domain", "templatefolder"); ?>"/>
								</p>
								<p>
									<label for="chkAlgemenevoorwaarden">Algemene voorwaarden</label>
									<input type="checkbox" name="chkAlgemenevoorwaarden" id="chkAlgemenevoorwaarden" value="algemenevoorwaarden" <? print((settings("startvalues", "algemenevoorwaarden") == TRUE) ? "checked='checked'" : ""); ?>/>
								</p>
								<p>
									<label for="chkVisibility">Profielen zichtbaar</label>
									<input type="checkbox" name="chkVisibility" id="chkVisibility" value="visibility" <?  print((settings("startvalues", "visibility") == TRUE) ? "checked='checked'" : ""); ?>/>
								</p>
								<p>
									<label for="txtAnalytics">Google analytics:</label><br/>
									<input type="text" class="form-control" name="txtAnalytics" id="txtAnalytics" value="<? echo settings("analytics"); ?>"/>
								</p>
							</fieldset>
							<fieldset>
								<legend>Debugging</legend>
								<p class="naastElkaar">
									<label for="chkShowwarnings">Waarschuwingen tonen</label>
									<input type="checkbox" name="chkShowwarnings" id="chkShowwarnings" value="showwarnings" <? print((settings("debugging", "showwarnings") == TRUE) ? "checked='checked'" : ""); ?>/>
								</p>
								<p class="naastElkaar">
									<label for="chkDemo">Demo</label>
									<input type="checkbox" name="chkDemo" id="chkDemo" value="demo" <? print((settings("debugging", "demo") == TRUE) ? "checked='checked'" : ""); ?>/>
								</p>
							</fieldset>
							<fieldset>
								<legend>Verzekeringen</legend>
								<p>
									<input type="text" class="form-control" name="txtV1" id="txtV1" value="<? echo settings("verzekeringen", "1"); ?>"/>
								</p>
								<p>
									<input type="text" class="form-control" name="txtV2" id="txtV2" value="<? echo settings("verzekeringen", "2"); ?>"/>
								</p>
							</fieldset>
							<fieldset>
								<legend>Tijdzone en lokatie</legend>
								<p class="naastElkaar">
									<label for="lstTimezone">Tijdzone:</label><br/>
									<select id="lstTimezone" name="lstTimezone" class="form-control">
									<?
										$zones = timezone_identifiers_list();

										foreach ($zones as $zone) {
											$place = explode("/", $zone);

											if (settings("date", "timezone") == $zone) {
												print("<option value='" . $zone . "' selected='selected'>" . $place[1] . "</option>");
											}
											else {
												if ($zone != "UTC") {
													print("<option value='" . $zone . "'>" . $place[1] . "</option>");
												}
											}
										}
									?>
									</select>
								</p>
								<p class="naastElkaar">
									<label for="txtLokatie">Standaard lokatie:</label><br/>
									<input type="text" class="form-control" name="txtLokatie" id="txtLokatie" value="<? echo coordinatesToAddress(settings("geo", "latitude"), settings("geo", "longitude")); ?>"/>
								</p>
							</fieldset>
							<fieldset class="credits">
								<legend>Credits</legend>
								<p class="naastElkaar">
									<label for="txtStart">Start:</label></br>
									<input type="number" class="form-control" name="txtStart" id="txtStart" min="0" max="<? echo settings("credits", "max"); ?>"  value="<? echo settings("startvalues", "credits"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtMin">Min:</label><br/>
									<input type="number" class="form-control" name="txtMin" id="txtMin" min="0" max="<? echo settings("credits", "max"); ?>" value="<? echo settings("credits", "min"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtMax">Max:</label><br/>
									<input type="number" class="form-control" name="txtMax" id="txtMax" min="0" value="<? echo settings("credits", "max"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtEenheid">Eenheid:</label><br/>
									<input type="text" class="form-control" name="txtEenheid" id="txtEenheid" value="<? echo settings("credits", "name", "1"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtMeervoud">Meervoud:</label><br/>
									<input type="text" class="form-control" name="txtMeervoud" id="txtMeervoud" value="<? echo settings("credits", "name", "x"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtOverdracht">Overdracht:</label><br/>
									<input type="text" class="form-control" name="txtOverdracht" id="txtOverdracht" value="<? echo settings("credits", "name", "overdracht"); ?>"/>
								</p>
							</fieldset>
							<fieldset>
								<legend>Mail</legend>
								<p>
									<label for="chkSMTP">SMTP</label>
									<input type="checkbox" name="chkSMTP" id="chkSMTP" value="smtp" <? print((settings("mail", "smtp") == TRUE) ? "checked='checked'" : ""); ?>/>
								</p>
								<p>
									<label for="txtHost">Host:</label><br/>
									<input type="text" class="form-control" name="txtHost" id="txtHost" value="<? echo settings("mail", "Host"); ?>"/>
								</p>
								<p>
									<label for="chkAuth">Authenticatie</label>
									<input type="checkbox" name="chkAuth" id="chkAuth" value="SMTPAuth" <? print((settings("mail", "SMTPAuth") == TRUE) ? "checked='checked'" : ""); ?>/>
								</p>
								<p>
									<label for="txtSecure">Secure:</label><br/>
									<input type="text" class="form-control" name="txtSecure" id="txtSecure" value="<? echo settings("mail", "SMTPSecure"); ?>"/>
								</p>
								<p>
									<label for="txtPort">Poort:</label><br/>
									<input type="number" class="form-control" name="txtPort" id="txtPort" min="0" max="65535" value="<? echo settings("mail", "Port"); ?>"/>
								</p>
								<p>
									<label for="txtUsername">Gebruikersnaam:</label><br/>
									<input type="text" class="form-control" name="txtUsername" id="txtUsername" value="<? echo settings("mail", "Username"); ?>"/>
								</p>
								<p>
									<label for="txtPasswd">Wachtwoord:</label><br/>
									<input type="password" class="form-control" name="txtPasswd" id="txtPasswd" value="<? echo settings("mail", "Password"); ?>"/>
								</p>
							</fieldset>
							<fieldset>
								<legend>Facebook loginapp</legend>
								<p class="naastElkaar">
									<label for="txtFbId">Id:</label><br/>
									<input type="text" class="form-control" name="txtFbId" id="txtFbId" value="<? echo settings("facebook", "loginapp", "id"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtFbSecret">Secret:</label><br/>
									<input type="text" class="form-control" name="txtFbSecret" id="txtFbSecret" value="<? echo settings("facebook", "loginapp", "secret"); ?>"/>
								</p>
							</fieldset>
							<fieldset id="mailalert">
								<legend>Mail alert</legend>
								<p>
									<label for="txtPlatform">Platform:</label><br/>
									<input type="number" class="form-control" name="txtPlatform" id="txtPlatform" min="0" value="<? echo settings("mailalert", "platform"); ?>"/>
								</p>
								<p class="naastElkaar tijdMail">
									<label for="txtNewMessage">Nieuw bericht:</label><br/>
									<input type="radio" name="rbNMwhen" id="rbNMDay" value="day" <? echo getPeriod(settings("mailalert", "newmessage"), "day"); ?>/><label for="rbNMDay">Dag</label>&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="rbNMwhen" id="rbNMWeek" value="week" <? echo getPeriod(settings("mailalert", "newmessage"), "week"); ?>/><label for="rbNMWeek">Week</label><br/>
									<input type="number" class="form-control" name="txtNewMessage" id="txtNewMessage" min="0" value="<? echo convertSeconds(settings("mailalert", "newmessage")); ?>"/>
								</p>
								<p class="naastElkaar tijdMail">
									<label for="txtNewSub">Nieuw aanbieding:</label><br/>
									<input type="radio" name="rbNSwhen" id="rbNSDay" value="day" <? echo getPeriod(settings("mailalert", "newsubscription"), "day"); ?>/><label for="rbNSDay">Dag</label>&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="rbNSwhen" id="rbNSWeek" value="week" <? echo getPeriod(settings("mailalert", "newsubscription"), "week"); ?>/><label for="rbNSWeek">Week</label><br/>
									<input type="number" class="form-control" name="txtNewSub" id="txtNewSub" min="0" value="<? echo convertSeconds(settings("mailalert", "newsubscription")); ?>"/>
								</p>
								<p class="naastElkaar tijdMail">
									<label for="txtRemSub">Herinnering aanbieding:</label><br/>
									<input type="radio" name="rbRSwhen" id="rbRSDay" value="day" <? echo getPeriod(settings("mailalert", "remindersubscription"), "day"); ?>/><label for="rbRSDay">Dag</label>&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="rbRSwhen" id="rbRSWeek" value="week" <? echo getPeriod(settings("mailalert", "remindersubscription"), "week"); ?>/><label for="rbRSWeek">Week</label><br/>
									<input type="number" class="form-control" name="txtRemSub" id="txtRemSub" min="0" value="<? echo convertSeconds(settings("mailalert", "remindersubscription")); ?>"/>
								</p>
								<p class="naastElkaar tijdMail">
									<label for="txtRemUnread">Ongelezen herinnering:</label><br/>
									<input type="radio" name="rbRUwhen" id="rbRUDay" value="day" <? echo getPeriod(settings("mailalert", "reminderunread"), "day"); ?>/><label for="rbRUDay">Dag</label>&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="rbRUwhen" id="rbRUWeek" value="week" <? echo getPeriod(settings("mailalert", "reminderunread"), "week"); ?>/><label for="rbRUWeek">Week</label><br/>
									<input type="number" class="form-control" name="txtRemUnread" id="txtRemUnread" min="0" value="<? echo convertSeconds(settings("mailalert", "reminderunread")); ?>"/>
								</p>
							</fieldset>
							<input type="submit" name="btnOpslaan" value="Opslaan" class="btn btn-default btn-save"/>	
						</form>
					</div>
				</div>
			</div>
			<? echo $oPage->endTabs(); ?>
		</div>
	<div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
	<script>
		function convertDayToWeek(d) {
			return (d % 7 == 0) ? d / 7 : 0;
		}

		function convertWeekToDay(w) {
			return ((w * 7) % 7 == 0) ? w * 7 : 0;
		}

		function convert(rb, txt) {
			var result = null;
			var txt = parseInt(txt);

			if (!isNaN(txt)) {
				if (rb == "day") {
					result = convertWeekToDay(txt);
				}
				else {
					result = convertDayToWeek(txt);
				}

				result = (result != 0) ? result : txt;
			}

			return result;
		}

		function enableDisableFields(state, fields) {
			var lenFields = fields.length;

			for (var i = 0; i < lenFields; i++) {
				if (!state) {
					fields[i].classList.remove("enabled");
					fields[i].classList.add("disabled");
				}
				else {
					fields[i].classList.remove("disabled");
					fields[i].classList.add("enabled");

					if (fields[i].classList.contains("fout")) {
						fields[i].classList.remove("enabled");
					}
				}

				if (fields[i].name == "chkAuth") {
					fields[i].disabled = !state;
				}
				else {
					fields[i].readOnly = !state;
				}
			}
		}

		document.addEventListener("DOMContentLoaded", function() {
			var chkSMTP = document.getElementById("chkSMTP");
			var txtHost = document.getElementById("txtHost");
			var chkAuth = document.getElementById("chkAuth");
			var txtSecure = document.getElementById("txtSecure");
			var txtPort = document.getElementById("txtPort");
			var txtUsername = document.getElementById("txtUsername");
			var txtPasswd = document.getElementById("txtPasswd");

			enableDisableFields(chkSMTP.checked,
				[txtHost, chkAuth]);

			enableDisableFields(chkAuth.checked,
				[txtSecure, txtPort, txtUsername,
				txtPasswd]);

			chkSMTP.addEventListener("click", function() {
				if (chkAuth.checked) {
					enableDisableFields(chkSMTP.checked,
						[txtHost, chkAuth, txtSecure,
						txtPort, txtUsername, txtPasswd]);
				}
				else {
					enableDisableFields(chkSMTP.checked,
						[txtHost, chkAuth]);
				}
			});

			chkAuth.addEventListener("click", function() {
				enableDisableFields(chkAuth.checked,
					[txtSecure, txtPort, txtUsername,
					txtPasswd]);
			});

			var txtStart = document.getElementById("txtStart");
			var txtMin = document.getElementById("txtMin");
			var txtMax = document.getElementById("txtMax");

			txtMax.addEventListener("blur", function() {
				txtStart.max = txtMax.value;
				txtMin.max = txtMax.value;
			});

			$("#mailalert input:radio").on("click", function() {
				if (this.name == "rbNMwhen") {
					var txtNewMessage = document.getElementById("txtNewMessage");
					var result = convert(this.value, txtNewMessage.value);

					txtNewMessage.value = (result != null) ? result : txtNewMessage.value;
				}
				else if (this.name == "rbNSwhen") {
					var txtNewSub = document.getElementById("txtNewSub");
					var result = convert(this.value, txtNewSub.value);

					txtNewSub.value = (result != null) ? result : txtNewSub.value;
				}
				else if (this.name == "rbRSwhen") {
					var txtRemSub = document.getElementById("txtRemSub");
					var result = convert(this.value, txtRemSub.value);

					txtRemSub.value = (result != null) ? result : txtRemSub.value;
				}
				else if (this.name == "rbRUwhen") {
					var txtRemUnread = document.getElementById("txtRemUnread");
					var result = convert(this.value, txtRemUnread.value);

					txtRemUnread.value = (result != null) ? result : txtRemUnread.value;
				}
			});
		});
	</script>
	</body>
</html>
