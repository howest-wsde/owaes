<?php
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
	
	$oActions = new actions(me());  
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
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
					$("div#results").load("index.ajax.php", {"t": "<?php echo $strType; ?>", "show": arYes, "hide": arNo, "waarden": arWaarden}); 
				}
				
				loadModals(<?php echo json_encode($oActions->modals()); ?>);  
				
			})
		</script>
    </head>
    <body id="index">               
        <?php echo $oPage->startTabs(); ?> 
    	<div class="body content content-market container">
        	
            <div class="row">
                <?php /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                echo $oMe->html("user.html");
                ?>
            </div>
             <!-- <div class="container sidecenterright"> --> 
                <div class="row">
                    <?php 
                        $oNew = owaesitem(0); 
                        $oNew->type($strType);  
                        if ($oNew->editable()===TRUE) {
                            ?>
                                <a href="owaesadd.php?t=<?php echo $strType; ?>" class="btn btn-default">
                                    <span class="icon icon-plus"></span><span class="title">Aanbod toevoegen</span>
                                </a>
                            <?php 
                        } else {
                            switch($oNew->editable()) {
                                case "emailverify": 
                                    ?>
                                        <a href="modal.mailnotverified.php" class="domodal btn btn-default">
                                            <span class="icon icon-plus"></span><span class="title">Aanbod toevoegen</span>
                                        </a>
                                    <?php 
                                    break; 	
                                case "voorwaarden": 
                                    ?>
                                        <a href="modal.voorwaarden.php" class="domodal btn btn-default">
                                            <span class="icon icon-plus"></span><span class="title">Aanbod toevoegen</span>
                                        </a>
                                    <?php 
                                    break; 	
                                case "level": 
                                    ?>
                                        <a href="modal.levelneeded.php?l=<?php echo $oNew->type()->minimumlevel(); ?>" class="domodal btn btn-default">
                                            <span class="icon icon-plus"></span><span class="title">Aanbod toevoegen</span>
                                        </a>
                                    <?php
                                    break;  
                            }  
                        }
                    ?>
                    <?php 
							$oOwaesList = new owaeslist();  
							$oOwaesList->filterByType($strType); 
							$oOwaesList->filterByState(STATE_RECRUTE); 
							  
							$oOwaesList->filterPassedDate(owaesTime()); 
							$bDesc = isset($_GET["d"])?($_GET["d"]==1):FALSE; 
							switch(isset($_GET["order"])?$_GET["order"]:"") {
								case "distance": 
									$oOwaesList->order("distance", $bDesc); 
									$strOrder = "Afstand";  
									break; 
								case "creation": 
									$oOwaesList->order("creation", $bDesc);  
									$strOrder = "Datum creatie";   
									break; 
								case "task": 
									$oOwaesList->order("task", $bDesc);  
									$strOrder = "Datum uitvoering";     
									break; 
								default: 
									$oOwaesList->optiOrder($oMe); 
									$strOrder = "Profielopbouw";  
									$bDesc = FALSE; 
							}
							
					?>
                                         
                    <div class="btn-group">
                            <button type="button" href="index.php" class="btn btn-default" onclick="location.href='<?php echo fixPath("index.php?" . qry(array("d"=>($bDesc?0:1)))); ?>';"><span class="icon icon-order-<?php echo ($bDesc?"desc":"asc"); ?>"></span><span class="title"><?php echo $strOrder; ?></span></button>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 17px 10px 18px; ">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                            </button>
                      <ul class="dropdown-menu">
                        <li><a href="<?php echo fixPath("index.php?" . qry(array(), array("order", "d"))); ?>">Profielopbouw</a></li>
                        <li><a href="<?php echo fixPath("index.php?" . qry(array("order"=>"creation", "d"=>1))); ?>">Datum creatie</a></li>
                        <li><a href="<?php echo fixPath("index.php?" . qry(array("order"=>"task"), array("d"))); ?>">Datum uitvoering</a></li>
                        <?php
                            if ($oMe->latitude() * $oMe->longitude() == 0) {
                                echo ("<li><a href=\"" . fixPath("modal.adres.php?r=" . urlencode(fixPath("index.php?" . qry(array("order"=>"distance"), array("d"))))) . "\" class=\"domodal\">Afstand</a></li>"); 
                            } else {
                                echo ("<li><a href=\"" . fixPath("index.php?" . qry(array("order"=>"distance"), array("d"))) . "\">Afstand</a></li>"); 
                            }
                        ?> 
                      </ul>
                    </div>  
                     
                </div> 
                
                <div class="row">
                    <div class="main market"> 
                        <div id="results">
                        <?php 
						
							 
                            foreach ($oOwaesList->getList() as $oItem) {  
                                echo $oItem->HTML("owaeskort.html"); 
                            }
                        ?>
                        </div>
                    </div>
                    </div>
                
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div> 
    </body>
</html>
