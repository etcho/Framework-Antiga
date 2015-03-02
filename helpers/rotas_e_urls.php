<?php
	function redirecionar($pagina){
		header("location: ".url_para($pagina));
		exit;
	}
	
	function redirecionar_admin($pagina){
		header("location: ".url_para_admin($pagina));
		exit;
	}
	
	//gera uma url para determinada pagina
    function url_para($pagina){
        return vazio($pagina) ? URL : URL."index.php/".$pagina;
    }
	
	function url_para_admin($pagina){
        return URL."admin.php/".$pagina;
    }
	
	function url_atual(){
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
			$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		return $pageURL;
	}
	
	function remover_get($remover, $url = ""){
		$url = vazio($url) ? url_atual() : $url;
		/*if (strpos($remover, "="))
			$url = str_replace("&&", "&", str_replace($remover, "", $url));
		else{
			$pos = strpos($url, $remover);
			if ($pos){
				$next_separator = strpos($url, "&", $pos);
				$url = substr($url, 0, $pos).substr($url, $next_separator, strlen($url));
			}
		}*/
		$url = str_replace("?&", "?", $url);
		if (in_array($url[strlen($url)-1], array("?", "&", "/")))
			$url = substr($url, 0, strlen($url)-1);
		return $url;
	}
	
	function adicionar_get($adicionar, $url = ""){
		$url = vazio($url) ? url_atual() : $url;
		if (strpos($url, "/?"))
			$url .= "&".$adicionar;
		else{
			if ($url[strlen($url)-1] != "/")
				$url .= "/";
			$url .= "?".$adicionar;
		}
		return $url;
	}
?>