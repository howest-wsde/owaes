// admin.configuratie.php Validatie
$(document).ready(function() {
	$("form#frmConfig").submit(function() {
		var valid = validateAddActivity(true);

		if (valid) {
			if (confirm("Wilt u deze instellingen opslaan?")) {
				return true;
			}
		}

		return false;
	});

	$("form#frmConfig :input").on("blur", function() {
		return validateAddActivity(false);
	}).on("focus", function() {
		$(this).removeClass("fout");
	});
});

function validateAddActivity(bShowAlerts) {
	arFouten = {};
	arMessage = [];
	var $message = "";

	var $txtTemplateFolder = $("#txtTemplateFolder").val();

	var $txtV1 = $("#txtV1").val();
	var $txtV2 = $("#txtV2").val();

	var $txtLokatie = $("#txtLokatie").val();

	var $txtStart = $("#txtStart").val();
	var $txtMin = $("#txtMin").val();
	var $txtMax = $("#txtMax").val();
	var $txtEenheid = $("#txtEenheid").val();
	var $txtMeervoud = $("#txtMeervoud").val();
	var $txtOverdracht = $("#txtOverdracht").val();

	var $chkSMTP = $("#chkSMTP:checked").length;
	var $txtHost = $("#txtHost").val();
	var $chkAuth = $("#chkAuth:checked").length;
	var $txtSecure = $("#txtSecure").val();
	var $txtPort = $("#txtPort").val();
	var $txtUsername = $("#txtUsername").val();
	var $txtPasswd = $("#txtPasswd").val();

	var $txtNewMessage = $("#txtNewMessage").val();
	var $txtNewSub = $("#txtNewSub").val();
	var $txtRemSub = $("#txtRemSub").val();
	var $txtPlatform = $("#txtPlatform").val();
	var $txtRemUnread = $("#txtRemUnread").val();

	if ($txtTemplateFolder == "") {
		arFouten["txtTemplateFolder"] = "Gelieve een template map mee te geven.";
	}

	if ($txtLokatie == "") {
		arFouten["txtLokatie"] = "Gelieve een lokatie mee te geven.";
	}

	if ($txtStart == "") {
		arFouten["txtStart"] = "Gelieve een start credit mee te geven.";
	}

	if ($txtStart < 0 || $txtStart > $txtMax) {
		arFouten["txtStart"] = "Gelieve een start credit mee te geven tussen 0 en " + $txtMax + ".";
	}

	if ($txtMin == "") {
		arFouten["txtMin"] = "Gelieve een minimum credit mee te geven.";
	}

	if ($txtMin < 0 || $txtMin > $txtMax) {
		arFouten["txtMin"] = "Gelieve een minimum credit te geven tussen 0 en het maximum.";
	}

	if ($txtMax == "") {
		arFouten["txtMax"] = "Gelieve een maximum credit mee te geven.";
	}

	if ($txtMax < $txtMin) {
		arFouten["txtMax"] = "Het maximum credit kan niet kleiner zijn dan het minimum credit.";
	}

	if ($txtEenheid == "") {
		arFouten["txtEenheid"] = "Gelieve een naam te geven voor credit eenheid.";
	}

	if ($txtMeervoud == "") {
		arFouten["txtMeervoud"] = "Gelieve een meervoud van credits mee te geven.";
	}

	if ($txtOverdracht == "") {
		arFouten["txtOverdracht"] = "Gelieve een bericht te geven bij credit overdracht.";
	}

	if ($chkSMTP > 0) {
		if ($txtHost == "") {
			arFouten["txtHost"] = "Gelieve een host mee te geven.";
		}

		if ($chkAuth > 0) {
			if ($txtSecure == "") {
				arFouten["txtSecure"] = "Gelieve een security metode mee te geven.";
			}

			if ($txtPort == "") {
				arFouten["txtPort"] = "Gelieve een poort mee te geven.";
			}

			if ($txtPort < 0 || $txtPort > 65535) {
				arFouten["txtPort"] = "Gelieve een geldig poort nummer mee te geven.";
			}

			if ($txtUsername == "") {
				arFouten["txtUsername"] = "Gelieve een gebruikersnaam in te geven.";
			}

			if ($txtPasswd == "") {
				arFouten["txtPasswd"] = "Gelieve een wachtwoord mee te geven.";
			}
		}
	}

	if ($txtNewMessage == "") {
		arFouten["txtNewMessage"] = "Gelieve het aantal dagen of weken mee te geven voor nieuw bericht.";
	}

	if ($txtNewMessage < 1) {
		arFouten["txtNewMessage"] = "Het aantal dagen of weken voor nieuw bericht kan niet kleiner zijn dan 1.";
	}

	if ($txtNewSub == "") {
		arFouten["txtNewSub"] = "Gelieve het aantal dagen of weken mee te geven voor nieuw aanbieding.";
	}

	if ($txtNewSub < 1) {
		arFouten["txtNewSub"] = "Het aantal dagen of weken voor nieuw aanbieding kan niet kleiner zijn dan 1.";
	}

	if ($txtRemSub == "") {
		arFouten["txtRemSub"] = "Gelieve het aantal dagen of weken mee te geven voor herinnering aanbieding.";
	}

	if ($txtRemSub < 1) {
		arFouten["txtRemSub"] = "Het aantal dagen of weken voor herinnering aanbieding kan niet kleiner zijn dan 1.";
	}

	if ($txtPlatform == "") {
		arFouten["txtPlatform"] = "Gelieve een aantal mee te geven voor platform.";
	}

	if ($txtPlatform < 0) {
		arFouten["txtPlatform"] = "Het aantal kan niet kleiner zijn dan 0.";
	}

	if ($txtRemUnread == "") {
		arFouten["txtRemUnread"] = "Gelieve het aantal dagen of weken mee te geven voor ongelezen herinnering.";
	}

	if ($txtRemUnread < 1) {
		arFouten["txtRemUnread"] = "Het aantal dagen of weken voor ongelezen herinnering kan niet kleiner zijn dan 1.";
	}

	if (Object.keys(arFouten).length > 0) {
		$.each(arFouten, function(strID, strFout) {
			$message += "<li class='error'>" + strFout + "</li>";
			$("#" + strID).addClass("fout");

			if ($("#" + strID).hasClass("enabled")) {
				$("#" + strID).removeClass("enabled");
			}

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
