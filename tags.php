<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$strSearch = $_GET["s"]; 
	$arTags = array(); 
	$oDB = new database(); 
	$oDB->execute("select distinct (tag) from tblMarketTags where tag like '" . $oDB->escape($strSearch) . "%' order by tag limit 100;"); 
	while ($oDB->nextRecord()) {
		$arTags[] = $oDB->get("tag"); 	
	}
	/*
	$oDB->execute("select distinct (tag) from tblMarketTags where tag like '%" . $oDB->escape($strSearch) . "%' order by tag limit 50;"); 
	while ($oDB->nextRecord()) {
		$arTags[] = $oDB->get("tag"); 	
	}
	*/
	echo json_encode($arTags); 
?>