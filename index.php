<?
	include "inc.default.php"; // should be included in EVERY file  

	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	if (!isset($_GET["t"])) {
		redirect("main.php?start"); 
	} else {
		$strType = $_GET["t"]; 
	}
	 
	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload"); 
	$oExperience->add(1);  
	
	$oMe = user(me()); 
	$oMe->expBijAanmelding(); 
	
 
	// $strType = isset($_GET["t"]) ? $_GET["t"] : "market"; 
	// $strType = ($oPage->tab() == "opdrachten") ? "work" : "market"; 
  
	$oOwaesList = new owaeslist();  
	$oOwaesList->filterByType($strType); 
	$oOwaesList->filterByState(STATE_RECRUTE); 
	  
	$oOwaesList->filterPassedDate(owaesTime()); 
	$oOwaesList->optiOrder($oMe); 
	
/*
	$oOwaesList->enkalkuli("social", $oMe->social());
	$oOwaesList->enkalkuli("physical", $oMe->physical());
	$oOwaesList->enkalkuli("mental", $oMe->mental());
	$oOwaesList->enkalkuli("emotional", $oMe->emotional());
	// $oOwaesList->enkalkuli("location", $oMe->emotional());
	*/

	// $oOwaesList->setUser($oUser); 
	
 
	$oActions = new actions(me());  
	
	 
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
        <script>
			$(document).ready(function(){
				
				$("ul.waardenfilter li").click(function(){
					$(this).removeClass("show"); 
					switch($(this).attr("rel")) {
						case "true": 
							$(this).removeAttr("rel");  
							break;  
						default: 
							$(this).addClass("show").attr("rel", "true"); 
							break; 	
					} 
					showFilterResult(); 
					return false; 
				})
				
				function showFilterResult() { 
					arYes = Array(); 
					arNo = Array();  
					arWaarden = Array();  
					$("ul.waardenfilter li").each(function(){
						switch($(this).attr("rel")) {
							case "true": arWaarden[arWaarden.length] = $(this).find("a").attr("rel"); break; 
						} 
					});
					$("div#results").load("index.ajax.php", {"t": "<? echo $strType; ?>", "show": arYes, "hide": arNo, "waarden": arWaarden}); 
				}
				
				loadModals(<? echo json_encode($oActions->modals()); ?>);  
				
			})
		</script>
    </head>
    <body id="index">               
        <? echo $oPage->startTabs(); ?> 
    	<div class="body content content-market container">
        	
            	<div class="row">
					<? /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    echo $oMe->html("user.html");
                    ?>
                </div>
                 <!-- <div class="container sidecenterright"> -->
				<? //if (user(me())->level()>=2) { ?>
                    <div class="row">
                    	<? 
							$oNew = owaesitem(0); 
							$oNew->type($strType);  
							if ($oNew->editable()===TRUE) {
								?>
									<a href="owaesadd.php?t=<? echo $strType; ?>" class="btn btn-default">
										<span class="icon icon-plus"></span><span class="title">Aanbod toevoegen</span>
									</a>
								<? 
							} else {
								switch($oNew->editable()) {
									case "voorwaarden": 
										?>
											<a href="modal.voorwaarden.php" class="domodal btn btn-default">
												<span class="icon icon-plus"></span><span class="title">Aanbod toevoegen</span>
											</a>
										<? 
										break; 	
									case "level": 
										?>
											<a href="modal.levelneeded.php?l=<? echo $oNew->type()->minimumlevel(); ?>" class="domodal btn btn-default">
												<span class="icon icon-plus"></span><span class="title">Aanbod toevoegen</span>
											</a>
										<?	
										break;  
								}  
							}
						?>
                    </div>
                <? //} ?>
                
                <div class="row">
                    <div class="main market"> 
                        <div id="results">
                        <? 
                            foreach ($oOwaesList->getList() as $oItem) {  
                                echo $oItem->HTML("owaeskort.html"); 
                            }
                        ?>
                        </div>
                    </div>
                    </div>
                
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div> 
    </body>
</html>
