<?
	$iPage = isset($_GET["p"]) ? intval($_GET["p"]) : 0; 
	$iPP = 50; 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>

@font-face {
    font-family: 'icomoon';
    src: url('fonts/icomoon.eot?-mmzquo');
    src: url('fonts/icomoon.eot?#iefix-mmzquo') format('embedded-opentype'), url('fonts/icomoon.woff?-mmzquo') format('woff'), url('fonts/icomoon.ttf?-mmzquo') format('truetype'), url('fonts/icomoon.svg?-mmzquo#icomoon') format('svg');
    font-weight: normal;
    font-style: normal;
}
span {text-align: center; }
span.f {
    font-family: 'icomoon';
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
	font-size: 20px; 
    line-height: 1;
	padding: 10px 10px 0; 
    /* Better Font Rendering =========== */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
	display: inline-block; width: 50px; height: 50px; border: 1px solid gray; 
}
span span {color: #999; font-size: 9px; border: 0; clear: both; display: block; margin-top: 15px;  }
<?
    	for ($i=($iPage*$iPP)+1; $i<=($iPage+1)*$iPP; $i++) {
			echo ("span.e$i:before {content: \"\e" . (599+$i) . "\"; } \n"); 
		}
	?>

</style>
<title>Icomoon</title>
</head> 
<body>
	<?
    	for ($i=($iPage*$iPP)+1; $i<=($iPage+1)*$iPP; $i++) {
			echo ("<span class=\"f e$i\"><span>e" . (599+$i) . "</span></span>"); 
		}
		if ($iPage > 0) echo ("<a href=\"_icomoon.php?p=" . ($iPage-1) . "\">vorige</a>"); 
		echo ("<a href=\"_icomoon.php?p=" . ($iPage+1) . "\">volgende</a>"); 
	?>
	
</body>
</html>
