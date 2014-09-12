$(document).ready(function() {

	$(window).scroll(function() { // fixed header visible vanaf 120px
		if ($(window).scrollTop() > 120) {
			$("body").addClass("scrolled"); 
		} else {
			$("body").removeClass("scrolled"); 
		}  
	}); 
	
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
	
	$(":input").on("blur", function() {
		$(this).addClass("gepasseerd");
	}).on("change", function() {
		$(this).addClass("aangepast");
	}); 
	 
	 
	
	$(document).on("click", "a.addfriend", function(event){ // add as friend - links in overzicht gebruikers
		strLink = $(this).attr("href");  
		strTarget = $(this).parentsUntil(".userlistitem").parent().attr("id"); 
		$("<div />").load(strLink, {"return": "item"}, function() {
			$("#" + strTarget).replaceWith($(this)); 
		})
		console.log($("#" + strTarget)); 
		console.log(strTarget); 
		console.log(strLink); 
		return false; 	
	}) 

	$(document).on('click', "div.subscribe a.subscribe", function(event){ // "schrijf in"-knoppen
		$(this).parent().load($(this).attr("href") + "&ajax=1");
		return false; 
	}); 
	
	
	$("span.moreA").click(function() {  // shorten-functie (in functions-php-file)
		$(this).hide(); 
		$(this).next("span").slideDown(); 
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
	
	$("pre.vardump").each(function(){ // debugging-purposes (****)
		strFull = $(this).text(); 
		strTitle = $(this).attr("title")?($(this).attr("title") + ": " + strFull.split(" ")[0]):strFull.split(" ")[0]; 
		$(this).attr("rel", strFull).attr("title", strTitle).html(strTitle).mouseover(function(){
			$(this).text($(this).attr("rel")).addClass("popp");	
		}).mouseout(function(){
			$(this).text($(this).attr("title")).removeClass("popp");	
		})
	})
	
	$("input").change(function(){
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
		google.maps.event.trigger(map, 'resize');
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
	

	
	/*
	$("a.contact").mouseover(function(){
		strLink = $(this).attr("href"); 
		if($(this).next("div.contactpopup").length == 0){
			$("div.contactpopup").remove(); 
			$(this).after(
				$("<div />").addClass("contactpopup").html("loading").load(strLink, {ajax: 1}, function(){
					$(this).find("form").submit(function(){ 
						console.log($(this).find(":input[name=message]")); 
						$(this).load($(this).attr("action"), {message: $(this).find(":input[name=message]").val() , mail: 1, ajax: 1}, function(){
							$(this).html("saved"); 
						}); 
						return false; 
					})
				})
			);	
		}
	})
	*/
	
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
	

	
/*
	$("form#frmowaesadd ol.addtabs li a").click(function(){
		showfrmtab($(this).attr("href")); 
		return false; 
	});
	$("form#frmowaesadd ol.addtabs li:not(.actief) a").each(function(){
		strLink = $(this).attr("href"); 
		$(strLink).hide(); 
	})
	function checkTabs() {
		iTabOK=0
		iTabActief=0; 
		$("form#frmowaesadd ol.addtabs li").removeClass("disabled").each(function(){
			strTabID = $(this).find("a").attr("href"); 
			if (iTabOK == iTabActief) { 
				if (checkTab(strTabID)) {
					iTabOK++; 
					$(strTabID).find("a.volgende").removeClass("disabled");	
				} else {
					$(strTabID).find("a.volgende").addClass("disabled");	
				}
			} else $(this).addClass("disabled");  
			$(strTabID).find(":input.required").change(function(){
				checkTabs(); 
			})
			iTabActief ++; 
		}) 
	}
	function checkTab(strID) {
		bOK = true; 
		$(strID).find(":input.required").each(function(){ 
			if ($(this).val() == "") bOK = false; 	
		})
		return bOK; 
	}
	checkTabs(); 
	
	*/
	/*
	$("form#frmowaesadd :input").each(function() {
		strID = $(this).attr("id"); 
		if (strID != "") {
			// Sommige velden worden pas getoond wanneer andere actief staan
			switch($(this).attr("type")){
				case "checkbox": 
				case "radio":
					if (!$(this).is(':checked')) $("dd." + strID + ",dt." + strID).hide(); 
					break; 
				default: 
			} 
		}
	})


	$("form#frmowaesadd .buttons a").click(function() {
		showfrmtab($(this).attr("href")); 
		return false; 
	})
	
	function showfrmtab(strMetHash) {  
		$("form#frmowaesadd ol.addtabs li a").each(function(){
			strLink = $(this).attr("href"); 
			$(strLink).hide(); 
		});
		strLink = $(this).attr("href"); 
		$("form#frmowaesadd ol.addtabs li").removeClass("actief"); 
		$("form#frmowaesadd ol.addtabs li a[href='" + strMetHash + "']").parent().addClass("actief"); 
		$(strMetHash).show();  
		$("." + $(this).attr("id")).show(); 
		oCurrentPos = map.getCenter(); 
		google.maps.event.trigger(map, 'resize');
		map.panTo(oCurrentPos);  
	}
	*/
	 
	$(document).on("click","a.ajax[rel!='']",function(e){
		strRel = $(this).attr("rel"); 
		$("#" + strRel).load($(this).attr("href")); 
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

	arMessages = Array(); 
	function ping() { 
		$.getJSON(strRoot + "message.ajax.php", function( data ) { 
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
						oMessage["link"]
					);
					//setTimeout(function() { 
					//	$("#message_" + strKey).fadeOut("slow"); 
					//}, 90000);
				}

			}); 
		}); 
		setTimeout(ping, 30000);
	}
	ping();

	
	if ($(".convoColumn").length) {
	    $(".convoColumn").scrollTop($(".convoColumn")[0].scrollHeight);
	}

 
});
 


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
  