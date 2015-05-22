// admin.gamification.php Validatie
$(document).ready(function() {
	$("form#frmGameConfig").submit(function() {
		var valid = validateAddActivity(true);

		if (valid) {
			if (confirm("Wilt u deze instellingen opslaan?")) {
				return true;
			}
		}

		return false;
	});

	$("form#frmGameConfig :input").on("blur", function() {
		return validateAddActivity(false);
	}).on("focus", function() {
		$(this).removeClass("fout");
	});
});

function resetArrayIndex(arr) {
	arr = arr.filter(function () { return true; });
	return arr;
}

function validateAddActivity(bShowAlerts) {
	arFouten = {};
	arMessage = [];
	var $message = "";

	var $txtLevels = [];
	var $txtWarnings = [];

	var i = 0;
	$("input[name^='txtLevel'").each(function() {
		$txtLevels[i] = $(this).val();
		i++;
	});

	var i = 0;
	$("input[name^='txtW'").each(function() {
		$txtWarnings[i] = $(this).val();
		i++;
	});

	var $txtCronsIndicators = $("#txtCronsIndicators").val();
	var $txtHTWFD = $("#txtHTWFD").val();
	var $txtDateSpeed = $("#txtDateSpeed").val();
	var $dDStart = $("#dDStart").val();
	var $dMStart = $("#dMStart").val();
	var $dYStart = $("#dYStart").val();
	var $tHStart = $("#tHStart").val();
	var $tMStart = $("#tMStart").val();
	var $txtIndicatorMultiplier = $("#txtIndicatorMultiplier").val();
	var $txtOwaesAdd = $("#txtOwaesAdd").val();
	
	var lenLevels = $txtLevels.length;
	var lenWarnings = $txtWarnings.length;

	var thresholds = [];
	var multipliers = [];

	var schenkingen = [];
	var transacties = []; // Transactiediversiteiten
	var credits = [];
	var waarderingen = [];
	var physicals = [];
	var socials = [];
	var mentals = [];
	var emotionals = [];
	var indisoms = [];

	for (var i = 0; i < lenLevels; i++) {
		if (i % 2 == 0) thresholds[i] = $txtLevels[i];
		else multipliers[i] = $txtLevels[i];
	}

	thresholds = resetArrayIndex(thresholds);
	multipliers = resetArrayIndex(multipliers);

	var aantalLevels = thresholds.length;

	for (var i = 0; i < aantalLevels; i++) {
		if (thresholds[i] == "") {
			arFouten["txtLevel" + i + "Threshold"] = "Gelieve een drempel mee te geven voor level " + i + ".";
		}

		if (thresholds[i] < 0) {
			arFouten["txtLevel" + i + "Threshold"] = "Gelieve een geldig drempel mee te geven voor level " + i + ".";
		}

		if (multipliers[i] == "") {
			arFouten["txtLevel" + i + "Multiplier"] = "Gelieve een vermenigvuldigingsfactor mee te geven voor level " + i + ".";
		}

		if (multipliers[i] < 0) {
			
			arFouten["txtLevel" + i + "Multiplier"] = "Gelieve een geldig vermenigvuldigingsfactor mee te geven voor level " + i + ".";
		}
	}

	for (var i = 0; i < lenWarnings; i++) {
		if (i % 9 == 0) schenkingen[i] = $txtWarnings[i];
		else if (i % 9 == 1) transacties[i] = $txtWarnings[i];
		else if (i % 9 == 2) credits[i] = $txtWarnings[i];
		else if (i % 9 == 3) waarderingen[i] = $txtWarnings[i];
		else if (i % 9 == 4) physicals[i] = $txtWarnings[i];
		else if (i % 9 == 5) socials[i] = $txtWarnings[i];
		else if (i % 9 == 6) mentals[i] = $txtWarnings[i];
		else if (i % 9 == 7) emotionals[i] = $txtWarnings[i];
		else if (i % 9 == 8) indisoms[i] = $txtWarnings[i];
	}

	schenkingen = resetArrayIndex(schenkingen);
	transacties = resetArrayIndex(transacties);
	credits = resetArrayIndex(credits);
	waarderingen = resetArrayIndex(waarderingen);
	physicals = resetArrayIndex(physicals);
	socials = resetArrayIndex(socials);
	mentals = resetArrayIndex(mentals);
	emotionals = resetArrayIndex(emotionals);
	indisoms = resetArrayIndex(indisoms);

	var aantalWarnings = schenkingen.length;

	for (var i = 0; i < aantalWarnings; i++) {
		if (schenkingen[i] == "") {
			arFouten["txtW" + (i + 1) + "Schenkingen"] = "Gelieve een nummer in te geven voor schenkingen in warning " + (i + 1) + ".";
		}

		if (schenkingen[i] < 0) {
			arFouten["txtW" + (i + 1) + "Schenkingen"] = "Gelieve een geldig nummer in te geven voor schenkingen in warning " + (i + 1) + ".";
		}

		if (transacties[i] == "") {
			arFouten["txtW" + (i + 1) + "Trans"] = "Gelieve een nummer in te geven voor transactiediversiteit in warning " + (i + 1) + ".";
		}

		if (transacties[i] < 0) {
			arFouten["txtW" + (i + 1) + "Trans"] = "Gelieve een geldig nummer in te geven voor transactiediversiteit in warning " + (i + 1) + ".";
		}

		if (credits[i] == "") {
			arFouten["txtW" + (i + 1) + "Credits"] = "Gelieve een nummer in te geven voor credits in warning " + (i + 1) + ".";
		}

		if (credits[i] < 0) {
			arFouten["txtW" + (i + 1) + "Credits"] = "Gelieve een geldig nummer in te geven voor credits in warning " + (i + 1) + ".";
		}

		if (waarderingen[i] == "") {
			arFouten["txtW" + (i + 1) + "Waardering"] = "Gelieve een nummer in te geven voor waardering in warning " + (i + 1) + ".";
		}

		if (waarderingen[i] < 0) {
			arFouten["txtW" + (i + 1) + "Waardering"] = "Gelieve een geldig nummer in te geven voor waardering in warning " + (i + 1) + ".";
		}

		if (physicals[i] == "") {
			arFouten["txtW" + (i + 1) + "Physical"] = "Gelieve een nummer in te geven voor fysiek in warning " + (i + 1) + ".";
		}

		if (physicals[i] < 0) {
			arFouten["txtW" + (i + 1) + "Physical"] = "Gelieve een geldig nummer in te geven voor fysiek in warning " + (i + 1) + ".";
		}

		if (socials[i] == "") {
			arFouten["txtW" + (i + 1) + "Social"] = "Gelieve een nummer in te geven voor sociaal in warning " + (i + 1) + ".";
		}

		if (socials[i] < 0) {
			arFouten["txtW" + (i + 1) + "Social"] = "Gelieve een geldig nummer in te geven voor sociaal in warning " + (i + 1) + ".";
		}

		if (mentals[i] == "") {
			arFouten["txtW" + (i + 1) + "Mental"] = "Gelieve een nummer in te geven voor kennis in warning " + (i + 1) + ".";
		}

		if (mentals[i] < 0) {
			arFouten["txtW" + (i + 1) + "Mental"] = "Gelieve een geldig nummer in te geven voor kennis in warning " + (i + 1) + ".";
		}

		if (emotionals[i] == "") {
			arFouten["txtW" + (i + 1) + "Emotional"] = "Gelieve een nummer in te geven voor welzijn in warning " + (i + 1) + ".";
		}

		if (emotionals[i] < 0) {
			arFouten["txtW" + (i + 1) + "Emotional"] = "Gelieve een geldig nummer in te geven voor welzijn in warning " + (i + 1) + ".";
		}

		if (indisoms[i] == "") {
			arFouten["txtW" + (i + 1) + "IndiSom"] = "Gelieve een nummer in te geven voor indicatorsom in warning " + (i + 1) + ".";
		}

		if (indisoms[i] < 0) {
			arFouten["txtW" + (i + 1) + "IndiSom"] = "Gelieve een geldig nummer in te geven voor indicatorsom in warning " + (i + 1) + ".";
		}
	}

	if ($txtCronsIndicators == "") {
		arFouten["txtCronsIndicators"] = "Gelieve het aantal dagen of weken mee te geven.";
	}

	if ($txtCronsIndicators < 1) {
		arFouten["txtCronsIndicators"] = "Het aantal dagen of weken kan niet kleiner zijn dan 1.";
	}

	if ($txtHTWFD == "") {
		arFouten["txtHTWFD"] = "Gelieve het aantal te werken uren voor delay in te vullen.";
	}

	if ($txtHTWFD < 0) {
		arFouten["txtHTWFD"] = "Het aantal te werken uren voor delay kan niet kleiner zijn dan 0.";
	}

	if ($txtDateSpeed == "") {
		arFouten["txtDateSpeed"] = "Gelieve een datum snelheid mee te geven.";
	}

	if ($dDStart == "") {
		arFouten["dDStart"] = "Gelieve een dag mee te geven.";
	}

	if ($dDStart < 1 || $dDStart > 31) {
		arFouten["dDStart"] = "Gelieve een geldig dag mee te geven.";
	}

	if ($dMStart == "") {
		arFouten["dMStart"] = "Gelieve een maand mee te geven.";
	}

	if ($dMStart < 1 || $dMStart > 12) {
		arFouten["dMStart"] = "Gelieve een geldig maand mee te geven.";
	}

	if ($dYStart == "") {
		arFouten["dYStart"] = "Gelieve een jaartal mee te geven.";
	}

	if ($dYStart < (new Date().getFullYear - 1) && $dYStart > new Date().getFullYear) {
		arFouten["dYStart"] = "Gelieve een geldig jaartal mee te geven.";
	}

	if ($tHStart == "") {
		arFouten["tHStart"] = "Gelieve een uur mee te geven.";
	}

	if ($tHStart < 0 || $tHStart > 23) {
		arFouten["tHStart"] = "Gelieve een geldig uur mee te geven.";
	}

	if ($tMStart == "") {
		arFouten["tMStart"] = "Gelieve een minuut mee te geven.";
	}

	if ($tMStart < 0 || $tMStart > 59) {
		arFouten["tMStart"] = "Gelieve een geldig minuut mee te geven.";
	}

	if ($txtIndicatorMultiplier == "") {
		arFouten["txtIndicatorMultiplier"] = "Gelieve een indicator vermenigvuldigingsfactor mee te geven.";
	}

	if ($txtOwaesAdd == "") {
		arFouten["txtOwaesAdd"] = "Gelieve een aantal mee te geven.";
	}

	if ($txtOwaesAdd < 0) {
		arFouten["txtOwaesAdd"] = "Gelieve een geldig aantal mee te geven.";
	}

	if (Object.keys(arFouten).length > 0) {
		$.each(arFouten, function(strID, strFout) {
			$message += "<li class='error'>" + strFout + "</li>";
			$("#" + strID).addClass("fout");

			if (bShowAlerts || $("#" + strID).hasClass("gepasseerd")) {
				arMessage[arMessage.length] = "<li class='error'>" + strFout + "</li>";
			}
		});

		if (arMessage.length > 0) {
			$message = "<div class='alert alert-dismissable alert-danger'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>Wij hebben enkele fouten opgemerkt:</strong><ul style='border: none;'>";
			$message += arMessage.join("");
			$message += "</ul></div>";

			$(".errors").html($message);
		}
		else {
			$(".errors").empty();

			return false;
		}
	}
	else {
		$(".errors").empty();

		return true;
	}
}
