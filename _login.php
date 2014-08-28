<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$oSecurity = new security(FALSE); 
	$oSecurity->doLogout(FALSE);  
	
	$oUser = new user(); 
	
	if (isset($_GET["p"])) {
		$strRedirect = $_GET["p"];
	} else if (isset($_POST["from"])) {
		$strRedirect = $_POST["from"];
	} else $strRedirect = "main.php";  

	if (isset($_POST["dosignup"])) {
		$bResult = $oSecurity->doLogin($_POST["username"], $_POST["pass"]); 
		if ($bResult == TRUE) { 
			header("Location: " . $strRedirect); 
			exit(); 
		}
		
		$arErrors = array();  
		if (!$oUser->login($_POST["username"])) $arErrors["username"] = "De gekozen loginnaam is ongeldig of bestaande. Een andere werd voorgesteld "; 
		$oUser->firstname($_POST["firstname"]); 
		$oUser->lastname($_POST["lastname"]); 
		if (!$oUser->email($_POST["email"])) $arErrors["email"] = "Dit e-mailadres bestaat reeds in het systeem";  
		$oUser->alias("", TRUE); 
		$oUser->password($_POST["pass"]);
		if ($_POST["pass"] == "") $arErrors["password"] = "Paswoord is verplicht";  
		if (count($arErrors) == 0)  {
			$oUser->update();  
			$oMail = new email(); 
				$oMail->setTo($oUser->email, $oUser->getName());
				$strMail = $oUser->HTML("templates/mail.subscribe.html");  
				$oMail->setBody($strMail);  
				$oMail->setSubject("OWAES inschrijving"); 
			$oMail->send();  
			$oLog = new log("user aangemaakt", array(
												"id" => $oUser->id(),  
												"naam" => $oUser->login(), 
												"login" => $oUser->getName(),  
											)); 
			$bResult = $oSecurity->doLogin($_POST["username"], $_POST["pass"]); 
			header("Location: " . $strRedirect); 
			exit(); 
		}
	}

	if (isset($_POST["dologin"])) {
		$bResult = $oSecurity->doLogin($_POST["username"], $_POST["pass"]); 
		if ($bResult == TRUE) {
			header("Location: " . $strRedirect); 
			exit(); 
		} else {
			echo $oSecurity->errorMessage(); 	
		}
	}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="login"> 
    	<div class="body">
            <div class="login">
                <form method="post">
                	Inloggen: 
                    <input type="hidden" name="from" id="from" value="<? echo $strRedirect; ?>" />
                    <input type="text" name="username" id="username" />
                    <input type="password" name="pass" id="pass" />
                    <input type="submit" name="dologin" value="inloggen" />
                    <a href="recover.php">paswoord vergeten</a>
                </form>  
                <div class="openid">
                    <?
			
						$strURL = fixPath("login.php", TRUE);
						$strReturnURL = fixPath("loggedin.php", TRUE);
						$strID = settings("domain", "name"); 
						session_start();
					
						$strHTML = "<ul>"; 
										
						// FACEBOOK:   
						$facebook = new Facebook(array(
							'appId'  => settings("facebook", "loginapp", "id"),
							'secret' => settings("facebook", "loginapp", "secret"),
						)); 
						$strHTML .= "<li><a class=\"login\" href=\"" . $facebook->getLoginUrl(array(
							'scope' => 'email', 
							'redirect_uri'=>$strReturnURL
						)) . "\" rel=\"1020,575\"><img src=\"img/facebook.png\" alt=\"Facebook\"/></a></li>"; 
						
						// GOOGLE:  
						$oOpenid = new LightOpenID($strID); 
						$oOpenid->identity = 'https://www.google.com/accounts/o8/id';
						$oOpenid->required = array(
							'namePerson/first',
							'namePerson/last',
							'contact/email',
						);
						$oOpenid->returnUrl = $strReturnURL;   
						$strHTML .= "<li><a class=\"login\" href=\"" . $oOpenid->authUrl() . "\" rel=\"400,560\"><img src=\"img/google.png\" alt=\"Google\"/></a></li>";  
						
						// YAHOO:  
						$oOpenid = new LightOpenID($strID); 
						$oOpenid->identity = 'https://me.yahoo.com';
						$oOpenid->required = array(
							'namePerson/first',
							'namePerson/last',
							'contact/email',
						);
						$oOpenid->returnUrl = $strReturnURL;   
						$strHTML .= "<li><a class=\"login\" href=\"" . $oOpenid->authUrl() . "\" rel=\"570,535\"><img src=\"img/yahoo.png\" alt=\"Yahoo\"/></a></li>";  
						
						$strHTML .= "</ul>";
						
						echo $strHTML;  
                    ?>
                </div>
            </div> 
            <div class="signup">
            	of maak een account aan: 
                <form method="post">
                    <input type="hidden" name="from" id="from" value="index.php" />
                    <dl>
                    	<dt>Voornaam: </dt>
                        <dd><input type="text" name="firstname" id="firstname" value="<? echo inputfield($oUser->firstname()); ?>" /></dd>
                    	<dt>Naam: </dt>
                        <dd><input type="text" name="lastname" id="lastname" value="<? echo inputfield($oUser->lastname()); ?>" /></dd>
                    	<dt>Login: </dt>
                        <dd><input type="text" name="username" id="username" value="<? echo ((isset($_POST["dosignup"])) ? inputfield($oUser->login()) : ""); ?>" /></dd>
                        <?
                        	if (isset($arErrors["username"])) echo ("<dd>" . $arErrors["username"] . "</dd>"); 
						?>
                    	<dt>E-mail: </dt>
                        <dd><input type="email" name="email" id="email" value="<? echo inputfield($oUser->email()); ?>" /></dd>
                        <?
                        	if (isset($arErrors["email"])) echo ("<dd>" . $arErrors["email"] . "</dd>"); 
						?>
                    	<dt>Nieuw paswoord: </dt>
                        <dd><input type="password" name="pass" id="pass" /></dd>
                        <?
                        	if (isset($arErrors["password"])) echo ("<dd>" . $arErrors["password"] . "</dd>");  
						?>
                    </dl> 
                    <input type="submit" name="dosignup" value="inschrijven" /> 
                </form> 
            
            </div> 
        </div>
        <div class="footer"> 
        </div>
    </body>
</html>
