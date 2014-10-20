<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(FALSE); 
	
	$strID = settings("domain", "name"); 
	
	$arLogin = array(); 
	
	$strRedirect = fixPath("main.php"); 
	
	// GOOGLE & YAHOO 
	$openid = new LightOpenID($strID); 
	if ($openid->mode) {
		if($openid->validate()) {  
			$data = $openid->getAttributes();
			switch( $openid->data["openid_op_endpoint"] ) {
				case "https://www.google.com/accounts/o8/ud": 
					$arLogin["site"] = "google"; 
					$arLogin["naam"] = $data['namePerson/last'];
					$arLogin["voornaam"] = $data['namePerson/first'];
					$arLogin["id"] = $data['contact/email']; 
					$arLogin["email"] = $data['contact/email']; 
					break; 
				case "https://me.yahoo.com": 
					$arLogin["site"] = "yahoo"; 
					$arLogin["naam"] = $data['namePerson/last'];
					$arLogin["voornaam"] = $data['namePerson/first'];
					$arLogin["id"] = $data['contact/email']; 
					$arLogin["email"] = $data['contact/email']; 
					break;  
				case "http://www.owaes.org/v2/openid/provider":  
					$arLogin["site"] = "owaes"; 
					$arLogin["naam"] = $data['namePerson/friendly'];
					$arLogin["voornaam"] = "";
					$arLogin["id"] = $data['contact/email']; 
					$arLogin["email"] = $data['contact/email']; 
					break;  
			} 
		}  
	} 
	
	
# echo "<hr>arLogin: "; 
# 	vardump($arLogin); 
	
	// FACEBOOK   
	$facebook = new Facebook(array(
		'appId'  => settings("facebook", "loginapp", "id"),
		'secret' => settings("facebook", "loginapp", "secret"),
	));
	
	$user = $facebook->getUser();  
	if ($user) { 
		try {
			$user_profile = $facebook->api('/me'); 
			
			$arLogin["site"] = "facebook"; 
			$arLogin["id"] = $user_profile["id"];
			$arLogin["naam"] = $user_profile["first_name"];
			$arLogin["voornaam"] = $user_profile["last_name"];
			$arLogin["email"] = $user_profile["email"];
		} catch (FacebookApiException $e) { 
			$user = null;
		}
	} 
	
	if (isset($arLogin["site"])) {
		if ($oSecurity->doLogin($arLogin["email"])) {
			$oLog = new log("gebruiker ingelogd via " . $arLogin["site"], $arLogin); 
		} else {
			// new user 
			$oUser = new user(); 
			$oUser->firstname($arLogin["voornaam"]); 
			$oUser->lastname($arLogin["naam"]); 
			$oUser->email($arLogin["email"]); 
			$oUser->alias("", TRUE); 
			$oUser->password(uniqueKey());  
		
			$oUser->update();  
			$oMail = new email(); 
				$oMail->setTo($oUser->email, $oUser->getName());
				$strMail = $oUser->HTML("mail.subscribe.html");  
				$oMail->setBody($strMail);  
				$oMail->setSubject("OWAES inschrijving"); 
			$oMail->send();  
			$oLog = new log("user aangemaakt via " . $arLogin["site"], $arLogin); 
			$bResult = $oSecurity->doLogin($arLogin["email"]);   
		}

	} 
	//var_dump($arLogin); 
	
?><script>
	window.opener.location.replace("<? echo $strRedirect; ?>");
	window.close();
</script>