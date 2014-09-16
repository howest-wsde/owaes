<?
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = security(TRUE); 
	if (!$oSecurity->admin()) $oSecurity->doLogout(); 
	
	$oPage->addJS("script/admin.js"); 
	$oPage->addCSS("style/admin.css"); 
 
 	if (isset($_POST["addgroep"])) {
		$oGroep = new group(); 
		$oGroep->naam($_POST["naam"]);
		$oGroep->info($_POST["info"]);
		$oGroep->admin($_POST["admin"]);
		$oGroep->update(); 
	}
 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="index">
            <? echo $oPage->startTabs(); ?> 
            
    	<div class="body">

                <div class="container">
                     <div class="row">
					    <? 
                        echo $oSecurity->me()->html("templates/user.html");
                        ?>
                    </div>
                    <div class="main market admin-groepen"> 
                        <ul>
                        	<li><a href="admin.php">Admin</a></li><li><a href="admin.users.php">Gebruikers</a></li>
                        </ul>
                    	<h1>Toevoegen: </h1>
                        <form method="post" class="groepToevoegenForm">
                        	<input type="text" name="naam" value="naam" />
                            
                            <select name="admin">
                            	<?
                                	$oUserList = new userlist();   
									foreach ($oUserList->getList() as $oUser) { 
										echo  "<option value=\"" . $oUser->id() . "\">" . $oUser->getName() . "</option>"; 	
									}
								?>
                            </select>
                            <textarea name="info">info</textarea>
                        	<input type="submit" name="addgroep" value="Toevoegen" class="btn btn-default"/>
                        </form>
                        
                        <h1>Groepen: </h1> 
                        <table class="editable">
                        	<tr>
                            	<th>id</th>
                            	<th>naam</th>
                            	<th>info</th>
                            	<th>admin</th>
                            	<th>...</th>
                            </tr>
							<?
                                $oGroepen = new grouplist(); 
                                
                                $itemsPerPage = 20;
                                $pages = array_chunk($oGroepen->getList(),$itemsPerPage);
                                
                                if(isset($_GET['showpage'])){
                                    $pageKey=(int)$_GET['showpage'];
                                }else{$pageKey=0;}
                                
                                if($pageKey >= count($pages)){
                                    $pageKey = count($pages)-1;
                                }
                                
                                foreach ($pages[$pageKey] as $oGroep) {
									echo "<tr>"; 
                                    echo "<td>" . $oGroep->id() . "</td>"; 
                                    echo "<td id=\"tblGroups_" . $oGroep->id() . "_naam\">" . $oGroep->naam() . "</td>"; 
                                    echo "<td id=\"tblGroups_" . $oGroep->id() . "_info\">" . $oGroep->info() . "</td>"; 
                                    echo "<td id=\"tblGroups_" . $oGroep->id() . "_admin\" class=\"user\">" . $oGroep->admin()->id() . "</td>"; 
                                    echo "<td><a href=\"admin.groepusers.php?group=" . $oGroep->id() . "\">users</a></td>"; 
									echo "</tr>"; 
                                }
                            ?>
                        </table>
					     <? 
                            echo("<div class='links'>");
                             if($pageKey > 0){
                                 $prevPage = $pageKey -1;
                                 echo("<a href='admin.groepen.php?showpage=$prevPage'>BACK</a>");
                             }
                             
                              for($i=1; $i< count($pages)+1; $i++): 
                                    $j = $i-1;
                                        if($pageKey + 1 == $i){
                                            echo("<span>".$i."</span>");
                                        }else{
                                            echo("<a href='admin.groepen.php?showpage=$j'> $i</a>");
                                        }
                                    
                                     endfor;
                                     
                           if($pageKey < (count($pages)-1)){
                                $nextPage = $pageKey +1;
                                echo("<a href='admin.groepen.php?showpage=$nextPage'>NEXT</a>");
                          }
                             echo("</div>");
                        ?>
                    </div>
                </div> 
        	<? echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<? echo $oPage->footer(); ?>
        </div>
    </body>
</html>
