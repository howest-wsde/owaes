<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	
	$iID = intval($_GET["id"]); 
	$oGroup = group($iID);   
	
	$oNotification = new notification(); 
	$oNotification->read("group." . $iID);  
	
	
	$oExperience = new experience(me());  
	$oExperience->detail("reason", "pageload");     
	$oExperience->add(1);  
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="profile">
    		<? echo $oPage->startTabs(); ?> 
    		<div class="body content content-account-profile container">
        	
            	<div class="row">
					<? /*echo $oSecurity->me()->html("leftuserprofile.html"); */
                    echo $oSecurity->me()->html("user.html");
                    ?>
                </div>
 

				<?  
                
                //vardump($oProfile->friends()); 
                
                    //echo "<div class=\"masonry\">";
                    echo $oGroup->html("group.html"); 
                    //echo "</div>";
                ?>
 

        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
        <? 
			$oRechten = new usergrouprights($oGroup, me());  
			if ($oRechten->value("confirmed") === FALSE) { 
				?>
					<div class="modal fade" id="invitationModal">
					  <div class="modal-dialog">
						<div class="modal-content">
						  <div class="modal-header">
							<h4 class="modal-title">Groepsuitnodiging</h4>
						  </div>
						  <div class="modal-body">
							<p>Wilt u lid worden van de groep "<? echo $oGroup->naam(); ?>"!</p>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" id="btn-accept" data-dismiss="modal">Ja</button>
							<button type="button" class="btn btn-cancel" id="btn-cancel" data-dismiss="modal">Neen</button>
						  </div>
						</div><!-- /.modal-content -->
					  </div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					<script>
					$(document).ready(function () { 
						$("#invitationModal").modal({
							show: true,
							backdrop: "static",
							keyboard: false
						}); 
						$("#btn-accept").click( function (){
							$.post("<? echo fixPath("group.invitationresponse.ajax.php") ?>", {"g":<? echo $iID; ?> , "a": 1});   
						});
						$("#btn-cancel").click( function (){
							$.post("<? echo fixPath("group.invitationresponse.ajax.php") ?>", {"g":<? echo $iID; ?>, "a": 0});  
						}); 
					});
				</script>
				<? 
			}
		?>
    </body>
</html>