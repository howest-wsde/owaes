<?php
	include "inc.default.php"; // should be included in EVERY file 
	  
	$oSecurity = new security(FALSE); 
	 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?> 
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
                <div class="signup col-lg-12">
                    <div class="well">
                    	<p>Bedankt voor uw inschrijving. </p>
                        <p>U ontvangt zo meteen een mail waarin u uw e-mailadres kunt bevestigen. Vanaf dan is uw inschrijving voltooid.</p>
                    </div>
                </div>
            </div> 
        </div>
		 
        <div class="footer"> 
        </div>
    </body>
</html>
