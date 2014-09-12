
$(document).ready(function(e) {
	$("table.editable").each(function(){
		$(this).wrap($("<form />").submit(function(){
			arSubmit = {}; 
			$(this).find(":input").each(function(){
				strID = $(this).attr("name");
				strVal = $(this).val(); 
				arSubmit[strID] = strVal; 
				$("#" + strID).addClass("saving"); 
			}); 
			$.ajax({
				type: "POST",
				url: "admin.dbsave.php",
				data: arSubmit, 
				dataType: "script"	
			});
			return false; 
		})).after(
			$("<input />").attr("type", "submit").attr("value", "Save").attr("class","btn btn-default save").attr("onclick","window.location.reload(true)")
		); 
	}) 
	$("table.editable td[id]").each(function(){
		strVal = $(this).text(); 
		$(this).html(
			$("<span />").text(strVal) 
		).attr("orig", strVal);
	}).click(function(){
		if ($(this).find(":input").length > 0) {
			$(this).find(":input").show();
			$(this).find("span").hide();
		} else {
			strVal = $(this).find("span").hide().text(); 
			strID = $(this).attr("id"); 
			$(this).append(
				$("<textarea />").attr("name", strID).val(strVal).blur(function(){
					strVal = $(this).val(); 
					strID = $(this).attr("name"); 
					if (strVal != $("#" + strID).attr("orig")) {
						$(this).hide(); 
						$("#" + strID).addClass("changed").find("span").show().text(strVal);
					} else { // ==
						$("#" + strID).removeClass("changed").find("span").show().text(strVal);
						$(this).remove();
					}
				})
			);  
		}
		$(this).find(":input").focus(); 
	})
	
	$(document).on("click", "a.userrights", function() { // admin.groepusers
		$(this).parent().load($(this).attr("href")); 
		return false; 	
	});  
	
	$("a#personenzoeken").click(function(){
		$("div#persoonzoekenresult").load("admin.groupusers.php", {"f": $("#persoonzoeken").val()}); 
		return false; 
	});
});