<?php	
	$_GET["not_found"] = "404_not_found.php";
	
	$_GET["title"] = "Gold Educacional";
	$_GET["description"] = "";
	$_GET["keywords"] = "";
	$_GET["author"] = "";
	$_GET["og:url"] = "";
	$_GET["og:image"] = "";
	
	if (!strpos($_SERVER["REQUEST_URI"], "index.php"))
		$params = array();
	else{
		$params = strpos($_SERVER["REQUEST_URI"], ".php");
		$params = substr($_SERVER["REQUEST_URI"], $params + 5);
		$params = explode("/", $params);
	}
	$_GET["params"] = $params;
	
	require_once("lib/config.php");
	
	if (!$_GET["pagina_incluida"])
		incluir($_GET["not_found"]);
?>