<?php
	include "inc.default.php"; // should be included in EVERY file  
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	 
	$oList = new grouplist(); 
         
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
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
                	<div class="info">
                  		<h2>Stagemarkt Roeselare 16 september 9 - 16u</h2> 
                    </div>
                    <ul>
					<?php  
						$oDB = new database(); 
						$oDB->execute("select * from tblStagemarktDates where student = " . me() . " order by slot;"); 
						while ($oDB->nextRecord()){
							echo "<li>Tijdslot " . $oDB->get("slot") . ": " . group($oDB->get("bedrijf"))->getLink() . "<li>"; 	
						} 
						
						$arGroupen = user(me())->groups(); 
						if (user(me())->admin()) { 
							$oGroupen = new grouplist(); 
							$arGroupen = $oGroupen->getList(); 
						}
						
						foreach($arGroupen as $oGroup) {
							$oDB->execute("select * from tblStagemarktDates where bedrijf = " . $oGroup->id() . " order by slot;"); 
							if ($oDB->length() > 0) {
								echo "<h3>" . $oGroup->naam() . "</h3>"; 	
								while ($oDB->nextRecord()){
									echo "<li>Tijdslot " . $oDB->get("slot") . ": " . user($oDB->get("student"))->getLink() . "<li>"; 	
								} 
							}
						} 
                    ?> 
                    </ul>
                </div>
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
