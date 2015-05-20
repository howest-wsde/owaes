<?php
	include "inc.default.php"; // should be included in EVERY file

	$oSecurity = new security(TRUE);

	if (!$oSecurity->admin()) stop("admin");

	$oPage->addJS("script/admin.js");
	$oPage->addCSS("style/admin.css");

	$oPage->addJS("script/gamiVal.js");

	function prepareAndExecuteStmt($key, $val, $dbPDO) {
		$query = "UPDATE `tblConfig` SET `value` = ? WHERE `key` LIKE ?";

		$stmt = $dbPDO->prepare($query);
		$stmt->bindParam(1, json_encode($val));
		$stmt->bindParam(2, $key);
		$stmt->execute();
	}

	function createDateTime($unixTimestamp) {
		date_default_timezone_set('UTC');

		$date = date('d/m/Y', $unixTimestamp);
		$time = date('H:i', $unixTimestamp);

		$d = explode('/', $date);
		$t = explode(':', $time);

		$day = intval($d[0]);
		$month = intval($d[1]);
		$year = intval($d[2]);

		$hour = intval($t[0]);
		$minute = intval($t[1]);

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

	function makeUnixTimestamp($dt) {
		date_default_timezone_set('UTC');

		$date = $dt["date"];
		$time = $dt["time"];

		$unixTimestamp = mktime($time["hour"], $time["minute"], $time["second"], $date["month"], $date["day"], $date["year"]);

		return intval($unixTimestamp);
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

	function getCronsIndicator($seconds) {
		$hours = $seconds / 3600;
		$days = $hours / 24;
		$weeks = $days / 7;

		return (is_int($weeks)) ? $weeks : $days;
	}

	if (isset($_POST["btnOpslaan"])) {
		/* Startwaarden */
		if (isset($_POST["txtPhysical"])) prepareAndExecuteStmt("startvalues.physical", intval($_POST["txtPhysical"]), $dbPDO);
		if (isset($_POST["txtSocial"])) prepareAndExecuteStmt("startvalues.social", intval($_POST["txtSocial"]), $dbPDO);
		if (isset($_POST["txtMental"])) prepareAndExecuteStmt("startvalues.mental", intval($_POST["txtMental"]), $dbPDO);
		if (isset($_POST["txtEmotional"])) prepareAndExecuteStmt("startvalues.emotional", intval($_POST["txtEmotional"]), $dbPDO);

		/* ------------- */

		/* Levels */
		$i = 0;

		foreach ($arConfig["levels"] as $level) {
			if (isset($_POST["txtLevel" . $i . "Threshold"])) prepareAndExecuteStmt("levels." . $i . ".threshold", doubleval($_POST["txtLevel" . $i . "Threshold"]), $dbPDO);
			if (isset($_POST["txtLevel" . $i . "Multiplier"])) prepareAndExecuteStmt("levels." . $i . ".multiplier", doubleval($_POST["txtLevel" . $i . "Multiplier"]), $dbPDO);

			$i++;
		}

		/* ------------- */

		/* Warnings */
		$i = 1;

		foreach ($arConfig["warnings"] as $warning) {
			if (isset($_POST["txtW" . $i . "Schenkingen"])) prepareAndExecuteStmt("warnings." . $i . ".schenkingen", doubleval($_POST["txtW" . $i . "Schenkingen"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "Trans"])) prepareAndExecuteStmt("warnings." . $i . ".transactiediversiteit", doubleval($_POST["txtW" . $i . "Trans"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "Credits"])) prepareAndExecuteStmt("warnings." . $i . ".credits", doubleval($_POST["txtW" . $i . "Credits"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "Waardering"])) prepareAndExecuteStmt("warnings." . $i . ".waardering", doubleval($_POST["txtW" . $i . "Waardering"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "Physical"])) prepareAndExecuteStmt("warnings." . $i . ".physical", intval($_POST["txtW" . $i . "Physical"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "Social"])) prepareAndExecuteStmt("warnings." . $i . ".social", intval($_POST["txtW" . $i . "Social"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "Mental"])) prepareAndExecuteStmt("warnings." . $i . ".mental", intval($_POST["txtW" . $i . "Mental"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "Emotional"])) prepareAndExecuteStmt("warnings." . $i . ".emotional", intval($_POST["txtW" . $i . "Emotional"]), $dbPDO);
			if (isset($_POST["txtW" . $i . "IndiSom"])) prepareAndExecuteStmt("warnings." . $i . ".indicatorsom", intval($_POST["txtW" . $i . "IndiSom"]), $dbPDO);

			$i++;
		}

		/* ------------- */

		/* Crons */
		if (isset($_POST["rbWhen"]) && isset($_POST["txtCronsIndicators"])) {
			$period = $_POST["rbWhen"];
			$result = intval($_POST["txtCronsIndicators"]) * 24 * 3600;

			if ($period == "week") $result = intval($_POST["txtCronsIndicators"]) * 168 * 3600;

			prepareAndExecuteStmt("crons.indicators", $result, $dbPDO);
		}

		if (isset($_POST["txtHTWFD"])) prepareAndExecuteStmt("crons.hourstoworkfordelay", doubleval($_POST["txtHTWFD"]), $dbPDO);
		if (isset($_POST["txtX"])) prepareAndExecuteStmt("crons.x", doubleval($_POST["txtX"]), $dbPDO);

		/* ------------- */

		/* Datum */
		if (isset($_POST["txtDateSpeed"])) prepareAndExecuteStmt("date.speed", doubleval($_POST["txtDateSpeed"]), $dbPDO);
		if (isset($_POST["dDStart"]) && isset($_POST["dMStart"]) && isset($_POST["dYStart"]) && isset($_POST["tHStart"]) && isset($_POST["tMStart"])) {
			$dateTime = array(
				"date" => array(
					"day" => intval($_POST["dDStart"]),
					"month" => intval($_POST["dMStart"]),
					"year" => intval($_POST["dYStart"])
				),
				"time" => array(
					"hour" => intval($_POST["tHStart"]),
					"minute" => intval($_POST["tMStart"]),
					"second" => 0
				)
			);

			prepareAndExecuteStmt("date.start", makeUnixTimestamp($dateTime), $dbPDO);
		}

		/* ------------- */

		/* Indicatoren */
		if (isset($_POST["txtIndicatorMultiplier"])) prepareAndExecuteStmt("indicatoren.multiplier", doubleval($_POST["txtIndicatorMultiplier"]), $dbPDO);
		if (isset($_POST["txtOwaesAdd"])) prepareAndExecuteStmt("indicatoren.owaesadd", doubleval($_POST["txtOwaesAdd"]), $dbPDO);

		/* ------------- */

		redirect(filename());
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<? echo $oPage->getHeader(); ?>
		<style>
			h1 {
				font-size: 20pt;
			}

			h2 {
				font-size: 13pt;
			}

			legend {
				font-size: 12pt;
			}

			fieldset {
				padding: 0 0 30px 0;
				width: 800px;
			}

			fieldset > p, fieldset > div {
				padding: 0 0 0 20px;
			}

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
					<div id="inhoud">
						<h1>Spel configuraties</h1>
						<div class="errors"></div>
						<form name="frmGameConfig" id="frmGameConfig" method="POST">
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

									foreach ($arConfig["levels"] as $level) {
	?>
										<div class="naastElkaar">
										<h2>Level <? echo $i; ?></h2>
										<p>
											<label for="txtLevel<? print($i . "Threshold"); ?>">Threshold:</label><br/>
											<input style="width: 75px;" type="number" name="txtLevel<?  print($i . "Threshold"); ?>" id="txtLevel<?  print($i . "Threshold"); ?>" min="0" step="0.001"  value="<? echo $level["threshold"]; ?>"/>
										</p>
										<p>
											<label for="txtLevel<? print($i . "Multiplier"); ?>">Vermenigvuldigingsfactor:</label><br/>
											<input style="width: 75px;" type="number" name="txtLevel<?  print($i . "Multiplier"); ?>" id="txtLevel<?  print($i . "Multiplier"); ?>" min="0" step="0.001" value="<? echo $level["multiplier"]; ?>"/>
										</p>
										</div>
										<?
										$i++;
									}
									?>
							</fieldset>
							<fieldset>
								<legend>Warnings</legend>
								<?
									$i = 1;

									foreach ($arConfig["warnings"] as $warning) {
	?>
										<div class="naastElkaar">
										<h2>Warning <? echo $i; ?></h2>
										<p>
											<label for="txtW<? print($i . "Schenkingen"); ?>">Schenkingen:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Schenkingen"); ?>" id="txtW<? print($i . "Schenkingen"); ?>" min="0" step="0.001" value="<? echo $warning["schenkingen"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "Trans"); ?>">Transactiediversiteit:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Trans"); ?>" id="txtW<? print($i . "Trans"); ?>" min="0" step="0.001" value="<? echo $warning["transactiediversiteit"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "Credits"); ?>">Credits:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Credits"); ?>" id="txtW<? print($i . "Credits"); ?>" min="0" step="0.001" value="<? echo $warning["credits"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "Waardering"); ?>">Waardering:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Waardering"); ?>" id="txtW<? print($i . "Waardering"); ?>" min="0" step="0.001" value="<? echo $warning["waardering"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "Physical"); ?>">Physical:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Physical"); ?>" id="txtW<? print($i . "Physical"); ?>" min="0" step="0.001" value="<? echo $warning["physical"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "Social"); ?>">Social:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Social"); ?>" id="txtW<? print($i . "Social"); ?>" min="0" step="0.001" value="<? echo $warning["social"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "Mental"); ?>">Mental:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Mental"); ?>" id="txtW<? print($i . "Mental"); ?>" min="0" step="0.001" value="<? echo $warning["mental"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "Emotional"); ?>">Emotional:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "Emotional"); ?>" id="txtW<? print($i . "Emotional"); ?>" min="0" step="0.001" value="<? echo $warning["emotional"]; ?>"/>
										</p>
										<p>
											<label for="txtW<? print($i . "IndiSom"); ?>">Indicatorsom:</label><br/>
											<input style="width: 75px;" type="number" name="txtW<? print($i . "IndiSom"); ?>" id="txtW<? print($i . "IndiSom"); ?>" min="0" step="0.001" value="<? echo $warning["indicatorsom"]; ?>"/>
										</p>
										</div>
										<?
										$i++;
									}
									?>
							</fieldset>
							<fieldset>
								<legend>Taken planner</legend>
								<p class="naastElkaar">
									<label for="txtCronsIndicators">Indicatoren verlagen:</label><br/>
									<input type="radio" name="rbWhen" id="rbDay" value="day" <? echo getPeriod(settings("crons", "indicators"), "day"); ?>/><label for="rbDay">Dag</label>&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="rbWhen" id="rbWeek" value="week" <? echo getPeriod(settings("crons", "indicators"), "week"); ?>/><label for="rbWeek">Week</label><br/>
									<input type="number" name="txtCronsIndicators" id="txtCronsIndicators" min="0" value="<? echo getCronsIndicator(settings("crons", "indicators")); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtHTWFD">Aantal uren werken voor delay:</label><br/>
									<input type="number" name="txtHTWFD" id="txtHTWFD" value="<? echo settings("crons", "hourstoworkfordelay"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtX">x</label><br/>
									<input type="number" name="txtX" id="txtX" value="<? echo settings("crons", "x"); ?>"/>
								</p>
							</fieldset>
							<fieldset>
								<legend>Datum</legend>
								<p class="naastElkaar">
									<label for="txtDateSpeed">Snelheid:</label><br/>
									<input type="number" name="txtDateSpeed" id="txtDateSpeed" min="0" value="<? echo settings("date", "speed"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="dDStart">Start:</label><br/>
									<input type="number" name="dDStart" id="dDStart" min="1" max="31" value="<? echo createDateTime(settings("date", "start"))["date"]["day"]; ?>"/>/
									<input type="number" name="dMStart" id="dMStart" min="1" max="12" value="<? echo createDateTime(settings("date", "start"))["date"]["month"]; ?>"/>/
									<input type="number" name="dYStart" id="dYStart" min="2014" value="<? echo createDateTime(settings("date", "start"))["date"]["year"]; ?>"/>&nbsp;&nbsp;&nbsp;
									<input type="number" name="tHStart" id="tHStart" min="0" max="23" value="<? echo createDateTime(settings("date", "start"))["time"]["hour"]; ?>"/>:
									<input type="number" name="tMStart" id="tMStart" min="0" max="59" value="<? echo createDateTime(settings("date", "start"))["time"]["minute"]; ?>"/>
								</p>
							</fieldset>
							<fieldset>
								<legend>Indicatoren</legend>
								<p class="naastElkaar">
									<label for="txtIndicatorMultiplier">Vermenigvuldigingsfactor:</label><br/>
									<input type="number" name="txtIndicatorMultiplier" id="txtIndicatorMultiplier" min="0" value="<? echo settings("indicatoren", "multiplier"); ?>"/>
								</p>
								<p class="naastElkaar">
									<label for="txtOwaesAdd">Aantal toevoegen:</label><br/>
									<input type="number" name="txtOwaesAdd" id="txtOwaesAdd" min="0" value="<? echo settings("indicatoren", "owaesadd"); ?>"/>
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
