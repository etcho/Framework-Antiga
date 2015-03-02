<?php
#############################################
### funções para manupilação de templates ###
#############################################

	//faz a inclusão do $arquivo de template. caso a template mobile seja necessária e não seja
	//encontrada, a template padrão será carregar no lugar
	function template($arquivo, $arquivo1 = "", $arquivo2 = ""){
		if (!vazio($arquivo)){
			if (!strpos($arquivo, ".php"))
				$arquivo .= ".php";
			if (!file_exists("templates/".$arquivo))
				include("templates/".substr($arquivo, strpos($arquivo, "mobile_") + 7, strlen($arquivo)));
			else
				include("templates/".$arquivo);
			template($arquivo1);
			template($arquivo2);
		}
	}
?>