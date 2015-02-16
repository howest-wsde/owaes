<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$arFixed = array( 
		"credits" => settings("credits", "name", "x"),  
	); 
?>var arVocabulaire = JSON.parse('<? echo json_encode($arFixed); ?>');  

function vocabulaire(strTerm) {
	return arVocabulaire[strTerm]; 
}

