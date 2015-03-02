<?php
	$params = $_GET["params"];
	
	if (vazio($params[0]) || $params[0] == "home")
		incluir("home/index");
?>