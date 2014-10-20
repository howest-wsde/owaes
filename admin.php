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
    
    
    if(isset($_GET["order"])){
        $order = $_GET["order"];
    }else{
        $order ="u.id";
    }
 
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
                            
                            $oDB->sql("select distinct u.id, u.alias, u.firstname, u.lastname, u.physical, u.mental, u.emotional, u.social, u.lastupdate, ms.clickdate, m.date as postdate, ((4800 + (select COALESCE(SUM(p.credits),0) from tblPayments p where receiver = u.id and actief =1)) - (select COALESCE(SUM(p.credits),0) from tblPayments p where sender = u.id and actief =1)) as credits,(SELECT l.datum  from tblLog l WHERE user=u.id ORDER BY `datum` desc LIMIT 1) as lastlogin from tblUsers u 
										left join tblMarketSubscriptions ms on ms.doneby = u.id and ms.clickdate =
													(select max(clickdate) from tblMarketSubscriptions ms2  where ms2.doneby = u.id)
										left join tblMarket m on m.author = u.id and m.date =
													(select max(date) from tblMarket m2 where m2.author = u.id)
								order by $order asc, m.date limit $start,$limit; "); 
							$oDB->execute();
							echo ("<table class=\"database\">");  
							echo ("<tr>");
                                
                            if($order=='u.id'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.id"."'>Id<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.id"."'>Id<span class='caret'></span></a></th>");}
                            if($order=='u.firstname'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.firstname"."'>firstname<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.firstname"."'>firstname<span class='caret'></span></a></th>");}    
                            if($order=='u.lastname'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.lastname"."'>lastname<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.lastname"."'>lastname<span class='caret'></span></a></th>");}    
                            if($order=='u.physical'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.physical"."'>physical<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.physical"."'>physical<span class='caret'></span></a></th>");}    
                            if($order=='u.mental'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.mental"."'>mental<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.mental"."'>mental<span class='caret'></span></a></th>");}    
							if($order=='u.emotional'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.emotional"."'>emotional<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.emotional"."'>emotional<span class='caret'></span></a></th>");}    
                            if($order=='u.social'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.social"."'>social<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.social"."'>social<span class='caret'></span></a></th>");}    
                            if($order=='credits'){echo("<th class='adminFilter' ><a href='admin.php?order="."credits"."'>credits<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."credits"."'>credits<span class='caret'></span></a></th>");}    
                            if($order=='u.lastupdate'){echo("<th class='adminFilter' ><a href='admin.php?order="."u.lastupdate"."'>lastupdate<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."u.lastupdate"."'>lastupdate<span class='caret'></span></a></th>");}    
                            if($order=='ms.clickdate'){echo("<th class='adminFilter' ><a href='admin.php?order="."ms.clickdate"."'>Laatst ingeschreven<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."ms.clickdate"."'>Laatst ingeschreven<span class='caret'></span></a></th>");}    
                            if($order=='m.date'){echo("<th class='adminFilter' ><a href='admin.php?order="."m.date"."'>Laatst item geplaatst<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."m.date"."'>Laatst item geplaatst<span class='caret'></span></a></th>");}
                            if($order=='lastlogin'){echo("<th class='adminFilter' ><a href='admin.php?order="."lastlogin"."'>Last Login<span class='caret'></span></a></th>");}else{echo("<th><a href='admin.php?order="."lastlogin"."'>Last Login<span class='caret'></span></a></th>");}
                            //echo("<th><a href='admin.php?order="."lastLogin"."'>Last Login<span class='caret'></span></a></th>");
                            //echo("<th><a href='admin.php?order="."u.physical"."'>Physical<span class='caret'></span></a></th>
                            //    <th><a href='admin.php?order="."u.mental"."'>Mental<span class='caret'></span></a></th>
                            //    <th><a href='admin.php?order="."u.emotional"."'>Emotional<span class='caret'></span></a></th>
                            //    <th><a href='admin.php?order="."u.social"."'>social<span class='caret'></span></a></th>
                            //    <th><a href='admin.php?order="."credits"."'>Credits<span class='caret'></span></a></th>
                            //    <th><a href='admin.php?order="."u.lastupdate"."'>lastupdate<span class='caret'></span></a></th>
                            //    <th><a href='admin.php?order="."ms.clickdate"."'>Clickdate<span class='caret'></span></a></th>
                            //    <th><a href='admin.php?order="."m.date"."'>Postdate<span class='caret'></span></a></th>
                            //    <th></th>");
							echo("<th></th></tr>"); 
							while ($oDB->nextRecord()) {
								echo ("<tr>"); 
                                echo ("<td>" . $oDB->get("id") . "</td>");
								//echo ("<td>" . $oDB->get("alias") . "</td>"); 
								echo ("<td>" . $oDB->get("firstname") . "</td>"); 
								echo ("<td>" . $oDB->get("lastname") . "</td>"); 
                                
                                echo (checkIndicator($oDB->get("physical")));
                                echo (checkIndicator($oDB->get("mental")));
                                echo (checkIndicator($oDB->get("emotional")));
                                echo (checkIndicator($oDB->get("social")));
                                echo(checkCredits($oDB->get("credits")));                    
                                echo(diffDates($oDB->get("lastupdate")));
                                echo(diffDates($oDB->get("clickdate")));//laatst ingeschreven
                                echo(diffDates($oDB->get("postdate")));//laatst item geplaatst
                                //$interval = $datetime1->diff($datetime2);
                                //echo ("<td>" . str_date($oDB->get("lastupdate")) . "</td>"); 
								//echo ("<td>" . str_date($oDB->get("clickdate")) . "</td>"); 
								//echo ("<td>" . str_date($oDB->get("postdate")) . "</td>"); 
                                echo(diffDates($oDB->get("lastlogin")));
								echo ("<td><a href=\"admin.user.php?u=" . $oDB->get("id") . "\">Details</a></td>");  
								echo ("</tr>"); 
							}
							echo ("</table>"); 
                            
                            echo("<div class='pages'>");
                            if($count > $limit){
                                if($back >=0){
                                    echo("<a class='prev' href='admin.php?start=$back&order=$order'>PREV</a>");
                                }
                                
                                $i = 0;
                                $l = 1;
                                for($i=0;$i <$count;$i=$i+$limit){
                                    if($i <> $start){
                                        echo("<a href='admin.php?start=$i&order=$order'>$l</a>");
                                    }else{
                                        echo("$l");
                                    }
                                    $l =$l+1;
                                }
                                
                                if($linkNext <$count){
                                    echo("<a class='next' href='admin.php?start=$next&order=$order'>NEXT</a>");
                                }
                            }
                            echo("</div>");
                            
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
