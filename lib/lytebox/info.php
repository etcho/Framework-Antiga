<?php
Error_Reporting(E_ALL & ~E_NOTICE);
//header("Pragma: no-cache");
//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Information</title>
<style> 
<!--
html {
	color:#000000;
	background-color:#FFFFFF;
	}
body {
	background:url(images/info_background.gif) #FFFFFF no-repeat;
	padding:0px;
	margin:0px;
	height:424px;
	width:572px;
	}
.info {
	position:absolute;
	top:25px;
	left:35px;
	width:510px; 
	height:375px; 
	padding:0px;
	border:0px;
	font:11px verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif; 
	color:#000000; 
	background: transparent;
	}
-->
</style>
</head>
<body>
<div class="info"><?php 
if (isset($_GET['info'])){
	$text = $_GET['info'];
	$text = str_replace('<br/>',"\n",$text);
} else {
	$text = 'This page is showing text from GET "info" variable';
}
echo $text;
?></div>
</body>
</html>
