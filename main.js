/*GLOBAL vars*/
var $addfile = "";
var $errors = [];
var $errorsTime = [];
var $dates = [];
var iBestand = 0;

$(document).ready(function () { 
    initAttributes();
    initEventHandlers();
	
	/* START FEEDBACK STARS */
	$(document).on("mouseover", "ol.stars li:not(.fixed)", function(){
		$(this).parent().find("li").removeClass("not"); 
		$(this).nextAll("li").addClass("not"); 
		$("span.stars").html($(this).find("a").html());  
		iStars = $(this).find("a").attr("rel"); 
		$(this).parentsUntil("form").parent().find("input.stars").val(iStars);
	}); 
	$("ol.stars li a").on("click", function(){ 
		iStars = $(this).attr("rel"); 
		$(this).parentsUntil("form").find("li").addClass("fixed"); 
		$(this).parentsUntil("form").find("li").removeClass("not"); 
		$(this).parent().nextAll("li").addClass("not"); 
		$("span.stars").html($(this).html());  
		$(this).parentsUntil("form").parent().find("input.stars").val(iStars);
		return false; 
	}); 
	/* END FEEDBACK STARS */
	
});

jQuery.removeFromArray = function (value, arr) {
    return jQuery.grep(arr, function (elem, index) {
        return elem !== value;
    });
};

function initCreditmeter(iPercentage) { 
    var iMaxHoek = 289; //niet 360, want dan zou de teller terug op nul staan, op 289° staat de teller op 9600
	var iDegrees = iMaxHoek / 100 * iPercentage;  
    
    $(".content-home .creditmeterpointer").css({
        "-moz-transform": "rotate(" + iDegrees + "deg)",
        "-ms-transform": "rotate(" + iDegrees + "deg)",
        "-o-transform": "rotate(" + iDegrees + "deg)",
        "-webkit-transform": "rotate(" + iDegrees + "deg)",
        "transform": "rotate(" + iDegrees + "deg)",
    });
}
 
/*
 * initAttributes()
 */
function initAttributes() {
    $(".badges li img").popover({
        toggle: "popover",
        trigger: "hover",
        placement: "top"
    });
 

    $("#geboortedatum").datepicker({
        dateFormat: "dd-mm-yy",
        showAnim: "slideDown",
        changeMonth: true,
        changeYear: true,
        yearRange: "1940:2000",
        monthNames: ["Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"],
        monthNamesShort: ["Jan", "Feb", "Maa", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec"],
        defaultDate: "-60y",
    });
    //$('.test').datepicker();

    $(".fc-day").remove(".fc-day-content"); 

    $("div#tijdsduur").hide();
    $("div#tijdstip").hide();
    $("div#datesperiods").hide();
}
 

/*
 * initEventHandlers()
 */
function initEventHandlers() {
    //home
    $(".layoutBlocks .panel-title a").click(function () {
        if ($(this).children(".icon").hasClass("icon-plus")) {
            console.log("JS: [initEventHandlers] collapsed");
            $(this).children(".icon").removeClass("icon-plus");
            $(this).children(".icon").addClass("icon-minus");
        } else if ($(this).children(".icon").hasClass("icon-minus")) {
            console.log("JS: [initEventHandlers] expanded");
            $(this).children(".icon").removeClass("icon-minus");
            $(this).children(".icon").addClass("icon-plus");
        } else {
            //iets anders
        }
    });
     
   // $(".content-market-add #owaesadd").click(validateAddActivity);
    

    changeDefaultDate();
    keyUpDateTime();
}
// --- Add New OWAES Item --- //

 

/*
 * appendKeyupErrors()
 * Zal de array van errors overlopen en nakijken of de array van errors gevuld is,
 * zo ja, printErrors($errorsTime) uitvoeren
 */
function appendKeyupErrors() {
    $(".errorsTime").empty();
    consoleArray($errorsTime);
    if ($errorsTime.length > 0) {
        $message = printErrors($errorsTime);
        $(".content-market-add .errorsTime").append($message);
    }
    //$errorsTime = [];
}

/*
 * validateHour(hour)
 */
function validateHour(hour) {
    $hour = parseInt(hour);
    if ($hour <= 23 && $hour >= 00) {
        return true;
    } else {
        return false;
    }
    return false;
}

/*
 * validateMInutes(minutes)
 */
function validateMinutes(minutes) {
    $minutes = parseInt(minutes);
    if ($minutes <= 59 && $minutes >= 00) {
        return true;
    } else {
        return false;
    }
    return false;
}
 

/*
 * changeDefaultDate()
 */
function changeDefaultDate() {
    $("#dates input[type='radio'], #datesperiods input[type='radio']").click(function () {
        if ($(this).is(':checked')) {
            $(".form-group").removeClass("default");
            $(".form-group").addClass("other");
            $(this).parent().parent(".form-group").addClass("default");
            keyUpDateTime();
        } else {
            //$(this).parent().parent(".form-group").addClass("other");
        }
    });
}

/*
 * keyUpDateTime()
 * luisterd naar iedere verandering in de inputboxes voor de uren en minuten en zal naargelang acties ondernemen
 * - errors toevoegen indien incorrect
 * - overige inputboxes aanvullen indien wel correct
 */
function keyUpDateTime() {
    //changeDefaultDate();

    $(".default .rangespan").on("change", function () {
        $(".default .rangevalue").val($(this).val());
        $(".other .rangespan").val($(this).val());
        $(".other .rangevalue").val($(this).val());
    });

    $(".other .rangespan").on("change", function () {
        $(this).parent().children(".rangevalue").val($(this).val());


        console.log("JS: [keyUpDateTime] this=" + $(this).val());
 
    });

    //BEGIN UUR
    $(".default .hourfrom").bind("change", function (e) {
        //$errorsTime = [];
        var $hourfrom = parseInt($(this).val());
        var $hourto = parseInt($(".default .hourto").val());

        if (validateHour($hourfrom)) {
            $("#dates .other .hourfrom").val($(this).val());
            $errorsTime = jQuery.removeFromArray("Het uur (start) moet tussen 00 en 23 vallen.", $errorsTime);
            $(this).removeClass("error");
        } else {
            //var $return = $(this).val().substring(0, 1);
            //$(this).val($return);
            $errorsTime.push("Het uur (start) moet tussen 00 en 23 vallen.");
            $(this).addClass("error");
        }
        $errorsTime = jQuery.unique($errorsTime);
        appendKeyupErrors();
    });

    //BEGIN MINUTEN
    $(".default .minutefrom").bind("change", function (e) {
        //$errorsTime = [];
        if (validateMinutes($(this).val())) {
            $("#dates .other .minutefrom").val($(this).val());
            $errorsTime = jQuery.removeFromArray("De minuten (start) moeten tussen 00 en 59 vallen.", $errorsTime);
            $(this).removeClass("error");
        } else {
            //var $return = $(this).val().substring(0, 1);
            //$(this).val($return);
            $errorsTime.push("De minuten (start) moeten tussen 00 en 59 vallen.");
            $(this).addClass("error");
        }
        $errorsTime = jQuery.unique($errorsTime);
        appendKeyupErrors();
    });
    
    //EIND UUR
    $(".default .hourto").bind("change", function (e) {
        //$errorsTime = [];
        var $hourfrom = parseInt($(".default .hourfrom").val());
        var $hourto = parseInt($(this).val());

        if (validateHour($hourto)) {
            $errorsTime = jQuery.removeFromArray("Het uur (einde) moet tussen 00 en 23 vallen.", $errorsTime);
            if ($hourto > $hourfrom) {
                $("#dates .other .hourto").val($hourto.toString());
                $errorsTime = jQuery.removeFromArray("Het uur (einde) moet hoger liggen dan het beginuur.", $errorsTime);
                $(this).removeClass("error");
            } else {
                $errorsTime.push("Het uur (einde) moet hoger liggen dan het beginuur.");
                $(this).addClass("error");
            }
        } else {
            //var $return = $(this).val().substring(0, 1);
            //$(this).val($return);
            $errorsTime.push("Het uur (einde) moet tussen 00 en 23 vallen.");
            $(this).addClass("error");
        }
        $errorsTime = jQuery.unique($errorsTime);
        appendKeyupErrors();
    });

    //EIND MINUTEN
    $(".default .minuteto").bind("change", function (e) {
        //$errorsTime = [];
        if (validateMinutes($(this).val())) {
            $("#dates .other .minuteto").val($(this).val());
            $errorsTime = jQuery.removeFromArray("De minuten (einde) moeten tussen 00 en 59 vallen.", $errorsTime);
            $(this).removeClass("error");
        } else {
            //var $return = $(this).val().substring(0, 1);
            //$(this).val($return);
            $errorsTime.push("De minuten (einde) moeten tussen 00 en 59 vallen.");
            $(this).addClass("error");
        }
        $errorsTime = jQuery.unique($errorsTime);
        appendKeyupErrors();
    });
}


/*
 * validateEmail()
 * Bevat een reguliere expressie voor email
 * return true wanneer de reguleire expressie overeenkomt met de gegeven waarde
 */
function validateEmail(email) {
    var regex = /^([\w-\.]+@(?!gmail.com)(?!yahoo.com)(?!hotmail.com)([\w-]+\.)+[\w-]{2,4})?$/;
    return regex.test(email);
}

/*
 * validatePhone()
 * Bevat een reguliere expressie voor zowel vaste telefoons als mobiele telefoons
 * return true wanneer de reguliere expressie overeenkomt met de gegeven waarde
 */
function validatePhone(phone) {
    var regexPhone = /^((\+|00)32\s?|0)(\d\s?\d{3}|\d{2}\s?\d{2})(\s?\d{2}){2}$/;
    var regexMobile = /^((\+|00)32\s?|0)4(60|[789]\d)(\s?\d{2}){3}$/;
    //phone=="" omdat het leeg mag zijn, maar wanneer het ingevuld is, moet het wel correct zijn
    if (regexPhone.test(phone) == true || regexMobile.test(phone) == true || phone == "") {
        return true;
    }
    return false;
}

/*
 * printErrors(errors)
 * Vraagt een array van errors
 * Returned HTML code voor een alert aan de hand van de errors
 */
function printErrors(errors) {
    $message = "";
    $message += "<div class=\"alert alert-dismissable alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">x</button><strong>Wij hebben enkele fouten opgemerkt:</strong> <ul>";
    for (i = 0; i < errors.length; i++) {
        $message += "<li class=\"error\">" + errors[i] + "</li>";
    }
    $message += "</ul></div>"

    return $message;
}


//function console($origin, $data) {
//    console.log("JS: [" + $origin + "] " + $data);
//}

function consoleArray(array) {
    for (i = 0; i < array.length; i++) {
        console.log("JS [array] " + array[i]);
    }
}
/*
 * raiseExp($exp)
 * $exp     de verkregen experience (int) van een opdracht/opleiding/delen
 */
function raiseExp($exp) { 
    var $progressbar = $(".progress-bar-experience");
    var $valuenow = $progressbar.attr("aria-valuenow");
    var $valuemax = $progressbar.attr("aria-valuemax");
    var $newExp = parseInt($valuenow) + parseInt($exp);
    var $percent = 100 / $valuemax * $newExp;

    $progressbar.css({
        "width": $percent + "%",
    });

    $progressbar.attr("aria-valuenow", $newExp);

    setTimeout(function () {
        if ($newExp >= $valuemax) { 
			alert($newExp); 
			alert($valuemax); 

            $progressbar.css({
                "width": "0%",
            });

            $progressbar.attr("aria-valuenow", 0);

            var $rest = $newExp - $valuemax;
            raiseExp($rest);
			$(".levelvalue").html(parseInt($(".levelvalue").html())+1);
			
        }
		
    }, 1000)
}


/*
 * notify($title, $body, $icon)
 * Momenteel enkel nog maar supported voor chrome, mozilla en safari op mac osx (mobile: blackberry & partially supported door de android browser)
 * Stuurt een notificatie vanuit de browser naar de desktop, wanneer de gebruiker op de notificatie klikt, wordt de browser geopend indiend deze geminimaliseerd was en het huidig tabblad op OWAES gezet
 * $title   De titel voor de notificatie     type string  
 * $body    Het bericht van de notificatie   type string
 * $icon    De URL naar een afbeelding       type string (volledige url)
 * $url     De URL naar het event            type string (volledige url)
 */
function notify($title, $body, $icon, $url) {
	 htmlNotify($title, $body, $icon, $url);
	 return ; 
	 
    // Support de browser desktop notifications?
	console.log($title); 
	console.log($body); 
	console.log($icon); 
	console.log($url); 
	console.log("------------------------------"); 
    if (!("Notification" in window)) {
        console.log("JS: [notify] Deze browser ondersteunt geen desktop notificaties.");
        htmlNotify($title, $body, $icon, $url);
    }

    // Laat de gebruiker desktop notifications toe? (dan moet de else if na deze niet overlopen worden)
    else if (Notification.permission === "granted") {
        var $notification = new Notification($title, {
            "body": $body,
            "icon": $icon,
        });
		console.log("new Notification (lijn 555)"); 

        // Wanneer de browser geminimaliseerd was, terug openen en focus (current tab) op OWAES zetten
        $notification.onclick = function () {
            $notification.close();
            window.focus();
            window.location.href = $url;
        }
    }

    // Als het niet denied is (de browser weet het niet) => permissie vragen
    else if (Notification.permission !== 'denied') {
		console.log("new Notification (lijn 567)"); 
        console.log(Notification.permission);
        Notification.requestPermission(function (permission) {
            // De persmissie van de gebruiker opslaan
            if (!('permission' in Notification)) {
                Notification.permission = permission;
            }
            if (permission === "granted") {
                var $notification = new Notification($title, {
                    "body": $body,
                    "icon": $icon,
                });

                // Wanneer de browser gesloten was, terug openen en focus (current tab) op OWAES zetten
                $notification.onclick = function () {
                    $notification.close();
                    window.focus();
                    window.location.href = $url;
                }
            }

            // Notificaties werden NU niet toegestaan
            if (permission !== 'granted') {
                htmlNotify($title, $body, $icon, $url);
            }
        });
        console.log(Notification.permission);
    }

    // Notificaties werden DE VORIGE KEER niet toegestaan
    else if (Notification.permission === "denied") {
        htmlNotify($title, $body, $icon, $url);
		console.log("new Notification (lijn 599)"); 
    }
    
    // Er is iets misgelopen met desktop notifications, dan maar een gewone notification
    else {
        htmlNotify($title, $body, $icon, $url);
		console.log("new Notification (lijn 605)"); 
    }
}

function htmlNotify($title, $body, $icon, $url) {
    var $html = "";
    $html += "<div class=\"notification\">";
    $html += "<div class=\"media\">";
    $html += "<img class=\"media-object pull-left\" src=\"" + $icon + "\">";
    $html += "<div class=\"media-body odd\">";
    $html += "<button type=\"button\" class=\"close\" >&times;</button>"; 
    $html += "<a href=\"" + $url + "\">";
	$html += "<h4 class=\"media-heading\">" + $title + "</h4>";
    $html += "<p class=\"\">" + $body + "</p>";
	$html += "</a>"; 
    $html += "</div>"; //media-object                                     
    $html += "</div>"; //media
    $html += "</div>"; //notification

	if ($("body div.notifications").length == 0)  $("body").append($("<div />").addClass("notifications"));
    $("body div.notifications").append($html);

	// alert($body); 
 
    $(".notification .close").click(function () {
        $(this).parentsUntil(".notification").parent().remove();
    });
}