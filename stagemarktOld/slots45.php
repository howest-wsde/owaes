<?php
	include "inc.default.php"; // should be included in EVERY file  
	$oSecurity = new security(TRUE); 
	$oLog = new log("page visit", array("url" => $oPage->filename())); 
	
	$iFixed = 5; // er worden direct x slots vastgelegd per student
	 
	$oDB = new database(); 
	$oDBstudent = new database(); 
	
	$oDB->execute("select * from tblStagemarktStudInschrijvingen order by student;"); 
	while ( $oDB->nextRecord() ) {
		$iStudent = $oDB->get("student"); 
		$arSubscribed = array(nr($oDB->get("k1")), nr($oDB->get("k2")), nr($oDB->get("k3")), nr($oDB->get("k4")), nr($oDB->get("k5"))); 
		$arStudentSlots = array(1,2,3,4,5,6,7,8);
		$oDBstudent->execute("select bedrijf, slot from tblStagemarktDates where student = " . $iStudent . ";"); 
		while ($oDBstudent->nextRecord()) {
			if(($iKey = array_search($oDBstudent->get("bedrijf"), $arSubscribed)) !== false) array_splice ($arSubscribed, $iKey, 1);  
			if(($iKey = array_search($oDBstudent->get("slot"), $arStudentSlots)) !== false)  array_splice ($arStudentSlots, $iKey, 1);  
		}
	 	foreach ($arSubscribed as $iBedrijf) { // de bedrijven waar gebruiker inschreef, maar nog geen slot doorkreeg 
			$arBedrijfsslots = $arStudentSlots; 
			$oDBstudent->execute("select slot from tblStagemarktDates where bedrijf = $iBedrijf; "); 
			while ($oDBstudent->nextRecord()) { 
				if(($iKey = array_search($oDBstudent->get("slot"), $arBedrijfsslots)) !== false)  array_splice ($arBedrijfsslots, $iKey, 1);  
			}
			if (count($arBedrijfsslots) > 0) {
				$iSlot = $arBedrijfsslots[0]; 
				$oDBstudent->execute("insert into tblStagemarktDates (student, bedrijf, slot) values ($iStudent, $iBedrijf, $iSlot); "); 
				if(($iKey = array_search($iSlot, $arStudentSlots)) !== false)  array_splice ($arStudentSlots, $iKey, 1); 
				echo $oDBstudent->sql() . "<br>"; 
			}
			if (count($arBedrijfsslots) == 0) $oDBstudent->execute("insert into tblStagemarktVolzet (bedrijfsid) values ($iBedrijf); "); 
		} 
	}
	
	
	function nr($i){
		return intval($i); 	
	}
	
	
	exit(); 
	 
?>