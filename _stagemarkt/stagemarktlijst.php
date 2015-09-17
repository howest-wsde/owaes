<?php
	include "inc.default.php"; // should be included in EVERY file  
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	if (!$oSecurity->admin()) stop("admin"); 
	 
	$oList = new grouplist(); 
         
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="users"> 
    	<div class="body content content-lists-users container">  
                <div class="usersfromlist row sidecenterright">  
                    <ul>
					<?php  
						$oDB = new database(); 
						
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
									echo "<li>Tijdslot " . $oDB->get("slot") . "" . slot($oDB->get("slot")) . ": " . user($oDB->get("student"))->getLink() . "</li>"; 	
								} 
							}
						} 
						
						echo "<hr>"; 
						
						$oUsers = new userlist();  
						foreach ($oUsers->getList() as $oUser) {
								
							$oDB->execute("select * from tblStagemarktDates where student = " . $oUser->id() . " order by slot;"); 
							if ($oDB->length() > 0) {
								echo "<h3>" . $oUser->getLink() . "</h3>"; 	
								while ($oDB->nextRecord()){
									echo "<li>Tijdslot " . $oDB->get("slot") . "" . slot($oDB->get("slot")) . ": " . group($oDB->get("bedrijf"))->getLink() . "</li>"; 	
								} 
							}
						}
						
						
						function slot($i) { 
							switch($i) {
								case 1: return " (13u30 - 13u45)"; 
								case 2: return " (13u45 - 14u00)";  
								case 3: return " (14u00 - 14u15)";  
								case 4: return " (14u15 - 14u30)"; 
								case 5: return " (15u00 - 15u15)";  
								case 6: return " (15u15 - 15u30)"; 
								case 7: return " (15u30 - 15u45)"; 
								case 8: return " (15u45 - 16u00)";  
										
							}
							return ""; 
						}
                    ?> 
                    </ul>
                </div> 
        </div> 
    </body>
</html>
