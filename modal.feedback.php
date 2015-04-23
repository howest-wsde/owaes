<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE);  
	
	$iMarket = intval($_GET["m"]); 
	$oMarket = owaesitem($iMarket); 
	$strHTML = $oMarket->html("modal.feedback.html");
	
	$iUser = intval($_GET["u"]); 
	$oUser = user($iUser); 
	$strHTML = $oUser->html($strHTML); 
	 
	if (isset($_POST["cancel"])) { 
		$oActions = new actions(me()); 
		$oAction = $oActions->search(array(
				"type" => "feedback", 
				"user" => $iUser, 
				"market" => $iMarket, 
			));  
		if ($oAction) {
			$oAction->tododate(owaestime() + (2*24*60*60)); // 2 dagen
			$oAction->update();  
		} 
		exit(); 
	} else if (isset($_POST["market"])) {  
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
		 
		$oExperience = new experience(me());  
		$oExperience->detail("reason", "sterren gegeven");  
		$oExperience->sleutel("feedback.out." . $iMarket);   
		$oExperience->add(150);  
		 
		$oExperience = new experience($iReceiver);  
		$oExperience->detail("reason", "sterren gekregen");  
		$oExperience->sleutel("feedback.in." . $iMarket);   
		$oExperience->add($iStars*10);  
		 
		$oActions = new actions(me()); 
		$oAction = $oActions->search(array(
				"type" => "feedback", 
				"user" => $iUser, 
				"market" => $iMarket, 
			)); 
		if ($oAction) {
			$oAction->done(owaestime()); 
			$oAction->update();  
		}
		exit();  
	} 
	
	echo $strHTML; 
	
?>