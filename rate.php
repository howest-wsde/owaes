<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE, (isset($_GET["ajax"]))?AJAX:PAGE);  
	 
	$iStars = intval(isset($_POST["score"]) ? $_POST["score"] : $_GET["score"]);  
	$iMarket = intval(isset($_POST["market"]) ? $_POST["market"] : $_GET["market"]);  
	$iReceiver = intval(isset($_POST["receiver"]) ? $_POST["receiver"] : $_GET["receiver"]);  
	$strComment = isset($_POST["comment"]) ? $_POST["comment"] : (isset($_GET["comment"]) ? $_GET["comment"] : "");  
 
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
		$oOwaes = owaesitem($oRating->market()); 
		header('Location: ' . $oOwaes->getLink()); 
	}
?>