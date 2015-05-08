<?php
	include "inc.default.php"; // should be included in EVERY file

	$oSecurity = new security(TRUE);

	if (!$oSecurity->admin()) stop("admin");

	$oPage->addJS("script/admin.js");
	$oPage->addCSS("style/admin.css");

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

	function prepareAndExecuteStmt($key, $val, $dbPDO) {
		$query = "UPDATE `tblConfig` SET `value` = ? WHERE `key` LIKE ?";

		$stmt = $dbPDO->prepare($query);
		$stmt->bindParam(1, $val);
		$stmt->bindParam(2, $key);
		$stmt->execute();
	}

	if (isset($_POST["btnOpslaan"])) {
		$dbPDO = new PDO("mysql:host=" . $arConfig["database"]["host"] . ";dbname=" . $arConfig["database"]["name"], $arConfig["database"]["user"], $arConfig["database"]["password"]);

		prepareAndExecuteStmt("startvalues.credits", $_POST["txtCredits"], $dbPDO);
		prepareAndExecuteStmt("startvalues.physical", $_POST["txtPhysical"], $dbPDO);
		prepareAndExecuteStmt("startvalues.social", $_POST["txtSocial"], $dbPDO);
		prepareAndExecuteStmt("startvalues.mental", $_POST["txtMental"], $dbPDO);
		prepareAndExecuteStmt("startvalues.emotional", $_POST["txtEmotional"], $dbPDO);

		$test = "FALSE";

		if (isset($_POST["chkVisibility"])) {
			$test = "TRUE";
		}

		prepareAndExecuteStmt("startvalues.visibility", $test, $dbPDO);
		prepareAndExecuteStmt("date.timezone", $_POST["lstTimezone"], $dbPDO);

		$coord = addressToCoordinates($_POST["txtLokatie"]);

		prepareAndExecuteStmt("geo.latitude", $coord["latitude"], $dbPDO);
		prepareAndExecuteStmt("geo.longitude", $coord["longitude"], $dbPDO);
		prepareAndExecuteStmt("credits.max", $_POST["txtMax"], $dbPDO);
		prepareAndExecuteStmt("credits.name.1", $_POST["txtEenheid"], $dbPDO);
		prepareAndExecuteStmt("credits.name.x", $_POST["txtMeervoud"], $dbPDO);
		prepareAndExecuteStmt("credits.name.overdracht", $_POST["txtOverdracht"], $dbPDO);

		$test = "FALSE";

		if (isset($_POST["chkSMTP"])) {
			$test = "TRUE";
		}

		prepareAndExecuteStmt("mail.smtp", $test, $dbPDO);

		if (isset($_POST["txtHost"])) prepareAndExecuteStmt("mail.Host", $_POST["txtHost"], $dbPDO);
		
		$test = "FALSE";

		if (isset($_POST["chkAuth"])) {
			$test = "TRUE";
		}

		prepareAndExecuteStmt("mail.SMTPAuth", $test, $dbPDO);

		if (isset($_POST["txtSecure"])) prepareAndExecuteStmt("mail.SMTPSecure", $_POST["txtSecure"], $dbPDO);
		if (isset($_POST["txtPort"])) prepareAndExecuteStmt("mail.Port", $_POST["txtPort"], $dbPDO);
		if (isset($_POST["txtUsername"])) prepareAndExecuteStmt("mail.Username", $_POST["txtUsername"], $dbPDO);

		$pwd = null;

		if (!empty($_POST["txtPasswd"])) {
			$query = "SELECT `value` FROM `tblConfig` WHERE `key` LIKE 'mail.Password'";
			$result = $dbPDO->query($query);
			$pwd = $_POST["txtPasswd"];

			foreach ($result as $p) {
				if ($pwd != $p["value"]) {
					$pwd = encryptor($pwd);
				}
			}
		}

		prepareAndExecuteStmt("mail.Password", $pwd, $dbPDO);

		redirect(filename());
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php echo $oPage->getHeader(); ?>
	</head>
	<body id="index">
		<?php echo $oPage->startTabs(); ?>
		<div class="body">
			<div class="container">
				<div class="row">
					<?php echo $oSecurity->me()->html("user.html"); ?>
				</div>
				<div class="main market admin">
					<?php include "admin.menu.xml"; ?>
					<h1>Configuratie paneel</h1>
					<form id="frmConfig" method="POST">
						<fieldset>
							<legend>Start waardes</legend>
							<p>
								<label for="txtCredits">Credits:</label><br/>
								<input type="number" name="txtCredits" id="txtCredits" value="<?php echo settings("startvalues", "credits"); ?>"/>
							</p>
							<p>
								<label for="txtPhysical">Physical:</label><br/>
								<input type="number" name="txtPhysical" id="txtPhysical" value="<?php echo settings("startvalues", "physical"); ?>"/>
							</p>
							<p>
								<label for="txtSocial">Social:</label><br/>
								<input type="number" name="txtSocial" id="txtSocial" value="<?php echo settings("startvalues", "social"); ?>"/>
							</p>
							<p>
								<label for="txtMental">Mental:</label><br/>
								<input type="number" name="txtMental" id="txtMental" value="<?php echo settings("startvalues", "mental"); ?>"/>
							</p>
							<p>
								<label for="txtEmotional">Emotional:</label><br/>
								<input type="number" name="txtEmotional" id="txtEmotional" value="<?php echo settings("startvalues", "emotional"); ?>"/>
							</p>
							<p>
								<label for="chkVisibility">Visibility</label>
								<input type="checkbox" name="chkVisibility" id="chkVisibility" value="visibility" <?php  print((settings("startvalues", "visibility") == "TRUE") ? "checked='checked'" : ""); ?>/>
							</p>
						</fieldset>
						<fieldset>
							<legend>Tijdzone en lokatie</legend>
							<p>
								<label for="lstTimezone">Tijdzone:</label><br/>
								<select id="lstTimezone" name="lstTimezone">
								<?php
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
							<p>
								<label for="txtLokatie">Lokatie:</label><br/>
								<input type="text" name="txtLokatie" id="txtLokatie" value="<?php echo coordinatesToAddress(settings("geo", "latitude"), settings("geo", "longitude")); ?>"/>
							</p>
						</fieldset>
						<fieldset>
							<legend>Credits</legend>
							<p>
								<label for="txtMax">Max:</label><br/>
								<input type="number" name="txtMax" id="txtMax" value="<?php echo settings("credits", "max"); ?>"/>
							</p>
							<p>
								<label for="txtEenheid">Eenheid:</label><br/>
								<input type="text" name="txtEenheid" id="txtEenheid" value="<?php echo settings("credits", "name", "1"); ?>"/>
							</p>
							<p>
								<label for="txtMeervoud">Meervoud:</label><br/>
								<input type="text" name="txtMeervoud" id="txtMeervoud" value="<?php echo settings("credits", "name", "x"); ?>"/>
							</p>
							<p>
								<label for="txtOverdracht">Overdracht:</label><br/>
								<input type="text" name="txtOverdracht" id="txtOverdracht" value="<?php echo settings("credits", "name", "overdracht"); ?>"/>
							</p>
						</fieldset>
						<fieldset>
							<legend>Mail</legend>
							<p>
								<label for="chkSMTP">SMTP</label>
								<input type="checkbox" name="chkSMTP" id="chkSMTP" value="smtp" <?php print((settings("mail", "smtp") == "TRUE") ? "checked='checked'" : ""); ?>/>
							</p>
							<p>
								<label for="txtHost">Host:</label><br/>
								<input type="text" name="txtHost" id="txtHost" value="<?php echo settings("mail", "Host"); ?>"/>
							</p>
							<p>
								<label for="chkAuth">Authentication</label>
								<input type="checkbox" name="chkAuth" id="chkAuth" value="SMTPAuth" <?php print((settings("mail", "SMTPAuth") == "TRUE") ? "checked='checked'" : ""); ?>/>
							</p>
							<p>
								<label for="txtSecure">Secure:</label><br/>
								<input type="text" name="txtSecure" id="txtSecure" value="<?php echo settings("mail", "SMTPSecure"); ?>"/>
							</p>
							<p>
								<label for="txtPort">Port:</label><br/>
								<input type="number" name="txtPort" id="txtPort" value="<?php echo settings("mail", "Port"); ?>"/>
							</p>
							<p>
								<label for="txtUsername">Username:</label><br/>
								<input type="text" name="txtUsername" id="txtUsername" value="<?php echo settings("mail", "Username"); ?>"/>
							</p>
							<p>
								<label for="txtPasswd">Password:</label><br/>
								<input type="password" name="txtPasswd" id="txtPasswd" value="<?php echo settings("mail", "Password"); ?>"/>
							</p>
						</fieldset>
						<input type="submit" name="btnOpslaan" value="Opslaan" class="btn btn-default btn-save"/>
					</form>
				</div>
			</div>
			<?php echo $oPage->endTabs(); ?>
		</div>
	<div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
	<script>
		function enableDisableFields(state, fields) {
			var lenFields = fields.length;

			for (var i = 0; i < lenFields; i++) {
				if (!state) {
					fields[i].style.background = "#dadada";
				}
				else {
					fields[i].style.background = "#ffffff";
				}

				if (fields[i].name == "chkAuth") {
					fields[i].disabled = !state;
				}
				else {
					fields[i].readOnly = !state;
				}
			}
		}

		var chkSMTP = document.getElementById("chkSMTP");
		var txtHost = document.getElementById("txtHost");
		var chkAuth = document.getElementById("chkAuth");
		var txtSecure = document.getElementById("txtSecure");
		var txtPort = document.getElementById("txtPort");
		var txtUsername = document.getElementById("txtUsername");
		var txtPasswd = document.getElementById("txtPasswd");

		var fields = [txtHost, chkAuth, txtSecure,
			txtPort, txtUsername, txtPasswd];

		enableDisableFields(chkSMTP.checked, fields);

		chkSMTP.addEventListener("click", function() {
			enableDisableFields(chkSMTP.checked, fields);
		});
	</script>
	</body>
</html>
