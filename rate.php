<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE, (isset($_GET["ajax"]))?AJAX:PAGE);  
	 
	$iStars = intval($_GET["score"]);  
	$iMarket = intval($_GET["market"]);  
	$iReceiver = intval($_GET["receiver"]);  
	$strComment = $_GET["comment"];  
 
 	$oRating = new rating(array(
						"market" => $iMarket, 
						"sender" => me(), 
						"receiver" => $iReceiver,
					)); 
	$oRating->stars($iStars); 
	$oRating->comment($strComment); 
	$oRating->rated(TRUE);  
	
	if (isset($_GET["ajax"])) {
		echo $oRating->html(); 
	} else {
		$oOwaes = new owaesitem($oRating->market()); 
		header('Location: ' . $oOwaes->getLink()); 
	}
?>