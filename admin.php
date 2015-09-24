<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	if (!$oSecurity->admin()) stop("admin"); 
	
	if (isset($_GET["u"]))  user($_GET["u"])->status(TRUE); 
	
    //Variables for pages
    if(isset($_GET["start"])){
       $start = $_GET["start"];
    } else {
       $start = 0;
    }
    $limit = 10;//aantal records per pagina
    $linkNext = $start +$limit; // wordt op het laatst gebruikt om te zien of er een 'NEXT' link moet komen
    $back = $start - $limit; 
    $next = $start + $limit; 

	$oExperience = new experience(me());  
	$oExperience->detail("reason", "admin-users");     
	$oExperience->add(1);  

    //vanaf 50 tot $limitIndicator =  oranje, alles eronder is rood
    $limitIndicators = 40;
    
    // 
    $limitCreditsOranje = 1000;
    $limitCreditsRood = 2000;
    $limitDaysRood = 10;
    $limitDaysOranje = 2;
    
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="index">
         <?php echo $oPage->startTabs(); ?> 
         
    	<div class="body">
 	          
                <div class="container ">
                  <div class="row">
					<?php 
                    echo $oSecurity->me()->html("user.html");
					//echo user(me())->status(TRUE); 
                    ?>
                </div>
                    <div class="main market admin"> 
                    	<?php include "admin.menu.xml"; ?>
                         
                        <?php
							$arFields = array(
										// fieldname = array (title, warningkey)
											"score" => array("Score", ""), 
											"status" => array("!", ""), 
											"partnercount" => array("Partners", ""), 
											"transactiecount" => array("Transactie's", ""), 
											"diversiteit" => array("Diversiteit", "diversiteit"), 
											"schenkingen" => array("Schenkingen", "schenkingen"), 
										//	"straffen" => array("Straffen", ""), 
											"waarderingen" => array("Sterren", "waardering"), 
										//	"sterren" => array("Sterren", ""), 
											"credits" => array("Saldo", "credits"), 
											"social" => array("Social", "indicatoren.social"), 
											"physical" => array("Physical", "indicatoren.physical"), 
											"mental" => array("Mental", "indicatoren.mental"), 
											"emotional" => array("Emotional", "indicatoren.emotional"), 
											"indictatorensom" => array("Som", "indicatoren.som"), 
										);  	
				
                        	$oDB = new database(); 
							$oDB->execute("select * from tblUsers order by status desc; "); 
							echo ("<table class=\"database\">"); 
							echo ("<tr>"); 
							echo ("<th class='order'>Voornaam</th>");  
							echo ("<th class='order'>Naam</th>");  
							foreach ($arFields as $strField=>$arField) {
								$arClasses = array("order"); 
								if ($strField == "score") $arClasses[] = "asc"; 
								echo ("<th class='" . implode(" ", $arClasses) . "'>" . $arField[0] . "</th>"); 
							}
							echo ("<th class='order'>updated</th>");  
							echo ("<th>actie</th>");  

							echo ("</tr>");  
							while ($oDB->nextRecord()) {
								$arStatus = json_decode($oDB->get("statusinfo"), TRUE); 
								if (!is_array($arStatus)) $arStatus = array("warnings"=>array()); 
								echo ("<tr>"); 
								//  title='"); 
								//if (isset($arStatus["warnings"])) var_dump($arStatus["warnings"]); 
								//echo ("'
								echo ("<td class=\"status" . (isset($arStatus["status"])?$arStatus["status"]:"") . "\">" . $oDB->get("firstname") . "</td>");
								echo ("<td class=\"status" . (isset($arStatus["status"])?$arStatus["status"]:"") . "\">" . $oDB->get("lastname") . "</td>");
								foreach ($arFields as $strField=>$arField) {
									$iWarning = 0; 
									foreach ($arStatus["warnings"] as $iSeverity=>$arWarning) {
										if ($arField[1] != "") if (in_array($arField[1], $arWarning)) $iWarning = $iSeverity; 
										
									}
									echo ("<td class=\"status$iWarning\">" . (isset($arStatus[$strField])?round($arStatus[$strField]*100)/100:"") . "</td>"); 
								}
								//echo ("<td>" . (4800-user($oDB->get("id"))->credits()) . "</td>");
								echo ("<td value=\"" . $oDB->get("statusdate") . "\">" . str_date($oDB->get("statusdate"), "shortago") . "</td>");
								//echo ("<td>"); 
								//var_dump($arStatus["warnings"]); 
								//echo ("<td>"); 
								echo ("<td>
											<a href=\"admin.php?u=" . $oDB->get("id") . "\">refresh</a>
											<a href=\"admin.user.php?u=" . $oDB->get("id") . "\">meer</a>
										</td>");  
								echo ("</tr>"); 
							}
							echo ("</table>"); 
                            
                            function checkIndicator($indicator){
                                global $limitIndicators;
                                
                                if($indicator < $limitIndicators){
                                    return ("<td class='lowValue'>" . $indicator . "</td>");
                                }else if($indicator < 50 && $indicator > $limitIndicators){
                                    return ("<td class='orangeValue'>" . $indicator. "</td>");
                                }else{
                                    return ("<td>".$indicator."</td>");
                                }
                            }
                            
                            function checkCredits($credits){
                                global $limitCreditsOranje, $limitCreditsRood;
                                
                                $maxcreditsOr = 4800 +$limitCreditsOranje;
                                $mincreditsOr = 4800 -$limitCreditsOranje;
                                $maxcreditsRo = 4800 +$limitCreditsRood;
                                $mincreditsRo = 4800 -$limitCreditsRood;
                                
                                if($credits < $mincreditsRo || $credits > $maxcreditsRo){
                                    return ("<td class='lowValue'>".$credits."</td>");
                                }else if($credits < $mincreditsOr || $credits > $maxcreditsOr){
                                    return ("<td class='orangeValue'>".$credits."</td>");
                                }else{
                                    return ("<td>".$credits."</td>");
                                }
                            } 
							
							function statusClass($iValue, $strKey, $strOperand = ">") {
								$iStatus = 0; 
								foreach (settings("warnings") as $iID => $arTresholds) {
									if (isset($arTresholds[$strKey])){
										switch($strOperand) {
											case ">": 
												if ($iValue >= $arTresholds[$strKey]) $iStatus = $iID; 
												break; 
											case "<": 
												if ($iValue <= $arTresholds[$strKey]) $iStatus = $iID; 
												break; 	
										}	
									}
								}
								return "status" . $iStatus; 
							}
						?>
							 
                    </div>
                </div> 
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
