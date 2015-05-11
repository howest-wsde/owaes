<?php
	include "inc.default.php"; // should be included in EVERY file 
	$oSecurity = new security(TRUE); 
	if (!$oSecurity->admin()) stop("admin"); 
	
	$oPage->addJS("script/admin.js"); 
	$oPage->addCSS("style/admin.css"); 
  
 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        	echo $oPage->getHeader(); 
		?>
    </head>
    <body id="index">
        <?php echo $oPage->startTabs(); ?> 
    	<div class="body">
        	
                <div class="container">
                  <div class="row">
					        <?php 
                                echo $oSecurity->me()->html("user.html");
                            ?>
                    </div>
                    
                    <div class="main market admin-users"> 
                       <?php include "admin.menu.xml"; ?>
                        
                        <!-- <h1>Users: </h1>  -->
                        <table class="editable">
                        	<tr> 
                            	<th class="order">first name</th>
                            	<th class="order">last name</th>
                            	<th class="order">alias</th>
                            	<th class="order">login</th>
                            </tr>
							<?php
                                $oUsers = new userList(); 
                                
                                $itemsPerPage = 10;
                                $pages = array_chunk($oUsers->getList(), $itemsPerPage);

                                 
                                 if (isset($_GET['showpage'])){
                                   $pageKey = (int)$_GET['showpage'];
                                 }else{$pageKey = 0;}
                              
                                 if($pageKey >= count($pages)){
                                    $pageKey = count($pages)-1;
                                 }
                                foreach ($pages[$pageKey] as $oUser) {
									echo "<tr>";  
                                    echo "<td id=\"tblUsers_" . $oUser->id() . "_firstname\">" . $oUser->firstname() . "</td>"; 
                                    echo "<td id=\"tblUsers_" . $oUser->id() . "_lastname\">" . $oUser->lastname() . "</td>";  
                                    echo "<td id=\"tblUsers_" . $oUser->id() . "_alias\">" . $oUser->alias() . "</td>";  
                                    echo "<td id=\"tblUsers_" . $oUser->id() . "_login\">" . $oUser->login() . "</td>";  
									echo "</tr>"; 
                                }
                            ?>
                        </table>
                        
                        <?php 
                        echo("<div class='links'>");
                        
                         if($pageKey > 0){
                            $prevPage = $pageKey -1;
                             echo("<a href='admin.users.php?showpage=$prevPage'>BACK</a>");
                          }
                         
                          for($i=1; $i< count($pages)+1; $i++): 
                                $j = $i-1;
                                    if($pageKey + 1 == $i){
                                        echo("<span>".$i."</span>");
                                    }else{
                                        echo("<a href='admin.users.php?showpage=$j'> $i</a>");
                                    }
                                    
                                 endfor;
                                 
                          if($pageKey < (count($pages)-1)){
                            $nextPage = $pageKey +1;
                             echo("<a href='admin.users.php?showpage=$nextPage'>NEXT</a>");
                          }
                         echo("</div>");
                        ?>
							 
                    </div>
                </div> 
        	<?php echo $oPage->endTabs(); ?>
        </div>
        <div class="footer">
        	<?php echo $oPage->footer(); ?>
        </div>
    </body>
</html>
