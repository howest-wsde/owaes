<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	if (!$oSecurity->admin()) $oSecurity->doLogout(); 
 
    //Variables for pages
    if(isset($_GET["start"])){
       $start = $_GET["start"];
    }else{
       $start = 0;
    }
    $limit = 10;//aantal records per pagina
    $linkNext = $start +$limit; // wordt op het laatst gebruikt om te zien of er een 'NEXT' link moet komen
    $back = $start - $limit; 
    $next = $start + $limit;
    
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
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="index">
         <? echo $oPage->startTabs(); ?> 
         
    	<div class="body">
 	          
                <div class="container ">
                  <div class="row">
					<? 
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
                    <div class="main market admin"> 
                    	<ul>
                        	<li><a href="admin.groepen.php">Groepen</a></li><li><a href="admin.users.php">Gebruikers</a></li>
                        </ul>
                        
                      <!--   <h1>Users: </h1> -->
                        <?
                        	$oDB = new database(); 
                            
                            
                            //aantal users "/*<th>alias</th>*/."
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
									"creditssum" => "credits", 
									"lastupdate" => "lastupdate", 
									"clickdate" => "Laatst ingeschreven", 
									"postdate" => "Laatst item geplaatst", 
									"lastlogin" => "Last Login", 
									
								);  
							$strOrder = (isset($_GET["order"])?$_GET["order"]:""); 
							if (!in_array($strOrder, array_keys($arTable))) $strOrder = "lastname"; 
                            
							$strSQL = "select u.id, u.firstname, u.lastname, 
								COALESCE(i.emotional,0) as emotional, 
								COALESCE(i.social,0) as social,  
								COALESCE(i.physical,0) as physical,  
								COALESCE(i.mental,0) as mental, 
								COALESCE(pIn.creditsin, 0) as creditsin, 
								COALESCE(pUit.creditsout, 0) as creditsout,  
								(COALESCE(pIn.creditsin, 0)-COALESCE(pUit.creditsout, 0)) as creditssum,  
								max(ms2.clickdate) as clickdate, 
								max(m.lastupdate) as lastupdate, 
								max(m.date) as postdate, 
								l.datum as lastlogin 
							from tblUsers u 
								left join (select user, sum(emotional)  as emotional, sum(social) as social, sum(physical) as physical, sum(mental) as mental from tblIndicators where actief = 1 group by user ) as i on u.id = i.user
								left join tblPayments pIn on u.id = pIn.receiver and pIn.actief = 1   
								left join (select receiver as user, sum(credits) as creditsin from tblPayments where actief = 1) as pIn on u.id = pIn.user
								left join (select sender as user, sum(credits) as creditsout from tblPayments where actief = 1) as pUit on u.id = pUit.user   
								left join tblMarketSubscriptions ms2 on u.id = ms2.doneby 
								left join tblMarket m on u.id = m.author  
								left join (select max(datum) as datum, user from tblLog group by user) as l on u.id = l.user
							group by u.id
							order by $strOrder
							";   
							$oDB->execute($strSQL);
							echo ("<table class=\"database\">");  
							echo ("<tr>");
							
							foreach ($arTable as $strField=>$strTitle) {
								if($strOrder==$strField){
									echo("<th class='adminFilter' ><a href='admin.php?order=$strField'>$strTitle<span class='caret'></span></a></th>");
								}else{
									echo("<th><a href='admin.php?order=$strField'>$strTitle<span class='caret'></span></a></th>");
								}	
							}
							 
							echo("<th></th></tr>"); 
							while ($oDB->nextRecord()) {
								echo ("<tr>"); 
                                echo ("<td>" . $oDB->get("id") . "</td>");
								//echo ("<td>" . $oDB->get("alias") . "</td>"); 
								echo ("<td>" . $oDB->get("firstname") . "</td>"); 
								echo ("<td>" . $oDB->get("lastname") . "</td>"); 
                                
                                echo (checkIndicator(settings("startvalues", "physical") + $oDB->get("physical")));
                                echo (checkIndicator(settings("startvalues", "mental") + $oDB->get("mental")));
                                echo (checkIndicator(settings("startvalues", "emotional") + $oDB->get("emotional")));
                                echo (checkIndicator(settings("startvalues", "social") + $oDB->get("social")));
                                echo(checkCredits(settings("startvalues", "credits") + $oDB->get("creditsin") - $oDB->get("creditsout")));                    
                                echo(diffDates($oDB->get("lastupdate")));
                                echo(diffDates($oDB->get("clickdate")));//laatst ingeschreven
                                echo(diffDates($oDB->get("postdate")));//laatst item geplaatst 
                                echo(diffDates($oDB->get("lastlogin")));
								echo ("<td><a href=\"admin.user.php?u=" . $oDB->get("id") . "\">Details</a></td>");  
								echo ("</tr>"); 
							}
							echo ("</table>"); 
                             
                            
                            function diffDates($lastupdate){
                                global $limitDaysRood, $limitDaysOranje;
                                $now = time();
                                $diff = $now-$lastupdate;
                                $maxtimeOranje = $limitDaysOranje * 24 *60 *60;
                                $maxtimeRood = $limitDaysRood * 24 *60 *60;
                                
                                if($diff > $maxtimeRood){
                                    return ("<td class='lowValue'>" .str_date($lastupdate) . "</td>"); 
                                }else if($diff > $maxtimeOranje && $diff<$maxtimeRood){
                                    return ("<td class='orangeValue'>" . str_date($lastupdate) . "</td>"); 
                                }else{
                                    return ("<td>".str_date($lastupdate)."</td>");
                                }
                            }
                            
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
						?>
							 
                    </div>
                </div> 
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
