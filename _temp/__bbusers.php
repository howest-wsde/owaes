<style>
table {border-collapse: collapse; }
td {border: 1px solid gray; padding: 3px; }
</style> 
<?php 

	$arDB = array (
      'database' => 'owa1325401472972',
      'username' => 'owa1325401472972',
      'password' => 'xxxxx',
      'host' => 'owa1325401472972.db.10290819.hostedresource.com',
      'port' => '',
      'driver' => 'mysql',
      'prefix' => '',
    );
	
	$arVelden = array(
	 "uid", "name", "pass", "mail", "theme", "signature", "created", "access", "login", "status", "timezone", "language", "picture", "init" ,"data",  "uuid", 
	); 
	
	mysql_connect($arDB["host"], $arDB["username"], $arDB["password"]);
	mysql_select_db($arDB["database"]);
	
	$arCopy = array(); 
	
	$result = mysql_query("SELECT " . implode(", ", $arVelden) . " FROM users where status= 1 ; ");  // demo demoOwaes 
	$iTeller = 0; 
	echo ("<table>"); 
		while($row = mysql_fetch_array($result)) {
			if ($iTeller++ == 0) {
				
				echo "<tr>";
				for ($i=0; $i < mysql_num_fields($result); $i++) echo "<td>" .  mysql_fetch_field($result, $i)->name . "</td>";  
				
				echo "</tr>";
			}
			$arNew = array(); 
			echo "<tr>";
			for ($i=0; $i < mysql_num_fields($result); $i++) {
				echo "<td>" .  $row[mysql_fetch_field($result, $i)->name] . "</td>";  
				$arNew[mysql_fetch_field($result, $i)->name]  = $row[mysql_fetch_field($result, $i)->name]; 
			}
			
			$arCopy[] = $arNew; 
			
			echo "</tr>";
		}
	echo ("</table>"); 
	
	echo ("\n\n<br>\n\n"); 
	 
	//var_dump($arCopy); 
	 
	
	$arDB =  array (
      'database' => 'owaesStage',
      'username' => 'owaesStage',
      'password' => 'xxxxxxxxxxxx',
      'host' => 'owaesStage.db.10290819.hostedresource.com',
      'port' => '',
      'driver' => 'mysql',
      'prefix' => 'a_',
    ); 
	
	
	mysql_connect($arDB["host"], $arDB["username"], $arDB["password"]);
	mysql_select_db($arDB["database"]);
	
	$iUserID = 0; 
	
	$result = mysql_query("SELECT * FROM a_users order by uid desc limit 1;  ");  // demo demoOwaes 
 
	echo ("<table>"); 
		while($row = mysql_fetch_array($result)) {
			 	
				echo "<tr>";
				for ($i=0; $i < mysql_num_fields($result); $i++) echo "<td>" .  mysql_fetch_field($result, $i)->name . "</td>";  
				$iUserID = $row["uid"]; 
				echo "</tr>";
		 
		}
	echo ("</table>"); 
	
// 	exit();
	 
	foreach ($arCopy as $arItem){
		$strSQL = "SELECT * FROM a_users where mail like '" . $arItem["mail"] . "'; "; 
		$result = mysql_query($strSQL);  // demo demoOwaes  
		echo $strSQL; 
		if (mysql_num_rows($result) == 0) {
			$arItem["uid"] = ++$iUserID; 
			 echo "<p>" . $arItem["mail"] . " bestaat nog nie</p>"; 
			 $arImports = array(); 
			 foreach ($arVelden as $strVeld) $arImports[] = "'" . $arItem[$strVeld] . "'"; 
			 $strSQL = "insert into a_users (" . implode(", ", $arVelden) . ") values (" . implode(", ", $arImports) . "); "; 
			 $result = mysql_query($strSQL);
			
			echo mysql_errno() . ": " . mysql_error() . "\n";
			
			var_dump($result); 
			 echo "<p>" . $strSQL . " - done</p>"; 
		} else {
			echo "<p>" . $arItem["mail"] . " bestaat</p>"; 
		}
	}
?> 