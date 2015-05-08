<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  

	$iGroep = intval($_GET["g"]); 
	$iUser = intval($_GET["u"]); 
	$strWut = $_GET["w"]; 
	$iVal = intval($_GET["v"]); 
	
	$oGroep = group($iGroep); 
	
	$oMijnRechten = $oGroep->userrights(me());  
	if ($oMijnRechten->userrights()) {  
		$oRechten = $oGroep->userrights($iUser);
		$oRechten->right($strWut, $iVal); 
		$oRechten->update(); 
		if ($oRechten->right($strWut)) {
			echo "<a class=\"checkbox on userrights\" href=\"" . fixpath("admin.groepusers.change.php?g=" . $iGroep . "&u=" . $iUser . "&w=" . $strWut . "&v=0") . "\">1</a>"; 
		} else {
			echo "<a class=\"checkbox off userrights\" href=\"" . fixpath("admin.groepusers.change.php?g=" . $iGroep . "&u=" . $iUser . "&w=" . $strWut . "&v=1") . "\">0</a>"; 
		}  
	}  
?>