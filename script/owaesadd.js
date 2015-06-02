$(document).ready(function () { 

    $("select.aanbod").change(aanbodAanpassen);
	

	$("form#frmowaesadd").submit(function(){
		return  validateAddActivity(true); 
	})
	$("form#frmowaesadd :input").on("blur", function(){
		return  validateAddActivity(false); 
	}).on ("focus", function(){
		$(this).removeClass("fout"); 	
	});

	$("#calendar").fullCalendar({
        firstDay: 1, //monday
        header: {
            left: 'title',
            center: '',
            right: 'prev,next'
        },
		monthNames: ["Jan.","Feb.","Maart","April","Mei","Juni","Juli", "Aug.", "Sept.", "Okt.", "Nov.", "Dec." ],  
		dayNamesShort: ['zo','ma','di','wo','do','vr','za'],
        dayClick: function (date, jsEvent, view) { 
            if (date < moment().subtract("days", 1)) return; // geen click op items in 't verleden 
			
			strDate = date.format('DD/MM/YYYY'); 
			if (arDatums[strDate]) {
				delete arDatums[strDate]; 
				$(this).removeClass("selected");
				var iCount = 0;
				for (var dummy in arDatums) iCount++; 
				if (iCount == 0) arDatums[""] = {"start": "", "tijd": ""}; 
			} else {
				arDatums[strDate] = {"start": "", "tijd": ""}; 
				$(this).addClass("selected");
				if (arDatums[""]) delete arDatums[""]; 
			}
			console.log(arDatums); 
			
			printDates();  
        }, 
		dayRender: function( date, cell ) { 
			strDate = date.format('DD/MM/YYYY'); 
			if (arDatums[strDate]) $(cell).addClass("selected"); 
		}
    });
	

	$(document).on("focus", "input.startuur", function(e){
		$(this).select();  
	}).on("blur", "input.startuur", function(e){
		$(this).addClass("gepasseerd");
		strVal = $(this).val(); 
		strTijd = getTimeValue(strVal); 
		$(this).removeClass("invalidtime"); 
		if ((strVal != "") && (strTijd == "")) $(this).addClass("invalidtime"); 
		if (strTijd != "") {
			$(this).val(strTijd);  
			$(this).parentsUntil("div.tijdstip").parent().nextAll().find("input.startuur").each(function(){
				if ($(this).val() == "") $(this).val(strTijd);
			})
		} 
		saveTimers(); 
		validateAddActivity(); 
	});  
	$(document).on("focus", "input.tijdsduur", function(e){
		$(this).select();  
	}).on("blur", "input.tijdsduur", function(e){
		$(this).addClass("gepasseerd");
		strVal = $(this).val(); 
		strTijd = getTimeValue(strVal); 
		$(this).removeClass("invalidtime"); 
		if ((strVal != "") && (strTijd == "")) $(this).addClass("invalidtime"); 
		if (strTijd != "") {
			$(this).val(strTijd);  
			$(this).parentsUntil("div.tijdstip").parent().nextAll().find("input.tijdsduur").each(function(){
				if ($(this).val() == "") $(this).val(strTijd);
			})
		} 
		saveTimers();
		validateAddActivity();  
	});  
	printDates(); 
});

$(document).on( 'shown.bs.tab', 'a[data-toggle="tab"]', function (e) { 
	// bij change tabs: indien op tab 'tijd en locatie' gekomen > refresh kalender en google maps
	if ($(e.target).hasClass("tijdlocatie")) { 
		$('#calendar').fullCalendar('render');  
		google.maps.event.trigger(map, 'resize'); 
	} 
})


var arDev = Array(); 
var arSliders = {}; 
$(function() {  
	 
	// setup master volume 
	$("input.development").attr("type", "hidden").each(function(){  
		strDev = $(this).attr("name"); 
		iVal = $(this).attr("value"); 
		$(this).attr("value", iVal); 
		// console.log($(this).attr("value")); 
		arDev[arDev.length] = strDev; 
		$(this).after(
			$("<div />").addClass("slidervalue col-lg-2").attr("rel", strDev).html(iVal + "%")
		).after(
			$("<div />").addClass("slider").addClass("development").attr("rel", strDev).slider({
				min: 0,
				max: 100,
				step: 25,  
				value: iVal,
				orientation: "horizontal", 
				slide: function( event, ui ) {
					strDev = ui.handle.offsetParent.attributes["rel"].value; 
					iVal = ui.value; 
					$("input[name=" + strDev + "]").attr("value", iVal); 
					$(".slidervalue[rel=" + strDev + "]").html((iVal) + "%"); 
					var index = arDev.indexOf(strDev);
					arDev.splice(index, 1);
					arDev[arDev.length] = strDev; 
					iTotaal = 0; 
					for (i=0; i<arDev.length; i++) iTotaal += parseInt($("input[name=" + arDev[i] + "]").attr("value"));
					for (i=0; i<arDev.length; i++) {
						iVal = parseInt($("input[name=" + arDev[i] + "]").attr("value") ); 
						if ((iTotaal > 100)&&(iVal > 0)) {
							iAdd = iTotaal - 100; 
							if (iAdd > iVal) iAdd = iVal; 
							iTotaal -= iAdd; 
							iVal -= iAdd; 
							$(".slidervalue[rel=" + arDev[i] + "]").html((iVal) + "%"); 
							$("input[name=" + arDev[i] + "]").attr("value", iVal); 
							arSliders[arDev[i]].slider( "value", iVal);
						}
						if ((iTotaal < 100)&&(iVal < 100)) {
							iAdd = 100 - iTotaal; 
							if (iAdd > 100-iVal) iAdd = 100-iVal; 
							iTotaal += iAdd; 
							iVal += iAdd; 
							$(".slidervalue[rel=" + arDev[i] + "]").html((iVal) + "%"); 
							$("input[name=" + arDev[i] + "]").attr("value", iVal);
							arSliders[arDev[i]].slider( "value", iVal);
						}
					} 
				}
			}).each(function(){
				arSliders[strDev] = $(this);  
			})
		)
	}); 
	arDev.reverse();  
	
	/*
	$("input#timingfreeslide").change(function(){
		iVal = parseInt($(this).attr("value"));
		$("div#timerslide").slider({ 
			value: iVal,
		});
		if (!$("input#creditsfield").hasClass("changed")) {
			iMinuten = $(this).attr("value") * 60; 
			$("input#creditsfield").attr("value", iMinuten); 
			$("div#creditsslide").slider({ 
				value: iMinuten,
			});
		}
	}).each(function(){   
		iVal = parseInt($(this).attr("value"));  
		iMin = parseInt($(this).attr("min")); 
		iMax = parseInt($(this).attr("max"));  
		$(this).after(
			$("<div />").addClass("sliderref").html("uur") // .addClass("slidervalue").attr("rel", "timing")
		).before(
			$("<div />").addClass("slider").attr("rel", "timing").attr("id", "timerslide").slider({
				min: iMin, 
				max: iMax, 
				value: iVal, 
				orientation: "horizontal", 
				slide: function( event, ui ) { 
					iVal = ui.value; 
					$("input#timingfreeslide").attr("value", iVal).change(); 
					//$(".slidervalue[rel=timing]").html((iVal==0)?"onbepaald":(iVal + " uur"));  
				}
			}) 
		)
	});   
	*/
	
	$("input#creditsfield").change(function(){ 
		iVal = parseInt($(this).val()); 
		$("div#creditsslide").slider({ 
			value: iVal,
		}); 
	}).each(function(){   
		iVal = parseInt($(this).attr("value"));  
		iMin = parseInt($(this).attr("min")); 
		iMax = parseInt($(this).attr("max")); 
		$(this).after(
			$("<div />").addClass("sliderref credits").html(vocabulaire("credits")) // .addClass("slidervalue").addClass("creditsslide").attr("rel", "credits").html((iVal==0)?"overeen te komen":iVal)
		).before(
			$("<div />").addClass("slider").attr("id", "creditsslide").addClass("creditsslide").attr("rel", "credits").slider({
				min: iMin,
				max: iMax, 
				value: iVal,
				orientation: "horizontal", 
				slide: function( event, ui ) { 
					iVal = ui.value;  
					$("input#creditsfield").val(iVal).addClass("changed").change(); 
					validateAddActivity(false); 
					//$(".slidervalue[rel=credits]").html((iVal==0)?"overeen te komen":iVal);  
				}
			}) 
		)
	});  
	
	$("a.addstarttime").click(function(){
		oEl = $("dd.timingfixed:last");
		oNew = $("<dd />").attr("class", oEl.attr("class")).append(oEl.html());  
		oNew.find("a").html("tijdstip verwijderen").click(function(){
			oDD = $(this).parentsUntil("dl"); 
			oDD.remove(); 	
		});
		// oNew.find("input").attr("value", oEl.find("input").attr("value", ));
		oNew.find('.datetimerpicker').appendDtpicker({
			"inline": false,
			"closeOnSelected": true, 
			"dateFormat": "DD/MM/YYYY hh:mm", 
			"locale": "nl",  
		});  
		oEl.after(oNew);
	})
	$("a.delstarttime").click(function(){
		oDD = $(this).parentsUntil("dl"); 
		oDD.remove(); 	
	});
	

	$("a.tabchange").click(function(){ 
		$('#tabsAdd a[href="' + $(this).attr("href") + '"]').tab('show'); 
	}) 
	
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		strCurrentTab = $(e.relatedTarget).attr("href"); 
		$(strCurrentTab + " :input").addClass("gepasseerd"); 
		 
		//e.target // activated tab
		//e.relatedTarget // previous tab
		//console.log(e.target); 
		//console.log($(e.target).attr("href")); 
		//console.log(e.relatedTarget); 
		// this tab all :input addClass gepasseerd
		validateAddActivity(false); 
		// $('#tabsAdd a[href="' + $(e.relatedTarget).attr("href") + '"]').tab('show'); 
		
//		alert("test"); 
//		return false; 
	})
});


/*
 * printDates()
 * Zal alle data in de array van dates weergeven, geordend volgens de datum
 */ 
  
function printDates() {
	arDatumValues = Array(); 
	iCount = 0; 
	for (var strDate in arDatums) {
		arDatumValues[arDatumValues.length] = strDate;  
		iCount ++; 
	}
	if (iCount == 0) {
		arDatums[""] = {"start": "", "tijd": ""}; 
		arDatumValues[0] = ""; 
	}
	arDatumValues.sort(compareDates); 
	$("div#timers").html(""); 
	for (i=0; i<arDatumValues.length; i++) {
		strDate = arDatumValues[i]; 
		strKey = strDate.split("/").join(""); 
        
			$("div#timers").append(
				$("<div />").addClass("tijdstip").append(
					$("<div />").addClass("form-group").append(
						$("<input />").attr("name", "data[]").attr("type", "hidden").addClass("datestamp").val(strKey)
					).append(
						$("<input />").attr("name", "datum-" + strKey).attr("type", "hidden").val(strDate)
					).append(
						$("<div />").addClass("col-lg-3").append( 
							$("<label />").addClass("date").html((strDate == "")?"willekeurige datum": strDate)
						)
					).append(
						$("<div />").addClass("col-lg-1").html("om")
					).append(
						$("<div />").addClass("col-lg-2").append( 
							$("<input />").attr("name", "start-" + strKey).addClass("time").attr("placeholder", "xx : xx").addClass("startuur").attr("type", "text").val(arDatums[strDate].start)
						)
					).append(
						$("<div />").addClass("col-lg-2").html("gedurende")
					).append(
						$("<div />").addClass("col-lg-2").append( 
							$("<input />").attr("name", "tijd-" + strKey).addClass("time").addClass("tijdsduur").attr("type", "text").attr("min", 1).attr("max", 8).val(arDatums[strDate].tijd)
						)
					).append(
						$("<div />").addClass("col-lg-1").html("uur")
					) 
				) 
			);
			
    }
	
	 
}


/*
 * compareDates(a, b)
 * Zal twee data vergelijken en ordenen
 * Wordt gebruikt om de data chronologisch te ordenen
 */
function compareDates(a, b) {
    if (dateval(a) < dateval(b)) return -1;
    if (dateval(a) > dateval(b)) return 1;
    return 0;
}
function dateval(strDate) {
	arDate = strDate.split("/"); 
	console.log(arDate + " - " + ((arDate[2]-2000)*400 + arDate[1]*40 + arDate[0])); 
	return arDate[2]*1000 + arDate[1]*40 + arDate[0]; 
}


/*
 * aanbodAanpassen()
 * Verandert de legend tag van het aanbod afhankelijk van welk aanbod men selecteerd (Werkervaring - Opleiding - Delen)
 */
function aanbodAanpassen() {
    var $aanbod = $("select.aanbod").val();
    var $legend = "Aanbod toevoegen: ";
    var $credits = "";

    switch ($aanbod) {
        case "ervaring":
            $legend += "<strong>Werkervaring</strong> <small>(" + vocabulaire("credits") + " uitgeven)</small>";
            $credits = "" + vocabulaire("credits") + " (uitgeven)"
            break;
        case "opleiding":
            $legend += "<strong>Opleiding</strong> <small>(" + vocabulaire("credits") + " verdienen)</small>";
            $credits = "" + vocabulaire("credits") + " (verdienen)"
            break;
        case "infra":
            $legend += "<strong>Delen</strong> <small>(" + vocabulaire("credits") + " verdienen)</small>";
            $credits = "" + vocabulaire("credits") + " (verdienen)"
            break;
        default:
            $legend = "Aanbod toevoegen";
            $credits = vocabulaire("credits"); 
    }
    $("legend.aanbod").html($legend);
    $("div.sliderref.credits").html($credits);
}




/*
 * validateAddActivity()
 * Zal de velden overlopen in de owaesadd.php en nakijken of deze correct zijn ingevuld.
 * Zo ja    => opslaan naar de database
 * Zo niet  => errormessage(s) weergeven
 */
 
function validateAddActivity(bShowAlerts) { 
	arFouten = {}; 
	arMessage = []; 
    var $message = "";
    var $titel = $(".content-market-add #title").val();
    var $omschrijving = $(".content-market-add #description").val();
    var $locatie = $(".content-market-add #location").val();
    var $tijdsduur = $(".content-market-add #timingfreeslide").val();
    var $credits = $(".content-market-add #creditsfield").val();
    var $inputTijdstippen = $(".tijdstippen input").val();
    var strType = $("#kiesowaestype").val();  
	var strPerson = $("#person").val();  
	
	if (arP[strType].indexOf(strPerson) < 0) arFouten["kiesowaestype"] = "Deze gebruiker kan geen opdracht van dit type aanmaken"; 
	
    if ($titel == "" || $titel.length < 5) {  
		arFouten["title"] = "De titel van een activiteit moet minstens 5 karakters bevatten."; 
	}
    if ($omschrijving == "" || $omschrijving.length < 20) { 
		arFouten["description"] = "Gelieve een volwaardige omschrijving in te geven."; 
	} 
	if ($(".invalidtime").length > 0) {
		$(".invalidtime:not([id])").each(function(){
			$(this).attr("id", "inpfield" + Math.floor(Math.random()*100000)); 
		})
		$(".invalidtime").each(function() {
			arFouten[$(this).attr("id")] = "Gelieve geldige waarden in te vullen voor startuur en duurtijd."; 
		}) 
	}
    if (!($credits > 0)) {
		arFouten["creditsfield"] = "Gelieve meer dan 0 " + vocabulaire("credits") + " te geven/vragen."; 
	}
    if (Math.floor($credits) > Math.floor($(".content-market-add #creditsfield").attr("max"))) { 
 		arFouten["creditsfield"] = "Er geldt een maximum van " + $(".content-market-add #creditsfield").attr("max") + " " + vocabulaire("credits") + " per opdracht."; 
	}
    if ($("input#voorwaarden:checked").length == 0) arFouten["voorwaarden"] = "Gelieve de gebruiksvoorwaarden aan te vinken.";   // Ik bevestig dat dit aanbod conform de gebruiksvoorwaarden is. 


	if (Object.keys(arFouten).length > 0) { 
		$.each(arFouten, function(strID, strFout) { 
			$message += "<li class=\"error\">" + strFout + "</li>"; 
			$("#" + strID).addClass("fout");
			if (bShowAlerts || $("#" + strID).hasClass("gepasseerd")) arMessage[arMessage.length] = "<li class=\"error\">" + strFout + "</li>"; 
		}); 
		if (arMessage.length > 0) {
			$message = "<div class=\"alert alert-dismissable alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>Wij hebben enkele fouten opgemerkt:</strong> <ul>";
			$message += arMessage.join(""); 
			$message += "</ul></div>" ; 
			$(".content-market-add .errors").html($message);
		} else $(".content-market-add .errors").empty(); 
		return false; 
	} else {
    	$(".content-market-add .errors").empty();
		return true; 
	}  
	 
	
}


function saveTimers() {
	iTotaal = 0; 
	$("div.tijdstip input.datestamp").each(function(){
		strKey = $(this).val(); 
		strDatum = $("input[name='datum-" + strKey + "']").val();
		strTijd = $("input[name='tijd-" + strKey + "']").val(); 
		arTijd = strTijd.split(":");  
		arDatums[strDatum] = {
			"start" : $("input[name='start-" + strKey + "']").val(),  
			"tijd" : strTijd, 
		}  
		if (arTijd.length == 2) iTotaal += parseInt(arTijd[0])*60 + parseInt(arTijd[1]); 
	})
	$("input#creditsfield:not(.changed)").val(iTotaal).change();  
}




// MAPS CHANGE
var iTimerZoek = 0; 
$(document).ready(function() {
	$("input#location").keyup(function() { 
		clearTimeout(iTimerZoek);
		iTimerZoek = setTimeout("geozoek();", 1000); 
	}) 
	$("input#location").bind("change click", function() {
		geozoek(); 
	}) 
})


function geozoek() {
	clearTimeout(iTimerZoek); 
	strVal = $("input#location").val(); 
	console.log("geozoek: " + strVal); 
	if (strVal == "") {
		$("input#locationlong").val(0);
		$("input#locationlat").val(0);
		deleteMarker(); 
	} else {
		$.ajax({
			type: "POST",
			url: "details.location.php", 
			data: "search=" + escape(strVal), 
			success: function(strResult){ 
				arLoc = strResult.split("|"); 
				if (arLoc.length == 2) {
					if (($("input#locationlong").val() != arLoc[0])||($("input#locationlat").val() != arLoc[1])){
						$("input#locationlong").val(arLoc[0]);
						$("input#locationlat").val(arLoc[1]);
						setMarker(new google.maps.LatLng(arLoc[1], arLoc[0]));   
					}
				}  else deleteMarker(); 
			}
		}); 
	}
}

function setMarker(oPos) {
	if (marker) marker.setMap(null);
	marker = new google.maps.Marker({
		map:map,
		draggable: false, 
		animation: google.maps.Animation.DROP, 
		position: oPos
	  });	
	  map.panTo(oPos); 
	  map.setZoom(14); 
}

function deleteMarker() {
		if (marker) marker.setMap(null);
		map.setZoom(12); 
}



$(document).ready(function() {
	rxSplitTags = /[,;]/; 
	$("input.tag").focus(function(){
		$("div#tags").addClass("actief"); 
	}).blur(function(){
		$("div#tags").removeClass("actief"); 
	}).keydown(function(e){
		switch(e.keyCode){
			case 13: 
				strVal = $(this).val(); 
				arVal = strVal.split(rxSplitTags);  
				while (arVal.length > 0) {
					strVal = arVal.shift(); 
					addTag(strVal); 
				} 
				$(this).val("");
				return false; 
				break; 	
			case 44: 
			case 59: 
				if ($(this).val() == "") {
					$(this).val($("div#tags span.tag:last input").val());
					$("div#tags span.tag:last").remove(); 
					return false; 
				}
				break; 
		} 
	}).keyup(function(){
		strVal = $(this).val(); 
		arVal = strVal.split(rxSplitTags);  
		while (arVal.length > 1) {
			strVal = arVal.shift(); 
			addTag(strVal); 
		} 
		strVal = arVal.join(""); 
		$(this).val(strVal);
		
		if (strVal != "") { 
			// $("div#tags ul.tags").load();   
			$.getJSON( "tags.php", { s: strVal } ).done(function( arTags ) {
				if ($("div#tags ul.tags").length == 0) $("div#tags").append(
					$("<ul />").addClass("tags")
				);
				$("div#tags ul.tags li").remove(); 
				for (i=0; i<=arTags.length; i++){
					strTag = arTags[i];
					$("div#tags ul.tags").append( 
						$("<li />").text(strTag).attr("rel", strTag).click(function(){
							$("input.tag").val("");
							addTag($(this).attr("rel")); 
							$("div#tags ul.tags").remove(); 
						})
					);
				}  
				if (arTags.length == 0) $("div#tags ul.tags").remove(); 
			});
		} else $("div#tags ul.tags").remove(); 
	}).change(function(){ 
		setTimeout(function() {  
			strVal = $("input.tag").val(); 
			arVal = strVal.split(rxSplitTags);  
			while (arVal.length > 0) {
				strVal = arVal.shift(); 
				addTag(strVal); 
			} 
			$("input.tag").val("");
			$("div#tags ul.tags").remove(); 
		}, 500); // timeout is nodig om click op list-item tijd te geven 
	})
	$("div#tags span.tag a").click(function(){
		$("#" + $(this).attr("rel")).remove(); 
		return false; 
	});
})
function addTag(strTag) {
	if (strTag != "") {
		strKey = "tag_" + ($("div#tags span.tag").length+1) + "_" + Math.floor(1000*Math.random()); 
		$("input.tag").before(
			$("<span />").addClass("tag").attr("id", strKey).append(
				$("<span>").text(strTag.trim())
			).append(
				$("<a />").attr("title", "verwijderen").text("x").attr("href", "#").attr("rel", strKey).click(function(){
					$("#" + $(this).attr("rel")).remove(); 
					return false; 
				})
			).append(
				$("<input />").attr("name", "tag[]").attr("type", "hidden").val(strTag.trim())
			)
		)
	}
	$("input.tag").focus(); 
}

function getTimeValue(strVal) {
	strTime = ""; 
	arSplit = strVal.split(/[^0-9]+/); 
	arUur = Array(); 
	for (i=0; i<arSplit.length; i++) if (arSplit[i] != "") arUur[arUur.length]=arSplit[i];  
	switch(arUur.length) { 
		case 0:  
			break; 
		case 1: 
			iUur = arUur[0];  
			if (iUur <= 99) strTime = iUur + ":00"; 
			break; 
		case 2: 
		default: 
			iUur = arUur[0]; 
			iMin = arUur[1]; 
			if (iMin == "5") iMin = 30; 
			if ((iUur <=99)&&(iMin<60)) strTime = iUur + ":" + iMin;  
			break;  
	}  
	return strTime;  	
}
