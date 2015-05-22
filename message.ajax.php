<?php
	include "inc.default.php"; // should be included in EVERY file 
	
	// message.ajax.php wordt elke 30 secondes opgeroepen (via function ping in owaes.js)
	
	$oSecurity = new security(TRUE);   
	if (++$_SESSION['views'] % 10 == 0) { 
		$oExperience = new experience(me());  
		$oExperience->detail("reason", "ping");  
		$oExperience->sleutel(str_date(owaestime(), "j M y") , TRUE); 
		$oExperience->add(0.5);  
	}   
	
	
	if (isset($_GET["read"])) { 
		$oNotification = new notification(); 
		$oNotification->read($_GET["read"]);  	
	}
	
	$oNotification = new notification(me()); 
	$arMessages = $oNotification->getList(); 
	 
	echo json_encode($arMessages); 
	/*
 <script>
            notify(
                "Notificatie Titel", 
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.", 
                "http://quq.be/owaes/profileimg.php?id=30&w=80&h=80&v=272",
                "http://quq.be/owaes/profile.php"
            );
        </script> 
		*/
?>