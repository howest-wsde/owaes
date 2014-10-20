<?
	include "inc.default.php"; // should be included in EVERY file 
	
	$oSecurity = new security(FALSE); 
	$oSecurity->doLogout(FALSE);  
	
	$oUser = new user(); 
	
	if (isset($_GET["p"])) {
		$strRedirect = $_GET["p"];
	} else if (isset($_POST["from"])) {
		$strRedirect = $_POST["from"];
	} else $strRedirect = "main.php?start";  

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
				$strMail = $oUser->HTML("mail.subscribe.html");  
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

    //if (isset($_POST["dologin"])) {
    //    $bResult = $oSecurity->doLogin($_POST["username"], $_POST["pass"]); 
    //    if ($bResult == TRUE) {
    //        header("Location: " . $strRedirect); 
    //        exit(); 
    //    } else {
    //        echo $oSecurity->errorMessage(); 	
    //    }
    //}
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
        <script>
            $(document).ready(function () { 
				$("a.pass-recover").click(function(){
					$("#paswoordvergeten").modal({
						show: true,
						backdrop: "true",
						keyboard: true
					});
				})
				
				$("form.form-recoverpw").submit(function(){ 
					strVal = $("#mailpaswoordlost").val(); 
					arFields = {}
					$(this).find(":input").each(function() { 
						arFields[this.name] = $(this).val();
					});
					$("#requestwachtwoordbody div.modal-body").html("<p>bezig met verzenden...</p>"); 
					$("#requestwachtwoordbody").load("recover.php", arFields); 
					return false; 	
				})
				
				<? if (isset($_GET["recover"])) { ?>
					$("#paswoordvergeten").modal({
						show: true,
						backdrop: "static",
						keyboard: false
					});
					$("#requestwachtwoordbody div.modal-body").html("<p>bezig met laden...</p>"); 
					$("#requestwachtwoordbody").load("recover.php", {"code": "<? echo $_GET["recover"]; ?>"}); 
				<? } ?>
			});
		</script>
    </head>
    <body id="login">
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="row">
                <div class="navbar-header">
                    <a href=""><h1 class="navbar-brand">OWAES</h1></a>
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse" id="navbar-main"><ul class="nav navbar-nav navbar-right"></ul></div>
            </div>
        </div>
    </nav>
    
    	<div class="body container content content-login">
        <div class="row">
            <div class="login col-lg-5">
            <div class="well">
            
            <?
            
                if (isset($_POST["dologin"])) {
		            $bResult = $oSecurity->doLogin($_POST["username"], $_POST["pass"]); 
		            if ($bResult == TRUE) {
			            header("Location: " . $strRedirect); 
			            exit(); 
		            } else {
                        $strErrorLogin = "<div class=\"alert alert-dismissable alert-danger\">";
                        $strErrorLogin .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">x</button>";
                        $strErrorLogin .= "<strong>Aanmelden mislukt: </strong>" . $oSecurity->errorMessage();
			            $strErrorLogin .= "</div>"; 
                        
                        echo $strErrorLogin;  
		            }
	            }
            
            ?>
            
                <form method="post" class="form-horizontal">
                	<fieldset>
                        <legend>Aanmelden</legend>
                    <input type="hidden" name="from" id="from" value="<? echo $strRedirect; ?>" />
                    <div class="form-group">
                            <!-- <label for="username" class="col-lg-3 control-label">Gebruikersnaam</label> -->
                            <div class="col-lg-12">
                                <input type="text" name="username" class="username form-control" id="username" placeholder="Gebruikersnaam of e-mailadres" autofocus />
                            </div>
                        </div>
                    <div class="form-group">
                            <!-- <label for="pass" class="col-lg-3 control-label">Wachtwoord</label> -->
                            <div class="col-lg-12">
                                <input type="password" name="pass" class="pass form-control" id="pass" placeholder="Wachtwoord" /> 
                            </div>
                        </div>
                    <div class="form-group">
                            <div class="col-lg-12"> <!-- col-lg-offset-3 -->
                            <a class="pass-recover" href="#wachtwoord">Wachtwoord vergeten?</a>
                                <button type="submit" name="dologin" class="btn btn-default btn-login pull-right">Aanmelden</button>
                            </div>
                        </div>
                    
                    </fieldset>
                </form>  
                <div class="openid">
                    <?
			
						$strURL = fixPath("login.php", TRUE);
						$strReturnURL = fixPath("loggedin.php", TRUE);
						$strID = settings("domain", "name"); 
						session_start();
					
						$strHTML = "<ul class=\"socialmedia\">"; 
										
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
						

						// OWAES:  
						$oOpenid = new LightOpenID($strID); 
						$oOpenid->identity = 'http://www.owaes.org/v2';
						$oOpenid->required = array(
							'namePerson/first',
							'namePerson/last',
							'contact/email',
						);
						$oOpenid->returnUrl = $strReturnURL;   
						$strHTML .= "<li><a class=\"login\" href=\"" . $oOpenid->authUrl() . "\" rel=\"400,560\"><img src=\"img/owaes.png\" alt=\"OWAES\"/></a></li>";  
						
						
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
            </div> 
            <div class="signup col-lg-7">
            <div class="well">
            <form method="post" class="form-horizontal">
                	<fieldset>
                        <legend>Registreren (nieuw bij OWAES)</legend>
                    <input type="hidden" name="from" id="from" value="index.php" />
                    <div class="form-group">
                        <label for="firstname" class="control-label col-lg-3">Voornaam:</label>
                        <div class="col-lg-9">
                            <input type="text" name="firstname" class="firstname form-control" id="firstname" placeholder="Voornaam" value="<? echo inputfield($oUser->firstname()); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label col-lg-3">Familienaam:</label>
                        <div class="col-lg-9">
                            <input type="text" name="lastname" class="lastname form-control" id="lastname" placeholder="Familienaam" value="<? echo inputfield($oUser->lastname()); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username" class="control-label col-lg-3">Loginnaam:</label>
                        <div class="col-lg-9">
                            <input type="text" name="username" class="username form-control" id="username" placeholder="Loginnaam" value="<? echo ((isset($_POST["dosignup"])) ? inputfield($oUser->login()) : ""); ?>" />
                            <?
                        	    if (isset($arErrors["username"])) echo ("<strong class=\"text-danger\">" . $arErrors["username"] . "</strong>"); 
						    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label col-lg-3">E-mailadres:</label>
                        <div class="col-lg-9">
                            <input type="text" name="email" class="email form-control" id="username" placeholder="E-mailadres" value="<? echo inputfield($oUser->email()); ?>" />
                            <?
                        	    if (isset($arErrors["email"])) echo ("<strong class=\"text-danger\">" . $arErrors["email"] . "</strong>"); 
						    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pass" class="control-label col-lg-3">Wachtwoord:</label>
                        <div class="col-lg-9">
                            <input type="password" name="pass" class="pass form-control" id="pass" placeholder="Wachtwoord" />
                            <?
                        	    if (isset($arErrors["password"])) echo ("<strong class=\"text-danger\">" . $arErrors["password"] . "</strong>"); 
						    ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pass-repeat" class="control-label longlabel col-lg-3">Wachtwoord herhalen:</label>
                        <div class="col-lg-9">
                            <input type="password" name="pass-repeat" class="pass-repeat form-control" id="pass-repeat" placeholder="Wachtwoord herhalen" />
                            <?
                                //if (isset($arErrors["password"])) echo ("<p>" . $arErrors["password"] . "</p>"); 
						    ?>
                        </div>
                    </div>
                    <div class="form-group">
                            <div class="col-lg-12"> <!-- col-lg-offset-3 -->
                                <button type="submit" name="dosignup" class="btn btn-default pull-right">Registreren</button>
                            </div>
                    </div>
                    
                    
                    </fieldset>
                </form> 
                </div>
            </div>
            </div> 
        </div>
		<div class="modal fade" id="paswoordvergeten">
            <form method="post" class="form-horizontal form-recoverpw">
              <div class="modal-dialog">
                <div class="modal-content" id="requestwachtwoordbody">
                  <div class="modal-header">
                    <h4 class="modal-title">Wachtwoord vergeten</h4>
                  </div>
                  <div class="modal-body">
                    <p>Geef uw e-mailadres of gebruikersnaam op om een nieuw wachtwoord aan te vragen</p> 
                    <div class="form-group">
                    <!-- <label for="username" class="col-lg-3 control-label">Gebruikersnaam</label> -->
                    <div class="col-lg-12">
                    	<input type="hidden" name="recover" value="y" />
	                    <input type="text" name="search" class="search form-control" id="mailpaswoordlost" placeholder="E-mailadres of gebruikersnaam" autofocus />
                    </div>
                    </div> 
     
                  </div>
                  <div class="modal-footer"> 
                    <input type="submit" class="btn btn-default" id="btn-paswoord" value="Aanvragen" />
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
         </form>
        </div><!-- /.modal -->
        <div class="footer"> 
        </div>
    </body>
</html>
