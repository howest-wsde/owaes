<?php
	include "inc.default.php"; // should be included in EVERY file
	$oSecurity = new security(TRUE);
	$oLog = new log("page visit", array("url" => $oPage->filename()));

	$oList = new grouplist();

	$oDB = new database();


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader();
		?>
        <style>
			.bedrijf { display: block; float: left; height: 100px; width: 50%; text-overflow: ellipsis; }
			.bedrijf .h100 {height: 90px; overflow: hidden; }
			.bedrijf h2 {font-size: 110%; font-weight: bold; margin: 0; display: inline-block;  }
			.bedrijf p.website {display: inline; margin-left: 15px; }
			.bedrijf p {margin: 0; }
			div.keuze {height: 65px; font-size: 35px; border: 2px dashed gray; display: inline-block; margin: 1.3%; padding: 10px; width: 17.4%; overflow: hidden; }
			div.keuze.full {font-size: 100%; font-weight: bold; background: white; }
			div.keuze.full img {width: 40px; float: right; }
			div.addknop img {opacity: 0.4; filter: alpha(opacity=40);  }
			.bedrijf.hover.ok div.addknop img {opacity: 1; filter: alpha(opacity=100);  }
			.bedrijf.hover.ok {cursor: pointer; }
			div.info {padding: 0 15px; display: block; overflow: auto; }
			a.opslaan {float: right; }
			a.opslaan.disabled {color: #aaa; }
            .volzet .well {background: rgba(50, 50, 50, 0.10); color: gray; }
            .volzet .well a {color: gray; }
		</style>
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
                    	<h2>Stagemarkt Roeselare 22 september 2016 9 - 16u</h2>

                    </div>
					<?php

						$oDB = new database();
						$arLijstVolzet = array();
						$oDB->execute("select bedrijfsid, status from tblStagemarktVolzet; ");
						while ($oDB->nextRecord()) $arLijstVolzet[$oDB->get("bedrijfsid")] = $oDB->get("status");

                        foreach ($oList->getList() as $oGroep) {
                            if (isset($arLijstVolzet[$oGroep->id()])) {
                                if ($arLijstVolzet[$oGroep->id()] == "volzet 2016") echo "<div id=\"group-" . $oGroep->id() . "\" class=\"bedrijf nok\" rel=\"" . $oGroep->id() . "\">" . $oGroep->HTML("group_stagemarktkeuze.volzet.html") . "</div>";
                            } else echo "<div id=\"group-" . $oGroep->id() . "\" class=\"bedrijf ok\" rel=\"" . $oGroep->id() . "\">" . $oGroep->HTML("group_stagemarktkeuze.vrij.html") . "</div>";
                        }
                    ?>
                </div>
        	<?php echo $oPage->endTabs(); ?>
        </div>


        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
