$(document).ready(function() {

	$(window).scroll(function() { // fixed header visible vanaf 120px
		if ($(window).scrollTop() > 120) {
			$("body").addClass("scrolled"); 
		} else {
			$("body").removeClass("scrolled"); 
		}  
	}); 
	 
	/* WYSIWYG - editor - START */
	$("textarea.wysiwyg").each(function(){ 
		strClasses = $(this).attr("class");  
		strCall = $(this).attr("id"); 
		if (!strCall) strCall = $(this).attr("name");  
		CKEDITOR.inline( strCall , {
			on: {
				blur: function( event ) { 
					strID = this.element.$.id;  
					$("#" + strID).parent().removeClass("focus"); 
					event.editor.updateElement();  
				}, 
				focus: function (event) {
					strID = this.element.$.id;  
					$("#" + strID).parent().addClass("focus");  
				}
			}
		} ); 
		$(".cke_textarea_inline").addClass(strClasses); 
	});  
	
	$.fn.modal.Constructor.prototype.enforceFocus = function () {
		var $modalElement = this.$element;
		$(document).on('focusin.modal', function (e) {
			var $parent = $(e.target.parentNode);
			if ($modalElement[0] !== e.target && !$modalElement.has(e.target).length
				// add whatever conditions you need here:
				&&
				!$parent.hasClass('cke_dialog_ui_input_select') && !$parent.hasClass('cke_dialog_ui_input_text')) {
				$modalElement.focus()
			}
		})
	};
	/* WYSIWYG - editor - END */
		
	$(document).on('click', "input[type=submit]", function(event){ // gebruikte submit-knop de class "submit" geven (om te detecteren welke submit-knop gebruikt werd)
		$("input[type=submit]").removeClass("clicked");
		$(this).addClass("clicked");
	}); 
	 
	$(document).on("click", "a.login[rel!='']", function(event){ // login-openID-links
		strSize = $(this).attr("rel"); 
		arSize = strSize.split(","); 
		window.open($(this).attr("href"),'login', 'width=' + arSize[0] + ',height=' + arSize[1] + ',scrollbars=no,toolbar=no,location=no');
		return false; 	
	})
	
	$(document).on("click", "a.loadmore", function(){ 
		oLink = this; 
		arVars = {};  
		$.each(this.attributes, function(i, attrib){
			arVars[attrib.name] = attrib.value; 
		});  
		strID = $(this).attr("href").split("#")[1]; 
		oEl = $("a[name='" + strID + "']"); 
	 	$("<div>").load("loadmore.php", arVars, function(strResult) {
			oEl.before($(strResult)); 
			if (strResult.indexOf("<!-- EOL -->")>=0) {
				$(oLink).hide(); 
			} else {
				$(oLink).attr("start", parseInt($(oLink).attr("start"))+parseInt($(oLink).attr("count")));
			}
		});
		return false; 
	});
	
	$(document).on("change", "input[ext]", function(){
		if ($(this).attr("type") == "file") { 
			arExt = $(this).attr("ext").toLowerCase().split(","); 
			strFile = $(this).val().match(/\.([^\.]+)$/)[1]; 
			if(arExt.indexOf(strFile.toLowerCase())<0) {
				modalalert("<p>De extentie '" + strFile + "' wordt niet ondersteund. </p><p>Ondersteunde bestandsextenties: " + arExt.join(", ") + "</p>"); 
				$(this).val(""); 
			}
		}
	});  
	
	
	$("ol.flow li.notconfirmed").mouseover(function(){
		oA = $(this).find("a");
		oA.attr("oud", oA.html()); 
		$(this).find("a").html("Uitschrijven");
	}).mouseout(function(){
		$(this).find("a").html(oA.attr("oud"));
	});  
	
	// start ACTION MODAL BUTTONS

	$(document).on("click", ".later-form", function(){  
		oForm = $(this).parentsUntil("form").parent(); 
		arData = oForm.serialize(); 
		arData = "cancel=1&" + arData;  
		$.post(oForm.attr("action"), arData);  
	})
	$(document).on("click", ".postpone-form", function(){  
		oForm = $(this).parentsUntil("form").parent(); 
		arData = oForm.serialize(); 
		arData = "postpone=" + $(this).attr("rel") + "&cancel=1&" + arData;  
		$.post(oForm.attr("action"), arData);  
	})
	

	$(document).on("click", ".save-form", function(){  
		oForm = $(this).parentsUntil("form").parent(); 
		arData = oForm.serialize(); 
		bRefresh = (oForm.find("input[name='refresh']").val()==1);  
		strRedirect = (oForm.find("input[name='redirect']").val() || false);  
		$.post(oForm.attr("action"), arData, function(){
			if (strRedirect) {
				window.location=strRedirect; 
			} else if (bRefresh) location.reload(); 
		});  
	})
	
	$(document).on("click", "a.domodal", function(){ 
		loadModals(Array($(this).attr("href")));
		return false; 
	}); 
	
	// END ACTION MODAL BUTTONS
	
	$(":input").on("blur", function() {
		$(this).addClass("gepasseerd");
	}).on("change", function() {
		$(this).addClass("aangepast");
	}); 
	
	$(":input.forceblank").val(""); 
	
	$("th.order").each(function(){
		// $(this).append("&gt;"); 
	}).click(function() {
		bUp = !($(this).hasClass("asc"));
		$("th.order").removeClass("asc").removeClass("desc")
		$(this).addClass(bUp ? "asc" : "desc"); 
		iCol = $(this).index();
		oTable = $(this).parentsUntil("table").parent();  
		$(oTable).find("tr").each(function(){ 
			$(this).attr("order", orderValue($(this).find("td:eq(" + iCol + ")").text()));
		}) 
		$(oTable).find("tr:not(:eq(0))").each(function(){
			oA = $(this); 
			$(oTable).find("tr:not(:eq(0))").each(function(){ 
				oB = $(this); 
				if (bUp) {
					if ($(oA).attr("order") > $(oB).attr("order")) $(oA).insertAfter($(oB)); 
				} else {
					if ($(oA).attr("order") < $(oB).attr("order")) $(oA).insertAfter($(oB)); 
				}
			}) 
		})  
	});  
	if ( $("th.order.asc").length + $("th.order.desc").length == 0) {
		$("th.order:first").click(); 
	} else {
		$("th.order.desc,th.order.asc").click().click();
	}
	function orderValue(str) {
		if (!isNaN(str)) {
			str = "0000000000000000" + str; 
			return str.substring(str.length - 16); 
		}
		return str.toUpperCase();
	}
	
	$(document).on('click', "div.subscribe a.subscribe", function(event){ // "schrijf in"-knoppen
		$(this).parent().load($(this).attr("href") + "&ajax=1");
		return false; 
	}); 
	
	
	$("div.moreA").click(function() {  // shorten-functie (in functions-php-file)
		$(this).hide(); 
		$(this).next("div").slideDown(); 
	})
	
	$("div").each(function(){ // geeft classes aan even/oneven items, voor layout-purposes)
		iTeller = 0; 
		$(this).find(">div").each(function(){
			$(this).addClass((++iTeller%2==0)?"even":"odd"); 
		})
	}) 
	$("li:first-child").addClass("first");  // geeft classes aan eerste item, voor layout-purposes)
	$("li:last-child").addClass("last");  // geeft classes aan laatste item, voor layout-purposes)
	
	
	$("#ADMIN a").click(function(){ // debugging-purposes (****)
		strClass = $(this).attr("rel"); 
		$("." + strClass).toggle(); 
		return false; 
	})
	
	$("textarea.vardump").each(function(){ // debugging-purposes (****)
		strFull = $(this).text(); 
		strTitle = $(this).attr("title")?($(this).attr("title") + ": " + strFull.split(" ")[0]):strFull.split(" ")[0]; 
		$(this).attr("rel", strFull).attr("title", strTitle).html(strTitle).mouseover(function(){
			$(this).text($(this).attr("rel")).addClass("popp");	
		}).mouseout(function(){
			$(this).text($(this).attr("title")).removeClass("popp");	
		})
	})
	
	$("input.showhide").change(function(){
		switch($(this).attr("type")){
			case "checkbox": 
				if ($(this).is(':checked')) {
					$("." + $(this).attr("id")).show(); 
				} else { 
					$("." + $(this).attr("id")).hide(); 
				}
				break; 
			case "radio": 
				$("input[name='" + $(this).attr("name") + "']").each(function(){ 
					if ($(this).is(':checked')) { 
						$("." + $(this).attr("id")).show(); 
					} else { 
						$("." + $(this).attr("id")).hide(); 
					}
				})
				break; 
		}
		try {
		   google.maps.event.trigger(map, 'resize');
		} catch(err) {}
	})
	  
	$("dl.steps").each(function(){ 
		$(this).find("dt:eq(1)").hide().nextAll().hide(); 
		$(this).find(".required").change(function(){  
			nextStep(this); 
		}).keyup(function(){  
			if ($(this).val() != "") nextStep(this); 
		})
		$(this).find(":input").each(function(){
			strID = $(this).attr("id");  
			$("." + strID + "_required").addClass("test").change(function(){  
				nextStep(this); 
			}).keyup(function(){  
				if ($(this).val() != "") nextStep(this); 
			})  
		})
		/*
		$(this).find(":input").change(function(){  
			nextStep(this); 
		})
		$(this).find(":input").keyup(function(){  
			nextStep(this); 
		})
		*/
	})
	
	
	initBadgeHover(); 
	
 
	
	$("div.checkboxer").mouseover(function(){
		$(this).addClass("hover");
	}).mouseout(function(){
		$(this).removeClass("hover");
	}).click(function(){
		var oCB = $("input[id=" + $(this).attr("rel") + "]");
		bCB = !oCB.prop("checked"); 
		oCB.prop("checked", bCB);
		if (bCB) {
			$(this).addClass("checked") 
		} else {
			$(this).removeClass("checked");
		}
		iCount = $("div#checkusers input:checked").length; 
		$("div.count").html(iCount + " gebruiker" + ((iCount==1)?"":"s") + " geselecteerd");
	});
	
	$("div#checkusers a.deleteuser").click(function(){ 
		$(this).parent().load($(this).attr("href")); 
		return false; 	
	})
	
 
	$(document).on("click","a.ajax[rel!='']",function(e){
		strRel = $(this).attr("rel"); 
		strLink = $(this).attr("href"); 
		arLink = strLink.split("?"); 
		if (arLink.length == 1) {
			strLink = strLink + "?ajax"; 
		} else {
			strLink = strLink + "&ajax"; 
		}
		$("#" + strRel).load(strLink); 
		return false; 	
	})
	$(document).on("click","a.ajax:not([rel])",function(e){  
		strLink = $(this).attr("href"); 
		arLink = strLink.split("?"); 
		if (arLink.length == 1) {
			strLink = strLink + "?ajax"; 
		} else {
			strLink = strLink + "&ajax"; 
		}
		$.ajax(strLink);  
		return false; 	
	})
	
	
	$(document).on("submit","form.rating",function(e){
		$(this).load($(this).attr("action") + "?ajax=1", $(this).serialize());   
		return false; 	
	}).on("click","form.rating a",function(e){ 
		iScore = $(this).attr("rel");  
		oForm = $(this).parentsUntil("form").parent(); 
		oForm.find("input[name=score]").val(iScore); 
		oForm.find("dd.actief").removeClass("actief"); 
		for (i=1; i<=iScore; i++) oForm.find("dd.stars" + i).addClass("actief"); 
		return false; 	
	})
	
	$(document).on("click","a.transactie",function(e){
		popwindow(Array($(this).attr("href")), $("body")); 
		return false; 
	}); 
	
	$("div.userid").mouseover(function(){
		userID = $(this).attr("rel"); 
		oEl = $(this);  
		strFile = $(this).hasClass("user") ? "profile.popup.php" : ($(this).hasClass("group") ? "group.popup.php" : "profile.popup.php"); 
		strURL = strRoot + strFile + "?id=" + escape(userID); 
		iUserBoxTimer = setTimeout(function(){ 
			$("body").append(
				$("<div />").attr("id", "test").addClass("profilepop").html("loading").css({position: 'absolute', display: 'block', top: oEl.offset().top, left: oEl.offset().left}).mouseleave(function(){
					$(this).remove();
				}).load(strURL, function(){
					// initBadgeHover(); 	
				}) 
			); 
		}, 800);  
	}).mouseout(function(){
		clearTimeout(iUserBoxTimer);
	})
	
	sameHeight(); 
	
	$("form").submit(function(){
		$(".unsaved").removeClass("unsaved"); 	
	});
	

	function clock() {
		$("div.clock").load(strRoot + "clock.php"); 	
		setTimeout(clock, 30000);
	}
	clock();  


	
	if ($(".convoColumn").length) {
	    $(".convoColumn").scrollTop($(".convoColumn")[0].scrollHeight);
	}

	ping();
 
});

arMessages = Array(); 
function ping(arSend) { 
	arSend = arSend || {}; 
	$.getJSON(strRoot + "message.ajax.php", arSend, function( data ) { 
//		console.log(data); 
		$.each( data, function(strKey, oMessage) {
		//	console.log(oMessage); 
			if (typeof arMessages[strKey] == 'undefined') {
				arMessages[strKey] = oMessage; 
				/*
				elMessage = $("<p>").html(oMessage["message"]);
				if (oMessage["link"]) elMessage = $("<a>").attr("href", oMessage["link"]).append(elMessage); 
				$("ul.popupmessages").prepend(
					$("<li />").attr("id", "message_" + strKey).hide().append(
						elMessage
					).append(
						$("<a>").addClass("close").html("x").attr("href", "#").click(function(){
							$(this).parentsUntil("ul").remove(); 
							return false; 
						})
					).fadeIn("slow")
				);  
				*/
				notify( // ($title, $body, $icon, $url) 
					oMessage["title"], 
					oMessage["message"], 
					oMessage["icon"], 
					oMessage["link"],
					strKey
				);
				//setTimeout(function() { 
				//	$("#message_" + strKey).fadeOut("slow"); 
				//}, 90000);
			}

		}); 
	}); 
	setTimeout(ping, 30000);
}
  
function loadModals(arURLs) {
	if (arURLs.length > 0) {
		strURL = arURLs.shift();  
		$("body").append(
			$("<div />").load(strURL, function(){ 
				$(this).find(":first").modal({
					show: true,
					backdrop: "static",
					keyboard: false
				}).on('hide.bs.modal', function (e) {
					loadModals(arURLs); 
				})
			})
		); 	
	}
}

function sameHeight() {
	$(".sameheight").css('height', '').each(function(){ // maakt alle siblings met .sameheight even hoog
		iH = $(this).height(); 
		$(this).parent().find(">.sameheight").each(function() {
			if ($(this).height() > iH) iH = $(this).height(); 
		}).each(function() {
			$(this).height(iH); 
		})
	});
}

function popwindow(arContent, oElement) {
   
	// strContent = ""; 
    oNew = $("<div />").addClass("popwindow");
    //oNew.append($("<div class='modal fade' id='mymodal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>"));
    //oNew.append($("<div class='modal-dialog'>"));
    //oNew.append($("<div class='modal-content'>"));
    //oNew.append($("<div class='modal-header'><button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button> <h4 class='modal-title'>Modal title</h4></div>"));
    //oNew.append($("<div class='modal-body'>"));
	for (i = 0; i < arContent.length; ++i) {
		oEl = arContent[i]; 
		switch(typeof oEl) {
			case "string": 
				// strContent += oEl; 
				$.ajax({
					url: oEl, 
					success: function(data){
						oNew.append($("<div />").html(data));
					},
					error: function(data){
						oNew.append($("<div />").html(oEl)); 
					},
				})
				//oNew.append(
				//	$("<div />").html(oEl)
				//)
				break; 
			case "object":
				oEl.each(function(){ 
					// strContent += $(this).html(); 
					oNew.append($(this)); 
				})
				break;  	
		}
		// console.log(oEl);
		// console.log ();  
	} 
	oNew.append($("</div>"));
	//oNew.append($("<div class=modal-footer>hallo</div></div></div></div>"));
	$("body").css({overflow: "hidden"}).append(
		$("<div />").addClass("popbackground") 	
	); 
	oElement.append(
		$("<a />").attr("href", "#").addClass("popclose").append("<img src='img/close.png' />").click(function(){
			$("a.popclose").remove(); 
			$("div.popwindow").remove(); 
			$("div.popbackground").remove(); 
			$("body").css({overflow: ""}); 
			return false; 
		})
	).append(
		oNew 
		// $("<div />").addClass("popwindow").html(strContent)
	)

}

$(window).bind('beforeunload', function(){ 
	if ($(".unsaved").length > 0){
		return "De aanpassingen werden niet opgeslaan en zullen verloren gaan als je deze pagina verlaat. Weet u zeker dat je deze pagina wilt verlaten? ";
	}
});
 
iUserBoxTimer = 0; 
 
function nextStep(oEl) {
	// als er items zijn met class gelijk aan id van huidige inputfield > deze zichtbaar maken. Anders volgende <dt> 
	strID = $(oEl).attr("id"); 
	iInputs = 0;  
	if ($("." + strID).length == 0) { 
		bShowNext = true; 
		$(oEl).parentsUntil("dl").prev().nextUntil("dt").next("dt").show().nextUntil("dt").show().each(function(){ 
			$(this).find(":input").each(function() { 
				strID = $(this).attr("id");  
				oLast = this; 
				$("." + strID).hide(); 
//				console.log(strID + ": " + $(this).hasClass("required") ); 
				if ($(this).hasClass("required")) bShowNext = false; 
				iInputs++; 
			})
		});  
		if (bShowNext && (iInputs>0)) nextStep(oLast); 
	} else { 
		$("." + strID).show(); 
	}
}
 
function initBadgeHover(){ 
	//$("ul.badges li img").mouseover(function(){
	//	strTitel = $(this).attr("alt"); 
	//	strInfo = $(this).attr("rel");  
	//	$(this).parent().append(
	//		$("<div />").attr("id", "tempEl").addClass("badgeinfo").append(
	//			$("<h2 />").text(strTitel)
	//		).append(
	//			$("<p />").text(strInfo)
	//		).mouseleave(function(){
	//			$("#tempEl").remove();
	//		})
	//	);	
	//}).mouseout(function(){
	//	$("#tempEl").remove();
	//})
}
 
  
function tweecijfers(iGetal) {
	strGetal = "00" + iGetal; 	
	return strGetal.substring(strGetal.length-2);
}


//Conversation page, een klasse 'active' toevoegen als erop geklikt is
//$(document).on("click", "#convo", function (event) {
//    $(".conversationActive").removeClass("conversationActive");
//    $(this).addClass("conversationActive");
//    return false;
//})



function setInputSelection(input, startPos, endPos) {
	input.focus();
	if (typeof input.selectionStart != "undefined") {
		input.selectionStart = startPos;
		input.selectionEnd = endPos;
	} else if (document.selection && document.selection.createRange) {
		// IE branch
		input.select();
		var range = document.selection.createRange();
		range.collapse(true);
		range.moveEnd("character", endPos);
		range.moveStart("character", startPos);
		range.select();
	}
}
  
  
 
function getLatLong(strZoek, fResult, fError) {   // gebruik: getLatLong("Ieper", function(iLat, iLong) {alert(iLat); }, function() {alert('fout'); }); 
	if (strZoek != "") { 
		$.ajax({
			type: "POST",
			url: "details.location.php", 
			data: "search=" + escape(strZoek), 
			success: function(strResult){ 
				arLoc = strResult.split("|"); 
				if (arLoc.length == 2) {
					fResult(arLoc[1], arLoc[0]); 
				} else fError(); 
			}
		}); 
	} 
}
 
function distance(lat1, lon1, lat2, lon2) {
	var radlat1 = Math.PI * lat1/180
	var radlat2 = Math.PI * lat2/180
	var radlon1 = Math.PI * lon1/180
	var radlon2 = Math.PI * lon2/180
	var theta = lon1-lon2
	var radtheta = Math.PI * theta/180
	var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
	dist = Math.acos(dist)
	dist = dist * 180/Math.PI
	dist = dist * 60 * 1.1515
	dist = dist * 1.609344 ;  
	return dist
}

function modalalert(strT1, strT2) { 
	if (typeof strT2 === "undefined") {
		strTitle = "Foutmelding"; 
		strTekst = strT1; 
	} else {
		strTitle = strT1; 
		strTekst = strT2;  
	}
	loadModals(Array("modal.alert.php?t=" + escape(strTitle) + "&a=" + escape(strTekst)));
}