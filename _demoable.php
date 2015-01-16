<?
	include "inc.default.php"; // should be included in EVERY file  

	$oSecurity = new security(FALSE); 
	
	$oDB = new database(); 
	
	$oDB->execute("delete from tblUsers where id not in (1, 4, 5, 7, 15, 16, 18, 21, 25, 26, 27, 28, 29, 31, 34, 35, 37, 40, 45, 46, 49, 51, 52, 54, 59, 60, 62, 63, 64, 65)"); 

	$oDB->execute("delete from tblMarketSubscriptions"); //  where user not in (select id from tblUsers); "); 
	
	$oDB->execute("delete from tblMarket where author not in (select id from tblUsers);"); 
	$oDB->execute("delete from tblMarket where createdby not in (select id from tblUsers where admin = 1); "); 
	$oDB->execute("delete from tblMarketDates where market not in (select id from tblMarket); "); 
	$oDB->execute("delete from tblMarketSubscriptions where market not in (select id from tblMarket); "); 
	$oDB->execute("delete from tblMarketTags where market not in (select id from tblMarket);  "); 

	$oDB->execute("update tblMarket set lastupdate = " . owaestime() . ";"); 
	$oDB->execute("update tblMarketDates set datum = datum + 7*24*60*60;"); 
	/*
	$oDB->execute("select distinct market from tblMarketDates where datum < " . (owaestime() + 7*24*60*60) . " ;"); 
	$arOwaesItems = array(); 
	while ($oDB->nextRecord()) {
		$arOwaesItems[] = $oDb->get("market"); 	
	}
	$oDB->execute("update tblMarketDates set datum = datum + 7*24*60*60 where market in (" . implode(",", $arOwaesItems) . ");"); 
	*/
	
	$oDB->execute("delete from tblConversations where sender not in (select id from tblUsers); "); 
	$oDB->execute("delete from tblNotifications; "); 
	$oDB->execute("delete from tblPayments; "); 
	$oDB->execute("delete from tblTransactions; "); 

?> 
