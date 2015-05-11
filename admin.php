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
											"credits" => array(settings("credits", "name", "x"), "credits"), 
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
							echo ("<th>Naam </th>");  
							foreach ($arFields as $strField=>$arField) {
								echo ("<th>" . $arField[0] . "</th>"); 
							}
							echo ("<th>updated</th>");  
							echo ("<th>actie</th>");  

							echo ("</tr>");  
							while ($oDB->nextRecord()) {
								$arStatus = json_decode($oDB->get("statusinfo"), TRUE); 
								echo ("<tr>"); 
								//  title='"); 
								//if (isset($arStatus["warnings"])) var_dump($arStatus["warnings"]); 
								//echo ("'
								echo ("<td class=\"status" . (isset($arStatus["status"])?$arStatus["status"]:"") . "\">" . $oDB->get("firstname") . " " . $oDB->get("lastname") . "</td>");
								foreach ($arFields as $strField=>$arField) {
									$iWarning = 0; 
									foreach ($arStatus["warnings"] as $iSeverity=>$arWarning) {
										if ($arField[1] != "") if (in_array($arField[1], $arWarning)) $iWarning = $iSeverity; 
										
									}
									echo ("<td class=\"status$iWarning\">" . (isset($arStatus[$strField])?round($arStatus[$strField]*100)/100:"") . "</td>"); 
								}
								echo ("<td>" . str_date($oDB->get("statusdate"), "shortago") . "</td>");
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
                            
                            /* 
                            $oDB->sql( "SELECT COUNT(*) AS aantal FROM tblUsers");
                            $oDB-> execute();
                            $count = $oDB->get("aantal"); 

							$arTable = array(
									"id" => "ID", 
									"firstname" => "firstname", 
									"lastname" => "lastname", 
									"physical" => "physical", 
									"mental" => "mental", 
									"emotional" => "emotional", 
									"social" => "social", 
									"_som" => "som", 
									"waardering" => "waardering", 
									"schenkingen" => "schenkingen", 
									"_credits" => "credits", 
									"creditsin" => "in", 
									"creditsout" => "uit",  
									"_diversiteit" => "diversiteit",  
							//		"lastupdate" => "lastupdate", 
							//		"clickdate" => "Laatst ingeschreven", 
							//		"postdate" => "Laatst item geplaatst", 
							//		"lastlogin" => "Last Login", 
									
								);  
							$strOrder = (isset($_GET["order"])?$_GET["order"]:""); 
							if (!in_array($strOrder, array_keys($arTable))) $strOrder = "lastname"; 
							
							
							$arSelect = array(
								" i.emotional, i.social, i.physical, i.mental" => "(select user, sum(emotional)  as emotional, sum(social) as social, sum(physical) as physical, sum(mental) as mental from tblIndicators where actief = 1 group by user ) as i on u.id = i.user ", 
								"creditsin, countin" => "(select receiver as user, sum(credits) as creditsin, count(credits) as countin from tblPayments where actief = 1 group by user) as pIn on u.id = pIn.user ",  
								"vSchenkingen.schenkingen" => "(select sender, count(id) as schenkingen from tblPayments where market = 0 and actief = 1 and  datum > " . (owaesTime()-(60*24*60*60)) . " group by sender) vSchenkingen on u.id = vSchenkingen.sender", 
							//	"max(ms2.clickdate) as clickdate" => "tblMarketSubscriptions ms2 on u.id = ms2.doneby ", 
							//	"max(m.lastupdate) as lastupdate,  max(m.date) as postdate" => "tblMarket m on u.id = m.author  ", 
							//	"l.datum as lastlogin" => "(select max(datum) as datum, user from tblLog group by user) as l on u.id = l.user",   
								"vWaardering.waardering, vWaardering.aantalwaarderingen" => "(select receiver, count(id) as aantalwaarderingen, sum(stars) as waardering from tblStars where market != 0 group by receiver) as vWaardering on u.id = vWaardering.receiver", 
								"vStraffen.straf, vStraffen.aantalstraffen" => "(select receiver, count(id) as aantalstraffen, sum(stars) as straf from tblStars where market = 0 group by receiver) as vStraffen on u.id = vStraffen.receiver", 
								"count(distinct vPartners.pB) as partnercount, count(vPartners.pB) as transcount" => "((select receiver as pA, sender as pB from tblPayments) union (select sender as pA, receiver as pB from tblPayments)) as vPartners on u.id = vPartners.pA ",
								"creditsout, countout" => "(select sender as user, sum(credits) as creditsout, count(credits) as countout from tblPayments where actief = 1 group by user) as pUit on u.id = pUit.user ",  
							); 
							 
							$strSQL = "select u.id, u.firstname, u.lastname, " . implode(", ", array_keys($arSelect)) . " 
											from tblUsers u 
											left join " . implode(" left join ", array_values($arSelect)) . "  
											group by u.id
											order by $strOrder; "; 
											 
							//echo $strSQL ; 
							$oDB->execute($strSQL);
							echo ("<table class=\"database\">");  
							echo ("<tr>");
							
							foreach ($arTable as $strField=>$strTitle) {
								if (substr($strField, 0, 1) == "_") {
									echo("<th>$strTitle</th>");
								} else {
									if($strOrder==$strField){
										echo("<th class='adminFilter'><a href='admin.php?order=$strField'>$strTitle<span class='caret'></span></a></th>");
									}else{
										echo("<th><a href='admin.php?order=$strField'>$strTitle<span class='caret'></span></a></th>");
									}	
								}
							}
 
							echo("<th></th></tr>"); 
							while ($oDB->nextRecord()) {
								echo ("<tr>"); 
                                echo ("<td>" . $oDB->get("id") . "</td>"); 
								echo ("<td>" . $oDB->get("firstname") . "</td>"); 
								echo ("<td>" . $oDB->get("lastname") . "</td>"); 
								
								echo ("<td class=\"" . statusClass(settings("startvalues", "physical") + $oDB->get("physical"), "physical", "<") . "\">" . (settings("startvalues", "physical") + $oDB->get("physical")) . "</td>"); 
								echo ("<td class=\"" . statusClass(settings("startvalues", "mental") + $oDB->get("mental"), "mental", "<") . "\">" . (settings("startvalues", "mental") + $oDB->get("mental")) . "</td>"); 
								echo ("<td class=\"" . statusClass(settings("startvalues", "emotional") + $oDB->get("emotional"), "emotional", "<") . "\">" . (settings("startvalues", "emotional") + $oDB->get("emotional")) . "</td>"); 
								echo ("<td class=\"" . statusClass(settings("startvalues", "social") + $oDB->get("social"), "social", "<") . "\">" . (settings("startvalues", "social") + $oDB->get("social")) . "</td>"); 
								
								echo ("<td class=\"" . statusClass((settings("startvalues", "physical")+settings("startvalues", "mental")+settings("startvalues", "emotional")+settings("startvalues", "social")+$oDB->get("physical")+$oDB->get("mental")+$oDB->get("emotional")+$oDB->get("social")), "indicatorsom", "<") . "\">" . (settings("startvalues", "physical")+settings("startvalues", "mental")+settings("startvalues", "emotional")+settings("startvalues", "social")+$oDB->get("physical")+$oDB->get("mental")+$oDB->get("emotional")+$oDB->get("social")) . "</td>");  
								   
                              
							  
                                echo "<td>" . (is_null($oDB->get("aantalwaarderingen")) ? "" : round(($oDB->get("waardering")+$oDB->get("straf"))/$oDB->get("aantalwaarderingen")*10)/10) . "</td>"; 
								echo ("<td class=\"" . statusClass($oDB->get("schenkingen"), "schenkingen", ">") . "\">" . ($oDB->get("schenkingen")) . "</td>"); 
								
								echo ("<td class=\"" . statusClass(abs($oDB->get("creditsin")-$oDB->get("creditsout")), "credits", ">") . "\">" . (settings("startvalues", "credits")+$oDB->get("creditsin")-$oDB->get("creditsout")) . "</td>");  
                                echo "<td>" . $oDB->get("creditsin") . "</td>"; 
                                echo "<td>" . $oDB->get("creditsout") . "</td>";      
                               // echo "<td>" . ($oDB->get("partnercount")/($oDB->get("countin") + $oDB->get("countout"))) . "</td>";     
                               // echo "<td>" . $oDB->get("transcount") . "</td>";    
								$iDiversiteit = ($oDB->get("countin") + $oDB->get("countout"))>0 ? round($oDB->get("partnercount")/($oDB->get("countin") + $oDB->get("countout"))*100) : 100;  
								 echo ("<td class=\"" . statusClass($iDiversiteit/100, "transactiediversiteit", "<") . "\">$iDiversiteit (" . ($oDB->get("countin") + $oDB->get("countout")) . "/" . $oDB->get("partnercount") . ")</td>");                   
                              //  echo(diffDates($oDB->get("lastupdate")));
                              //  echo(diffDates($oDB->get("clickdate")));//laatst ingeschreven
                              //  echo(diffDates($oDB->get("postdate")));//laatst item geplaatst 
                              //  echo(diffDates($oDB->get("lastlogin")));
								echo ("<td><a href=\"admin.user.php?u=" . $oDB->get("id") . "\">Details</a></td>");  
								echo ("</tr>"); 
							}
							echo ("</table>"); 
							*/
                              
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
