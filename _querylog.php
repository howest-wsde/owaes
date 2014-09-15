<style>
	table {border-collapse: collapse; }
	td, th {border: 1px solid #CCC; padding: 3px 8px; font-size: 11px; font-family: Tahoma, Geneva, sans-serif; }
	tr {border-left: 10px solid #CCC; }
	tr td {background: #F63; }
	tr.count1 td {background: white; }
	tr.sessie1 {border-left-color: #3F6; }
	tr.sessie2 {border-left-color: #39C; }
	tr.sessie3 {border-left-color: #696; }
	tr.sessie4 {border-left-color: #93C; }
	tr.sessie5 {border-left-color: #F33; }
	tr.sessie6 {border-lef-colort: #FC0; }
</style>
<a href="_querylog.php?empty">clear</a>
<table>
	<tr>
    	<th>Datum</th>
        <th>URL</th>
        <th>QRY</th>
        <th>Tijd</th>
        <th>IP</th>
        <th>user</th>
        <th>sessie</th>
        <th>count</th>
    </tr>
<?
	include "inc.functions.php"; 
	$strDBlog = "cache/dbqueries.json";
	
	$arSessies = array(); 
	$iSessies = 0; 
	
	if (isset($_GET["empty"])) {
		json($strDBlog, array()); 
		header('Location: _querylog.php');
	} else {
		
		$arQueries = json($strDBlog);  
		foreach (array_reverse($arQueries) as $strKey=>$arQRY) {
			$arClasses = array(
				"count" . $arQRY["count"], 
			); 
			if (!isset($arSessies[$arQRY["sessie"]])) $arSessies[$arQRY["sessie"]] = "sessie" . (++$iSessies); 
			$arClasses[] = $arSessies[$arQRY["sessie"]]; 
			echo ("<tr class=\"" . implode(" ", $arClasses) . "\">
					<td>-" . (time()-$arQRY["date"]) . " s</td>
					<td>" . $arQRY["url"] . "</td>
					<td>" . $arQRY["sql"] . "</td>
					<td>" . $arQRY["tijd"] . "</td>
					<td>" . $arQRY["ip"] . "</td>
					<td>" . $arQRY["user"] . "</td>
					<td>" . $arQRY["sessie"] . "</td>
					<td>" . $arQRY["count"] . "</td>
				</tr>"); 
		}
	}
?></table>
