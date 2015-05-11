<?php
	include "inc.default.php"; // should be included in EVERY file

	$oSecurity = new security(TRUE);

	if (!$oSecurity->admin()) stop("admin");

	$oPage->addJS("script/admin.js");
	$oPage->addCSS("style/admin.css");

	function prepareAndExecuteStmt($key, $val, $dbPDO) {
		$query = "UPDATE `tblConfig` SET `value` = ? WHERE `key` LIKE ?";

		$stmt = $dbPDO->prepare($query);
		$stmt->bindParam(1, $val);
		$stmt->bindParam(2, $key);
		$stmt->execute();
	}

	function createDateTime($mkTime) {
		$dt = explode(",", $mkTime);
		$t = explode("(", $dt[0]);
		$dt[0] = $t[1];
		$dt[5] = substr($dt[5], 0, 5);

		$day = intval($dt[4]);
		$month = intval($dt[3]);
		$year = intval($dt[5]);

		$hour = intval($dt[0]);
		$minute = intval($dt[1]);

		$dateTime = array(
			"date" => array(
				"day" => $day,
				"month" => $month,
				"year" => $year
			),
			"time" => array(
				"hour" => $hour,
				"minute" => $minute,
				"second" => 0
			)
		);

		return $dateTime;
	}

	function makeTime($dt) {
		$date = $dt["date"];
		$time = $dt["time"];

		$mkTime = "mktime(" . $time["hour"] . "," . $time["minute"] . "," . $time["second"] . "," . $date["month"] . "," . $date["day"] . "," . $date["year"] .  ")";

		return $mkTime;
	}

	function getPeriod($formula, $from) {
		$digits = explode("*", $formula);

		$test = "";

		if ($from == "day" && $digits[1] == "24") $test = "checked='checked'";
		if ($from == "week" && $digits[1] == "168") $test = "checked='checked'";

		return $test;
	}

	function getCronsIndicator($formula) {
		$digits = explode("*", $formula);
		return $digits[0];
	}

	if (isset($_POST["btnOpslaan"])) {
		/* Startwaarden */
		if (isset($_POST["txtPhysical"])) prepareAndExecuteStmt("startvalues.physical", $_POST["txtPhysical"], $dbPDO);
		if (isset($_POST["txtSocial"])) prepareAndExecuteStmt("startvalues.social", $_POST["txtSocial"], $dbPDO);
		if (isset($_POST["txtMental"])) prepareAndExecuteStmt("startvalues.mental", $_POST["txtMental"], $dbPDO);
		if (isset($_POST["txtEmotional"])) prepareAndExecuteStmt("startvalues.emotional", $_POST["txtEmotional"], $dbPDO);

		/* ------------- */

		/* Levels */
		$i = 0;

		foreach (settings("levels") as $level) {
			if (isset($_POST["txtLevel" . $i . "Threshold"])) prepareAndExecuteStmt("levels." . $i . ".threshold", $_POST["txtLevel" . $i . "Threshold"], $dbPDO);
			if (isset($_POST["txtLevel" . $i . "Multiplier"])) prepareAndExecuteStmt("levels." . $i . ".multiplier", $_POST["txtLevel" . $i . "Multiplier"], $dbPDO);

			$i++;
		}

		/* ------------- */

		/* Warnings */
		$i = 1;

		foreach (settings("warnings") as $warning) {
			if (isset($_POST["txtW" . $i . "Schenkingen"])) prepareAndExecuteStmt("warnings." . $i . ".schenkingen", $_POST["txtW" . $i . "Schenkingen"], $dbPDO);
			if (isset($_POST["txtW" . $i . "Trans"])) prepareAndExecuteStmt("warnings." . $i . ".transactiediversiteit", $_POST["txtW" . $i . "Trans"], $dbPDO);
			if (isset($_POST["txtW" . $i . "Credits"])) prepareAndExecuteStmt("warnings." . $i . ".credits", $_POST["txtW" . $i . "Credits"], $dbPDO);
			if (isset($_POST["txtW" . $i . "Waardering"])) prepareAndExecuteStmt("warnings." . $i . ".waardering", $_POST["txtW" . $i . "Waardering"], $dbPDO);
			if (isset($_POST["txtW" . $i . "Physical"])) prepareAndExecuteStmt("warnings." . $i . ".physical", $_POST["txtW" . $i . "Physical"], $dbPDO);
			if (isset($_POST["txtW" . $i . "Social"])) prepareAndExecuteStmt("warnings." . $i . ".social", $_POST["txtW" . $i . "Social"], $dbPDO);
			if (isset($_POST["txtW" . $i . "Mental"])) prepareAndExecuteStmt("warnings." . $i . ".mental", $_POST["txtW" . $i . "Mental"], $dbPDO);
			if (isset($_POST["txtW" . $i . "Emotional"])) prepareAndExecuteStmt("warnings." . $i . ".emotional", $_POST["txtW" . $i . "Emotional"], $dbPDO);
			if (isset($_POST["txtW" . $i . "IndiSom"])) prepareAndExecuteStmt("warnings." . $i . ".indicatorsom", $_POST["txtW" . $i . "IndiSom"], $dbPDO);

			$i++;
		}

		/* ------------- */

		/* Crons */
		if (isset($_POST["rbWhen"]) && isset($_POST["txtCronsIndicators"])) {
			$period = $_POST["rbWhen"];
			$formula = $_POST["txtCronsIndicators"] . "*24*3600";

			if ($period == "week") $formula = $_POST["txtCronsIndicators"] . "*168*3600";

			prepareAndExecuteStmt("crons.indicators", $formula, $dbPDO);
		}

		if (isset($_POST["txtHTWFD"])) prepareAndExecuteStmt("crons.hourstoworkfordelay", $_POST["txtHTWFD"], $dbPDO);
		if (isset($_POST["txtX"])) prepareAndExecuteStmt("crons.x", $_POST["txtX"], $dbPDO);

		/* ------------- */

		/* Datum */
		if (isset($_POST["txtDateSpeed"])) prepareAndExecuteStmt("date.speed", $_POST["txtDateSpeed"], $dbPDO);
		if (isset($_POST["dDStart"]) && isset($_POST["dMStart"]) && isset($_POST["dYStart"]) && isset($_POST["tHStart"]) && isset($_POST["tMStart"])) {
			$dateTime = array(
				"date" => array(
					"day" => $_POST["dDStart"],
					"month" => $_POST["dMStart"],
					"year" => $_POST["dYStart"]
				),
				"time" => array(
					"hour" => $_POST["tHStart"],
					"minute" => $_POST["tMStart"],
					"second" => 0
				)
			);

			prepareAndExecuteStmt("date.start", makeTime($dateTime), $dbPDO);
		}

		/* ------------- */

		/* Indicatoren */
		if (isset($_POST["txtIndicatorMultiplier"])) prepareAndExecuteStmt("indicatoren.multiplier", $_POST["txtIndicatorMultiplier"], $dbPDO);
		if (isset($_POST["txtOwaesAdd"])) prepareAndExecuteStmt("indicatoren.owaesadd", $_POST["txtOwaesAdd"], $dbPDO);

		/* ------------- */

		redirect(filename());
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<? echo $oPage->getHeader(); ?>
		<style>
			input[type="range"] {
				width: 500px;
				height: 10px;
				-webkit-appearance: none;
			}

			input[type="range"]::-webkit-slider-thumb {
				-webkit-appearance: none;
				border: 0;
				border-radius: 50%;
				width: 18px;
				height: 18px;
				border: 1px solid #a0a0a0;
				background: #e4e4e4;
			}

			input::-moz-range-track {
				background: transparent;
				border: 0;
			}

			input[type="range"]::-ms-track {
				background: transparent;
				border-color: transparent;
				color: transparent;
			}

			input[type="range"]::-ms-thumb {
				border-radius: 50%;
				border: 2px solid #e4e4e4;
				background: #e4e4e4;
			}

			#txtPhysical::-ms-fill-upper, #txtPhysical::-ms-fill-lower {
				background-color: #ff3131;
			}

			#txtSocial::-ms-fill-upper, #txtSocial::-ms-fill-lower {
				background-color: #8dc63f;
			}

			#txtMental::-ms-fill-upper, #txtMental::-ms-fill-lower {
				background-color: #0072bc;
			}

			#txtEmotional::-ms-fill-upper, #txtEmotional::-ms-fill-lower {
				background-color: #ffcc00;
			}
		</style>
	</head>
	<body id="index">
		<? echo $oPage->startTabs(); ?>
		<div class="body">
			<div class="container">
				<div class="row">
					<? echo $oSecurity->me()->html("user.html"); ?>
				</div>
				<div class="main market admin">
					<? include "admin.menu.xml"; ?>
					<h1>Spel configuraties</h1>
					<form id="frmGameConfig" method="POST">
						<fieldset>
							<legend>Startwaarden</legend>
							<p>
								<label for="txtPhysical">Physical:</label>&nbsp;&nbsp;<span id="sPhy"></span>
								<input step="1" onchange="printValue('txtPhysical', 'sPhy')" style="background-color: #ff3131;" type="range" name="txtPhysical" id="txtPhysical" min="0" max="100" value="<? echo settings("startvalues", "physical"); ?>"/>
							</p>
							<p>
								<label for="txtSocial">Social:</label>&nbsp;&nbsp;<span id="sSoc"></span>
								<input step="1" onchange="printValue('txtSocial', 'sSoc')" style="background-color: #8dc63f;" type="range" name="txtSocial" id="txtSocial" min="0" max="100" value="<? echo settings("startvalues", "social"); ?>"/>
							</p>
							<p>
								<label for="txtMental">Mental:</label>&nbsp;&nbsp;<span id="sMen"></span>
								<input step="1" onchange="printValue('txtMental', 'sMen')" style="background-color: #0072bc;" type="range" name="txtMental" id="txtMental" min="0" max="100" value="<? echo settings("startvalues", "mental"); ?>"/>
							</p>
							<p>
								<label for="txtEmotional">Emotional:</label>&nbsp;&nbsp;<span id="sEmo"></span>
								<input step="1" onchange="printValue('txtEmotional', 'sEmo')" style="background-color: #ffcc00;" type="range" name="txtEmotional" id="txtEmotional" min="0" max="100" value="<? echo settings("startvalues", "emotional"); ?>"/>
							</p>
						</fieldset>
						<fieldset>
							<legend>Levels</legend>
							<?
								$i = 0;

								foreach (settings("levels") as $level) {
									?>
									<h2>Level <? echo $i; ?></h2>
									<p>
										<label for="txtLevel<? print($i . "Threshold"); ?>">Threshold:</label><br/>
										<input style="width: 75px;" type="number" name="txtLevel<?  print($i . "Threshold"); ?>" id="txtLevel<?  print($i . "Threshold"); ?>" value="<? echo $level["threshold"]; ?>"/>
									</p>
									<p>
										<label for="txtLevel<? print($i . "Multiplier"); ?>">Vermenigvuldigingsfactor:</label><br/>
										<input style="width: 75px;" type="number" name="txtLevel<?  print($i . "Multiplier"); ?>" id="txtLevel<?  print($i . "Multiplier"); ?>" value="<? echo $level["multiplier"]; ?>"/>
									</p>
									<?
									$i++;
								}
							?>
						</fieldset>
						<fieldset>
							<legend>Warnings</legend>
							<?
								$i = 1;

								foreach (settings("warnings") as $warning) {
									?>
									<h2>Warning <? echo $i; ?></h2>
									<p>
										<label for="txtW<? print($i . "Schenkingen"); ?>">Schenkingen:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Schenkingen"); ?>" id="txtW<? print($i . "Schenkingen"); ?>" value="<? echo $warning["schenkingen"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "Trans"); ?>">Transactiediversiteit:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Trans"); ?>" id="txtW<? print($i . "Trans"); ?>" value="<? echo $warning["transactiediversiteit"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "Credits"); ?>">Credits:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Credits"); ?>" id="txtW<? print($i . "Credits"); ?>" value="<? echo $warning["credits"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "Waardering"); ?>">Waardering:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Waardering"); ?>" id="txtW<? print($i . "Waardering"); ?>" value="<? echo $warning["waardering"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "Physical"); ?>">Physical:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Physical"); ?>" id="txtW<? print($i . "Physical"); ?>" value="<? echo $warning["physical"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "Social"); ?>">Social:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Social"); ?>" id="txtW<? print($i . "Social"); ?>" value="<? echo $warning["social"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "Mental"); ?>">Mental:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Mental"); ?>" id="txtW<? print($i . "Mental"); ?>" value="<? echo $warning["mental"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "Emotional"); ?>">Emotional:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "Emotional"); ?>" id="txtW<? print($i . "Emotional"); ?>" value="<? echo $warning["emotional"]; ?>"/>
									</p>
									<p>
										<label for="txtW<? print($i . "IndiSom"); ?>">Indicatorsom:</label><br/>
										<input style="width: 75px;" type="number" name="txtW<? print($i . "IndiSom"); ?>" id="txtW<? print($i . "IndiSom"); ?>" value="<? echo $warning["indicatorsom"]; ?>"/>
									</p>
									<?
									$i++;
								}
							?>
						</fieldset>
						<fieldset>
							<legend>Taken planner</legend>
							<p>
								<label for="txtCronsIndicators">Indicatoren verlagen:</label><br/>
								<input type="radio" name="rbWhen" value="day" <? echo getPeriod(settings("crons", "indicators"), "day"); ?>/>Dag&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" name="rbWhen" value="week" <? echo getPeriod(settings("crons", "indicators"), "week"); ?>/>Week<br/>
								<input type="number" name="txtCronsIndicators" id="txtCronsIndicators" min="0" value="<? echo getCronsIndicator(settings("crons", "indicators")); ?>"/>
							</p>
							<p>
								<label for="txtHTWFD">Aantal uren werken voor delay:</label><br/>
								<input type="number" name="txtHTWFD" id="txtHTWFD" value="<? echo settings("crons", "hourstoworkfordelay"); ?>"/>
							</p>
							<p>
								<label for="txtX">x</label><br/>
								<input type="number" name="txtX" id="txtX" value="<? echo settings("crons", "x"); ?>"/>
							</p>
						</fieldset>
						<fieldset>
							<legend>Datum</legend>
							<p>
								<label for="txtDateSpeed">Snelheid:</label><br/>
								<input type="number" name="txtDateSpeed" id="txtDateSpeed" value="<? echo settings("date", "speed"); ?>"/>
							</p>
							<p>
								<label for="dDStart">Start:</label><br/>
								<input type="number" name="dDStart" id="dDStart" min="1" max="31" value="<? echo createDateTime(settings("date", "start"))["date"]["day"]; ?>"/>/
								<input type="number" name="dMStart" id="dMStart" min="1" max="12" value="<? echo createDateTime(settings("date", "start"))["date"]["month"]; ?>"/>/
								<input type="number" name="dYStart" id="dYStart" min="2014" value="<? echo createDateTime(settings("date", "start"))["date"]["year"]; ?>"/>&nbsp;&nbsp;&nbsp;
								<input type="number" name="tHStart" id="tHStart" min="1" max="24" value="<? echo createDateTime(settings("date", "start"))["time"]["hour"]; ?>"/>:
								<input type="number" name="tMStart" id="tMStart" min="0" max="59" value="<? echo createDateTime(settings("date", "start"))["time"]["minute"]; ?>"/>
							</p>
						</fieldset>
						<fieldset>
							<legend>Indicatoren</legend>
							<p>
								<label for="txtIndicatorMultiplier">Vermenigvuldigingsfactor:</label><br/>
								<input type="number" name="txtIndicatorMultiplier" id="txtIndicatorMultiplier" value="<? echo settings("indicatoren", "multiplier"); ?>"/>
							</p>
							<p>
								<label for="txtOwaesAdd">Aantal toevoegen:</label><br/>
								<input type="number" name="txtOwaesAdd" id="txtOwaesAdd" value="<? echo settings("indicatoren", "owaesadd"); ?>"/>
							</p>
						</fieldset>
						<input type="submit" name="btnOpslaan" value="Opslaan" class="btn btn-default btn-save"/>
					</form>
				</div>
			</div>
			<? echo $oPage->endTabs(); ?>
		</div>
		<div class="footer">
			<? echo $oPage->footer(); ?>
		</div>
	<script>
		function printValue(sliderID, spanID) {
			var span = document.getElementById(spanID);
			var sliderID = document.getElementById(sliderID);

			span.innerHTML = sliderID.value;
		}

		window.addEventListener("DOMContentLoaded", function() {
			printValue("txtPhysical", "sPhy");
			printValue("txtSocial", "sSoc");
			printValue("txtMental", "sMen");
			printValue("txtEmotional", "sEmo");
		});
	</script>
	</body>
</html>
