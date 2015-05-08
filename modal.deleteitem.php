<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security();  
	
//	$strAlert = isset($_GET["a"]) ? $_GET["a"] : "Fout!"; 
//	$strTitel = isset($_GET["t"]) ? $_GET["t"] : "Fout"; 
	
	$iID = intval($_GET["i"]); 
	$oItem = owaesitem($iID); 
	 
	if (!$oItem->userrights("edit", me())) {  
		redirect("owaes.php?owaes=" . $iID); 
		exit(); 
	}
	
	if (isset($_POST["delete"])) {  
		$oItem->state(STATE_DELETED); 
		$oItem->update();  
		if (isset($_POST["comment"])) {
			if ($_POST["comment"] != "") {  
				$oMessage = new message();  
				$oMessage->receiver($oItem->author()->id()); 
				$oMessage->body("Er werd een item verwijderd: ");
				$oMessage->data("market", $iID);  
				$oMessage->data("info", $_POST["comment"]);  
				$oMessage->data("reporter", me());     
				$oMessage->update(); 
			}
		}
		exit(); 
	} 
	
	$strHTML = $oItem->html("modal.deleteitem.html"); 
	$strHTML = str_replace("[mainpage]", fixPath("index.php?t=" . $oItem->type()->key()), $strHTML); 
	if ($oItem->author()->id() == me()) {
		$strHTML = str_replace("[xtra]", '', $strHTML); 
	} else {
		$strHTML = str_replace("[xtra]", '<div class="col-lg-12">
											<p>Geef info voor ' . $oItem->author()->getName() . '</p>
											<textarea name="comment" class="form-control" placeholder="Extra info"></textarea>
										</div>', $strHTML); 
	}
	  
	echo $strHTML;
	
?>