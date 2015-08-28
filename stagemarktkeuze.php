<?php
	include "inc.default.php"; // should be included in EVERY file  
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	if (!user(me())->levelrights("groepslijst")) stop("level");
 
	$oList = new grouplist(); 
    
	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload");     
	$oExperience->add(1);  

    $oPage->tab("lijsten");
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
        <style>
			.bedrijf { display: block; float: left; height: 100px; width: 50%; text-overflow: ellipsis; }
			.bedrijf .h100 {height: 90px; overflow: hidden; }
			.bedrijf h2 {font-size: 110%; font-weight: bold; margin: 0; display: inline-block;  }
			.bedrijf p.website {display: inline; margin-left: 15px; }
			.bedrijf p {margin: 0; }
			div.keuze {background: white; height: 100px; font-size: 50px; border: 2px dashed gray; display: inline-block; margin: 1.3%; padding: 10px; width: 17.4%; }
			div.addknop img {opacity: 0.4; filter: alpha(opacity=40);  }
			.bedrijf.hover.ok div.addknop img {opacity: 1; filter: alpha(opacity=100);  }
			.bedrijf.hover.ok {cursor: pointer; }
			a.add {}
		</style>
        <script>
			$(document).ready(function(e) {
                $(".bedrijf").mouseover(function(){
					$(this).addClass("hover");	
				}).mouseout(function(){
					$(this).removeClass("hover"); 	
				})
				$(".bedrijf a.link").mouseover(function(){
					$(this).parentsUntil(".bedrijf").parent().removeClass("ok");	
				}).mouseout(function(){
					$(this).parentsUntil(".bedrijf").parent().addClass("ok"); 
				})
				$(".bedrijf.ok").click(function() {
					console.log($(this).attr("rel")); 
					$("div.keuze").html($(this).attr("rel"));
				})
            });
		</script>
    </head>
    <body id="users">
        <?php echo $oPage->startTabs(); ?> 
    	<div class="body content content-lists-users container">
        	
            	<div class="row">
					<?php  
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                <div class="usersfromlist row sidecenterright"> 
					<?php 
                        for ($iKeuze = 1; $iKeuze <= 5; $iKeuze ++) {
                            echo ("<div class=\"keuze\">$iKeuze</div>"); 	
                        }
                        foreach ($oList->getList() as $oGroep) { 
                            echo "<div id=\"group-" . $oGroep->id() . "\" class=\"bedrijf ok\" rel=\"" . $oGroep->id() . "\">" . $oGroep->HTML("group_stagemarktkeuze.html") . "</div>";   
                        }
                    ?> 
                </div>
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
