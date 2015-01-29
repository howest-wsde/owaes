<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	if (!$oSecurity->admin())  stop("admin"); 
  
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
		<style>
            ol.table {display: table; xidth: 100%; }
            ol.table li {display: table-row; }
            ol.table li div {display: table-cell; }
            ol.table li.titles div {font-weight: bold; }
        </style>
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
                    <div class="meldingen admin"> 
                    	<? include "admin.menu.xml"; ?>
                    	<ol class="table">
                        	<li class="titles">
                            	<div>Datum</div>
                            	<div>Reporter</div>
                            	<div>User</div>
                            	<div>Owaes</div>
                            	<div>Reason</div>
                            	<div>Comment</div>
                            </li>
							<?
                                $oReports = new reports();  
								$arCounters = array(
										"reporter" => array(), 
										"user" => array(), 
										"market" => array(), 
									); 
								foreach ($oReports->getList() as $oReport) {
                                    if (!isset($arCounters["reporter"][$oReport->reporter()])) $arCounters["reporter"][$oReport->reporter()] = 0; 
									$arCounters["reporter"][$oReport->reporter()]++; 
                                    if (!isset($arCounters["user"][$oReport->user()])) $arCounters["user"][$oReport->user()] = 0; 
									$arCounters["user"][$oReport->user()]++; 
                                    if (!isset($arCounters["market"][$oReport->market()])) $arCounters["market"][$oReport->market()] = 0; 
									$arCounters["market"][$oReport->market()]++; 
                                }
                                foreach ($oReports->getList() as $oReport) {
                                    echo "<li>"; 
                                        echo "<div>" . str_date($oReport->timestamp(), "d/m/y") . "</div>";
                                        echo "<div>" . user($oReport->reporter())->getLink() . " (" . $arCounters["reporter"][$oReport->reporter()] . ")</div>";
                                        echo "<div>" . user($oReport->user())->getLink() . " (" . $arCounters["user"][$oReport->user()] . ")</div>";
                                        echo "<div>" . owaesitem($oReport->market())->link() . " (" . $arCounters["market"][$oReport->market()] . ")</div>";
                                        echo "<div>" . $oReport->reason() . "</div>";
                                        echo "<div>" . $oReport->data("comment") . "</div>";
                                    echo "</li>"; 	
                                }
                            ?>
                        </ol>
                    </div>
                </div> 
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
