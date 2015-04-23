<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	if (!$oSecurity->admin()) stop("admin"); 
	
	$oPage->addJS("script/admin.js"); 
	$oPage->addJS("script/flot/jquery.flot.js"); 
	$oPage->addJS("script/flot/jquery.flot.time.js"); 
    $oPage->addJS("script/flot/jquery.flot.symbol.js");
	$oPage->addCSS("style/admin.css"); 
	
	$iUser = intval($_GET["u"]); 
	$oUser = user($iUser);  
	
	if (isset($_POST["changeindicatoren"])) {
		$oDB = new database(); 	
		$oDB->execute("insert into tblIndicators (user, datum, physical, mental, emotional, social, reason) values ('" . $iUser . "', '" . owaestime() . "', '" . (intval($_POST["physical"]) - $oUser->physical(NULL, FALSE)) . "', '" . (intval($_POST["mental"]) - $oUser->mental(NULL, FALSE)) . "', '" . (intval($_POST["emotional"]) - $oUser->emotional(NULL, FALSE)) . "', '" . (intval($_POST["social"]) - $oUser->social(NULL, FALSE)) . "', '" . 0 . "'); ");
		redirect(filename());  // refresh om values up te daten
	}
	
	if (isset($_POST["changepass"])) {
		$strNewPass = $_POST["paswoord"]; 
		if (trim($_POST["paswoord"]) != "") {
			$oUser->password($strNewPass); 
			$oUser->update();  
		}
		redirect(filename());  // refresh om form post uit url te halen
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
         <div class="container">
        	
            	  <div class="row">
					<? 
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div> 
                    <div class="main market admin-user"> 
                     	<? include "admin.menu.xml"; ?>
                        <?   
							$arChart = array(
								"Fysiek" => array(
									"current" => settings("startvalues", "physical"), 
									"data" => array(), 
								), 
								"Kennis" => array(
									"current" => settings("startvalues", "mental"), 
									"data" => array(), 
								), 
								"Welzijn" => array(
									"current" => settings("startvalues", "emotional"), 
									"data" => array(), 
								), 
								"Sociaal" => array(
									"current" => settings("startvalues", "social"), 
									"data" => array(), 
								), 
								"Credits" => array(
									"current" => settings("startvalues", "credits"), 
									"data" => array(), 
								),  
							);

							$arEvents = array(); 
							function chartEvent($iTime, $iValue, $strInfo) {
								global $arEvents;  
								$strKey = $iTime . ":" . $iValue; 
								if (!isset($arEvents[$strKey])) $arEvents[$strKey] = array(); 	 
								$arEvents[$strKey][] = $strInfo;  
							}
							
							$oDB = new database("select * from tblIndicators where user = $iUser order by datum; ", TRUE);  
							while ($oDB->nextRecord()) {
								$arChart["Fysiek"]["data"][] = array(javatime($oDB->get("datum")), $arChart["Fysiek"]["current"] += $oDB->get("physical")); 
								$arChart["Kennis"]["data"][] = array(javatime($oDB->get("datum")), $arChart["Kennis"]["current"] += $oDB->get("mental")); 
								$arChart["Welzijn"]["data"][] = array(javatime($oDB->get("datum")), $arChart["Welzijn"]["current"] += $oDB->get("emotional")); 
								$arChart["Sociaal"]["data"][] = array(javatime($oDB->get("datum")), $arChart["Sociaal"]["current"] += $oDB->get("social")); 
								
							}
                            
                            $oDB = new database("select u.id, u.firstname, u.lastname,  (SELECT l.datum  from tblLog l WHERE user=$iUser ORDER BY `datum` desc LIMIT 1) as lastlogin from tblUsers u where u.id =$iUser");
							$oDB->execute();
				           // $lastLogin = $oDB->get("lastlogin");
                            //echo(vardump($arrGebruiker));
                            
							$oDB = new database("select * from tblPayments where (sender = $iUser or receiver = $iUser) and actief = 1 order by datum; ", TRUE);  
							while ($oDB->nextRecord()) {
								$iVal = $arChart["Credits"]["current"] += (($oDB->get("sender") == $iUser)?-1:1) * $oDB->get("credits"); 
								$arChart["Credits"]["data"][] = array(javatime($oDB->get("datum")), $iVal); 
								chartEvent(javatime($oDB->get("datum")), $iVal, (
										($oDB->get("sender") == $iUser) ? 
											($oDB->get("credits") . " " . settings("credits", "name", "x") . " naar " . user($oDB->get("receiver"))->getName()): 
											($oDB->get("credits") . " " . settings("credits", "name", "x") . " van " . user($oDB->get("sender"))->getName())
									) ); 
							}
							
							foreach ($arChart as $strKey=>$arData) {
								$arChart[$strKey]["data"][] = array(javatime(owaestime()), $arData["current"]); 	
							}
						?>
                       <script id="source" language="javascript" type="text/javascript">
$(function () {
  

	
	
	var arEvents = <? echo json_encode($arEvents); ?>; 

    
    var datasets ={
        "Welzijn" : {
            label:"Welzijn",
            data: <? echo json_encode($arChart["Welzijn"]["data"]);?>
        },
         "Kennis" : {
            label:"Kennis",
            data: <? echo json_encode($arChart["Kennis"]["data"]);?>
        },
        "Fysiek" : {
            label:"Fysiek",
            data: <? echo json_encode($arChart["Fysiek"]["data"]);?>
        },
        "Sociaal" : {
            label:"Sociaal",
            data: <? echo json_encode($arChart["Sociaal"]["data"]);?>
        },
         "Credits" : {
            label:"<? echo ucfirst(settings("credits", "name", "x")); ?>",
            data: <? echo json_encode($arChart["Credits"]["data"]);?>,
            yaxis:2
      },
    };
    
    var i = 0;
        $.each(datasets, function(key, val) {
        val.color = i;
        ++i;
    }); 

     var choiceContainer = $("#choices");
      $.each(datasets, function(key, val) {
                                choiceContainer.append('<div class="filterOption"><input type="checkbox" name="' + key +
                                '" checked="checked" id="id' + key + '">' +
                                '<label class="filter'+key+'" for="id' + key + '">'
                                + val.label + '</label></div>');
                                });
                                choiceContainer.find("input").click(plotAccordingToChoices); 
                                


function plotAccordingToChoices() {
        var data = [];
        
        choiceContainer.find("input:checked").each(function () {
            var key = $(this).attr("name");
            
        if (key && datasets[key])
                data.push(datasets[key]);
        });
        
        if (data.length > 0)
            $.plot($("#chart"), data, {
                 xaxis: { mode: 'time' },
                 x2axis: { mode: 'time' },
                 yaxis: {  
			 			    min: 0, 
			 			    tickFormatter: function (v, axis) { return v.toFixed(axis.tickDecimals) +" %" }
			 		    },
                 y2axis: { 
			 			    tickFormatter: function (v, axis) { return v.toFixed(axis.tickDecimals) +" <? echo settings("credits", "name", "x"); ?>" }
			 		    },
                 legend: { position: 'sw' }, 
			     series: {
				    lines: {
					    show: true
				    }, points: { show: true },
			    }, 
			    grid: {
				    hoverable: true,
				    clickable: true, 
				    backgroundColor: "#fff" , 
			    },
	}); 
    
    
    $("#chart").bind("plothover", function (event, pos, item) { 
			if (item) {
				var x = item.datapoint[0].toFixed(2),
					y = item.datapoint[1].toFixed(2);
				strTekst = item.series.label + ": " + Math.round(y); 
				strKey = Math.round(x) + ":" + Math.round(y); 
				if (arEvents[strKey]) {
					arT = arEvents[strKey]; 
					strTekst += "<br>" + arT.join("<br>");  
				} 
				$("#tooltip").html(strTekst)
					.css({top: item.pageY+5, left: item.pageX+5})
					.fadeIn(200);
			} else {
				$("#tooltip").hide();
			} 
		});
		$("<div id='tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #fdd",
			padding: "2px",
			"background-color": "#fee",
			opacity: 0.80
		}).appendTo("body");
		
    }

   plotAccordingToChoices();

		
});


</script>
                        
                            <div id="chart" ></div>
                            <div id="choices"></div>
                            <!-- style="width: 900px; height: 500px;" -->
                            
                            
                            <div class="well" style="clear: both; ">
                                <form method="post">
                                    <fieldset>
                                        <h4>Indicatoren aanpassen: </h4>
                                        <div class="form-group">
                                            <label class="control-label col-lg-1" for="mental">mental:</label>
                                            <div class="col-lg-2">
                                                <input type="text" pattern="^0*(?:[0-9][0-9]?|100)$" value="<? echo $oUser->mental();  ?>" placeholder="mental" id="mental" class="form-control" name="mental" />
                                            </div>
                                            
                                            <label class="control-label col-lg-1" for="mental">physical:</label>
                                            <div class="col-lg-2">
                                                <input type="text" pattern="^0*(?:[0-9][0-9]?|100)$" value="<? echo $oUser->physical();  ?>" placeholder="physical" id="physical" class="form-control" name="physical" />
                                            </div>
                                            
                                            <label class="control-label col-lg-1" for="mental">emotional:</label>
                                            <div class="col-lg-2">
                                                <input type="text" pattern="^0*(?:[0-9][0-9]?|100)$" value="<? echo $oUser->emotional();  ?>" placeholder="emotional" id="emotional" class="form-control" name="emotional" />
                                            </div>
                                            
                                            <label class="control-label col-lg-1" for="mental">social:</label>
                                            <div class="col-lg-2">
                                                <input type="text" pattern="^0*(?:[0-9][0-9]?|100)$" value="<? echo $oUser->social();  ?>" placeholder="social" id="social" class="form-control" name="social" />
                                            </div>
                                         </div>
                                         <div class="form-group"> 
                                            <input type="submit" value="Opslaan" name="changeindicatoren" class="btn btn-default btn-save" />
                                        </div>
                                     </fieldset>
                                </form>
                                
                            </div>
                            

                            <div class="well" style="clear: both; ">
                                <form method="post">
                                    <fieldset>
                                        <h4>Paswoord aanpassen: </h4>
                                        <div class="form-group">
                                            <label class="control-label col-lg-1" for="mental">nieuw paswoord:</label>
                                            <div class="col-lg-2">
                                                <input type="text" value="" placeholder="paswoord" id="paswoord" class="form-control" name="paswoord" />
                                            </div>
                                             
                                         </div>
                                         <div class="form-group"> 
                                            <input type="submit" value="Opslaan" name="changepass" class="btn btn-default btn-save" />
                                        </div>
                                     </fieldset>
                                </form>
                                
                            </div>
                            
                            <div class="aanradenContainer" style="display: none; ">
                                <div class="aanraden"><span data-toggle="modal" data-target="#popupOpleiding" class="icon icon-opleiding aanradenPopup">   Raad een Opleiding aan</span></div>
                                <div class="aanraden"><span data-toggle="modal" data-target="#popupWerkervaring" class="icon icon-werkervaring aanradenPopup">   Raad een Werkervaring aan</span></div>
                                <div class="aanraden"><span data-toggle="modal" data-target="#popupQuest" class="icon icon-trophy aanradenPopup">   Raad een Quest aan</span></div>
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

<!--popupOpleiding-->
<div class="modal fade" id="popupOpleiding" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Aanraden van een opleiding</h4>
                </div>
                <div class="modal-body">
                     <div class="list-group">
                    <div class="list-group-item">
                     <input type="checkbox"/>     
                    <div class="media">
                            <img class="media-object pull-left" src="img/placeholder.png">
                            <div class="media-body">
                                <h4 class="media-heading">Vestibulum fermentum tortor in dui mattis</h4>

                                <div class="development odd">
                                    <ul>
                                        <li class="physical first"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="mental last"><img src="img/mental.png" title="Kennis" alt="Kennis: 25%"></li>
                                    </ul>
                                </div>

                                <span class="icon icon-opleiding icon-lg"></span>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                    <input type="checkbox"/>
                        <div class="media">
                            <img class="media-object pull-left" src="img/placeholder.png">
                            <div class="media-body">
                                <h4 class="media-heading">Morbi tempor lorem rhoncus</h4>

                                <div class="development odd">
                                    <ul>
                                        <li class="physical first"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="mental last"><img src="img/mental.png" title="Kennis" alt="Kennis: 25%"></li>
                                    </ul>
                                </div>

                                <span class="icon icon-opleiding icon-lg"></span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white btn-cancel" data-dismiss="modal">Sluiten</button>
                    <button type="button" class="btn btn-default btn-save">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--popupWerkervaring-->
<div class="modal fade" id="popupWerkervaring" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Aanraden van een werkervaring</h4>
                </div>
                <div class="modal-body">
                     <div class="list-group">
                    <div class="list-group-item">
                    <input type="checkbox"/>
                        <div class="media">
                            <img class="media-object pull-left" src="img/placeholder.png">
                            <div class="media-body">
                                <h4 class="media-heading">Vestibulum fermentum tortor in dui mattis</h4>

                                <div class="development odd">
                                    <ul>
                                        <li class="physical first"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="mental last"><img src="img/mental.png" title="Kennis" alt="Kennis: 25%"></li>
                                    </ul>
                                </div>

                                <span class="icon icon-werkervaring icon-lg"></span>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                    <input type="checkbox"/>
                        <div class="media">
                            <img class="media-object pull-left" src="img/placeholder.png">
                            <div class="media-body">
                                <h4 class="media-heading">Morbi tempor lorem rhoncus</h4>

                                <div class="development odd">
                                    <ul>
                                        <li class="physical first"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="physical"><img src="img/physical.png" title="Fysiek" alt="Fysiek: 75%"></li>
                                        <li class="mental last"><img src="img/mental.png" title="Kennis" alt="Kennis: 25%"></li>
                                    </ul>
                                </div>

                                <span class="icon icon-werkervaring icon-lg"></span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white btn-cancel" data-dismiss="modal">Sluiten</button>
                    <button type="button" class="btn btn-default btn-save">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--popupQuest-->
<div class="modal fade" id="popupQuest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Aanraden van een werkervaring</h4>
                </div>
                <div class="modal-body">
                <div class="list-group">
                    <div class="list-group-item">
                    <input type="checkbox"/>
                        <div class="media">
                            <img class="media-object pull-left" src="img/day.png">
                            <div class="media-body">
                                <h4 class="media-heading">Dag: Lorem ipsum morbi tempor</h4>
                                <p>Vestibulum tempor aliquet nibh, vitae pulvinar orci. Proin at accumsan lectus. 
                                   Nunc eu hendrerit neque. Mauris id elit in sapien mollis rhoncus. Donec quis tempus leo, rutrum eleifend odio. 
                                   Etiam eget luctus lorem, non vestibulum urna. Fusce lorem risus, eleifend ac varius quis.
                                </p>
                                <h5>Beloning</h5>
                                <p>100 exp.</p>
                                <span class="icon icon-trophy icon-lg"></span>
                            </div>
                        </div>
                    </div>
                     <div class="list-group">
                    <div class="list-group-item">
                    <input type="checkbox"/>
                        <div class="media">
                            <img class="media-object pull-left" src="img/week.png">
                            <div class="media-body">
                                <h4 class="media-heading">Week: Lorem ipsum morbi tempor</h4>
                                <p>Vestibulum tempor aliquet nibh, vitae pulvinar orci. Proin at accumsan lectus. 
                                   Nunc eu hendrerit neque. Mauris id elit in sapien mollis rhoncus. Donec quis tempus leo, rutrum eleifend odio. 
                                   Etiam eget luctus lorem, non vestibulum urna. Fusce lorem risus, eleifend ac varius quis.
                                </p>
                                <h5>Beloning</h5>
                                <p>700 exp.</p>
                                <span class="icon icon-trophy icon-lg"></span>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                    <input type="checkbox"/>
                      <div class="media">
                            <img class="media-object pull-left" src="img/month.png">
                            <div class="media-body">
                                <h4 class="media-heading">Maand: Lorem ipsum morbi tempor</h4>
                                <p>Mattis at dui. Integer sed nunc in lacus lacinia auctor.Proin at accumsan lectus. 
                                   Nunc eu hendrerit neque. Mauris id elit in sapien mollis rhoncus. Donec quis tempus leo, rutrum eleifend odio. 
                                   Etiam eget luctus lorem, non vestibulum urna. Aenean consectetur, lacus lacinia.
                                </p>
                                <h5>Beloning</h5>
                                <p>2800 exp.</p>
                                <span class="icon icon-trophy icon-lg"></span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white btn-cancel" data-dismiss="modal">Sluiten</button>
                    <button type="button" class="btn btn-default btn-save">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>