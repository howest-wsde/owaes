$(document).ready(function() {

	$("form.bestanden").submit(function () {
		arErrors = Array(); 
		oErrors = {}; 
		$(":input.veldnamen").each(function(){
			arVelden = $(this).val().split(",");  
			if (($(":input[name=" + arVelden[0] + "]").val() != "")||($(":input[name=" + arVelden[1] + "]").val() != "")) {
				if ($(":input[name='" + arVelden[0] + "']").val() == "") {
				   $(":input[name='" + arVelden[0] + "']").parent("div").addClass("has-error"); 
				   oErrors["titel"] = ""; 
				}
				if ($(":input[name='" + arVelden[1] + "']").val() == "") {
				   $(":input[name='" + arVelden[1] + "']").parent("div").addClass("has-error"); 
				   oErrors["bestand"] = ""; 
				}
				
			}
		}) 
		$(":input.existingfiles").each(function(){
			arVelden = $(this).val().split(",");  
			if (($(":input[name=" + arVelden[0] + "]").val() == "")&&($(":input[name=" + arVelden[2] + "]").val() != -1)) {
				$(":input[name='" + arVelden[0] + "']").parent("div").addClass("has-error"); 
				oErrors["titel"] = ""; 
			}
		}) 
		 
		for (var strError in oErrors) {
			switch(strError) {
				case "titel": 
					arErrors.push("Ieder bestand moet een titel hebben"); 
					break; 
				case "bestand": 
					arErrors.push("Voor iedere bestandstitel moet een bestand upgeload worden"); 
					break; 	
			}
		}
		
		if (arErrors.length == 0) {
			return true; 
		} else {
			$(printErrors(arErrors)).insertBefore("#editBestanden .modal-body fieldset");
			return false; 
		} 
	})
	
	
	$("form.persoonlijkeinformatie").submit(function () {
		arErrors = Array(); 
		if (!validateEmail($("#email").val())) {
     	   $("#editPersoonlijkeInformatie #email").parent("div").addClass("has-error");
		   arErrors.push("Er werd geen geldig e-mailadres ingevuld"); 
		} 
		if (arErrors.length == 0) {
			return true; 
		} else {
			$(printErrors(arErrors)).insertBefore("#editPersoonlijkeInformatie .modal-body fieldset");
			return false; 
		} 
	})

	
	$("#editPersoonlijkeInformatie #email").change(function(){
		$(this).addClass("loading"); 
		$("#editPersoonlijkeInformatie .btn-save").addClass("disabled"); 
		$.ajax({
			type: "POST",
			url: "checkmail.ajax.php",
			data: {"m": $("#editPersoonlijkeInformatie #email").val()},
			success: function(strResult){ // "yes" of "no"
				$("#editPersoonlijkeInformatie .btn-save").removeClass("disabled"); 
				$("#editPersoonlijkeInformatie #email").removeClass("loading").removeClass("invalid");	
				$("#editPersoonlijkeInformatie #email").parent("div").removeClass("has-error");
				if (strResult == "no") {
					$("#editPersoonlijkeInformatie #email").addClass("invalid");  
					$("#editPersoonlijkeInformatie #email").parent("div").addClass("has-error");
				}
			}, 
		});
	});


    //profile
    //$(".well-intro span.icon-settings").click(bestandenInit);
    $("#editBestanden .bestand-toevoegen").click(bestandenToevoegen);
    $("#editIntro .close").click(function () { 
        bestandenToevoegen();
    });
    $("#editIntro .btn-cancel").click(function () { 
        bestandenToevoegen();
    });
    $("#editIntro .btn-save").click(introOpslaan);
    
	//$("#editPersoonlijkeInformatie .btn-save").click(persoonlijkeGegevensOpslaan);
	$("form.persoonlijkeinformatie").submit(persoonlijkeGegevensOpslaan); 
	
    $("#editBasisgegevens .btn-save").click(basisGegevensOpslaan);

 


}); 


// --- Profile Edit --- //


/*
 * bestandenToevoegen
 * Zal een $addfile appenden aan de .files
 * Wordt afgevuurd door een click op de anchor .bestand-toevoegen, in het model
 * $addfile werd globaal aangemaakt, data werd toegevoegd via bestandenInit()
 */
function bestandenToevoegen() {
 
	iBestand++; 
	arVelden = Array("titel" + iBestand, "bestand" + iBestand, "visibility" + iBestand);  
    strAddFile = "<input type=\"hidden\" name=\"files[]\" class=\"veldnamen\" value=\"" + arVelden.join(",") + "\" /> ";
    strAddFile += "<div class=\"form-group file-uploaden\">";
    strAddFile += "<div class=\"col-lg-3\"><input type=\"text\" class=\"form-control filetitle\" placeholder=\"Titel voor bestand\" name=\"" + arVelden[0] + "\" /></div>";
    strAddFile += "<div class=\"col-lg-5\"><input type=\"file\" ext=\"pdf,doc,docx,txt,jpg,jpeg,gif,bmp,png,xls,xlsx,md,ppt,pps,odt,odt,ods,odp,csv,svg\" class=\"form-control filedata\" name=\"" + arVelden[1] + "\" /></div>";
    strAddFile += "<div class=\"col-lg-4\"><select class=\"form-control\" name=\"" + arVelden[2] + "\" ><option>Zichtbaar voor iedereen</option><option>Zichtbaar voor vrienden</option><option>Verborgen</option></select></div>";
    strAddFile += "</div>"; 
	
    $("#editBestanden .files").append(strAddFile); 
}
 


/*
 * basisGegevensOpslaan()
 * Valideert de basisgegevens
 * Valid    =>  opslaan naar de database
 * Invalid  =>  een alert tonen adhv printErrors($errors)
 */
function persoonlijkeGegevensOpslaan() { 
    arErrors = []; 
    $(".alert").remove();
    $(".has-error").removeClass("has-error");
	 
	//$("#editPersoonlijkeInformatie #email").addClass("");
    if (!validateEmail($("#editPersoonlijkeInformatie #email").val())) {
        arErrors.push("Het gegeven e-mailadres is incorrect.");
        $("#editPersoonlijkeInformatie #email").parent("div").addClass("has-error");
        $("#editPersoonlijkeInformatie #email").focus();
    }else if ($("#editPersoonlijkeInformatie #email").hasClass("invalid")) {
		arErrors.push("E-mailadres bestaat reeds in het systeem");
		$("#editPersoonlijkeInformatie #email").parent("div").addClass("has-error");
		$("#editPersoonlijkeInformatie #email").focus();
	} 
	
	console.log(arErrors); 
    if (arErrors.length > 0) { 
        $(printErrors(arErrors)).insertBefore("#editPersoonlijkeInformatie .modal-body fieldset");
		return false; 
    } else {  
		return true;   
    } 
}
 



/*
 * basisGegevensOpslaan()
 * Slaat de basisgegevens op. Geen check nodig want alles mag leeg zijn en validatie is niet van toepassing
 */
function basisGegevensOpslaan() {
    $errors = [];
    $message = "";
    $(".alert").remove();
    $(".has-error").removeClass("has-error");
 
 
	strFB = $("#facebook").val(); 
    if (strFB != "") { 
		if (strFB.indexOf("://") < 0) strFB = "http://" + strFB; 
		if (( strFB.indexOf("facebook.com") + strFB.indexOf("fb.com") ) < 0) { // indien geen van beide: resultaat = -2, indien 1 > resultaat = 3 ofzo
			$errors.push("De opgegeven facebook-link is ongeldig. Kopieer de volledige link van uw facebook-pagina. (bv. https://www.facebook.com/howestbe) ");
        	$("#facebook").parent("div").addClass("has-error");
		} else {
			$("#facebook").val(strFB); 
		}
    }
	strTwitter = $("#twitter").val(); 
    if (strTwitter != "") { 
		if (strTwitter.indexOf("://") < 0) strTwitter = "http://" + strTwitter; 
		if (( strTwitter.indexOf("twitter.com") ) < 0) {  
			$errors.push("De opgegeven Twitter-link is ongeldig. Kopieer de volledige link van uw Twitter-pagina. (bv. https://www.twitter.com/howestbe) ");
        	$("#twitter").parent("div").addClass("has-error");
		} else {
			$("#twitter").val(strTwitter); 
		}
    }
	strLinkedIn = $("#linkedin").val(); 
    if (strLinkedIn != "") { 
		if (strLinkedIn.indexOf("://") < 0) strLinkedIn = "http://" + strLinkedIn; 
		if (( strLinkedIn.indexOf("linkedin.com") ) < 0) {  
			$errors.push("De opgegeven LinkedIn-link is ongeldig. Kopieer de volledige link van uw LinkedIn-pagina. (bv. https://www.linkedin.com/profile/view?id=15032447) ");
        	$("#linkedin").parent("div").addClass("has-error");
		} else {
			$("#linkedin").val(strLinkedIn); 
		}
    }
	strPlus = $("#plus").val(); 
    if (strPlus != "") { 
		if (strPlus.indexOf("://") < 0) strPlus = "http://" + strPlus; 
		if (( strPlus.indexOf("plus.google.com") ) < 0) {  
			$errors.push("De opgegeven GooglePlus-link is ongeldig. Kopieer de volledige link van uw GooglePlus-pagina. (bv. https://plus.google.com/howestbe) ");
        	$("#plus").parent("div").addClass("has-error");
		} else {
			$("#plus").val(strPlus); 
		}
    }
	
    $message = printErrors($errors);

    if ($errors.length > 0) {
        //errors printen
        console.log("JS: [basisGegevensOpslaan] errors: " + $errors[0] + " " + $errors[1]);
        $($message).insertBefore("#editBasisgegevens .modal-body fieldset");
		return false; 
    } else {
        //opslaan in de database
        console.log("JS: [basisGegevensOpslaan] gegevens opslaan naar de database...");
    }  
}




/*
 * introOpslaan()
 * Zal eerste alle lege rijen (bestanden) weghalen, erna opslaan naar de database
 * Getriggerd door .btn-save
 */
function introOpslaan() {
	 
    $errors = [];
    $message = "";
    $(".alert").remove();
    $(".has-error").removeClass("has-error"); 
	 
    if ($(".filetitle").val() == "") {
        $errors.push("U hebt een bestand opgeladen zonder deze een naam te geven. Gelieve een naam toe te kennen aan uw bestand.");
        $("#editIntro .filetitle").parent("div").addClass("has-error");
        $("#editIntro .filetitle").focus();
    }
    if ($(".filedata").val() == "") {
        $errors.push("U hebt een titel gegeven aan een bestand dat u nog niet opgeladen hebt. Gelieve de titel te verwijderen of het bestand op te laden.");
        $("#editIntro .filedata").parent("div").addClass("has-error");
        //no focus here
    }
	
     

    $message = printErrors($errors);

    if ($errors.length > 0) {
        //errors printen
        console.log("JS: [introOpslaan] errors: " + $errors[0] + " " + $errors[1]);
        $($message).insertBefore("#editIntro .modal-body fieldset");
		return false; 
    } else {
        //opslaan in de database
        console.log("JS: [introOpslaan] gegevens opslaan naar de database...");
    }
	return false; 
}
