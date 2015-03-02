<?php
	function inserir_acentos($string){
		$a = "ÁÉÍÓÚÝáéíóúýÀÈÌÒÙàèìòùÃÕãõÂÊÎÔÛâêîôûÄËÏÖÜäëïöüÿÇçÑñ";
		$b = array(
					"&Aacute;",
					"&Eacute;",
					"&Iacute;",
					"&Oacute;",
					"&Uacute;",
					"&Yacute;",
					"&aacute;",
					"&eacute;",
					"&iacute;",
					"&oacute;",
					"&uacute;",
					"&yacute;",
					"&Agrave;",
					"&Egrave;",
					"&Igrave;",
					"&Ograve;",
					"&Ugrave;",
					"&agrave;",
					"&egrave;",
					"&igrave;",
					"&ograve;",
					"&ugrave;",
					"&Atilde;",
					"&Otilde;",
					"&atilde;",
					"&otilde;",
					"&Acirc;",
					"&Ecirc;",
					"&Icirc;",
					"&Ocirc;",
					"&Ucirc;",
					"&acirc;",
					"&ecirc;",
					"&icirc;",
					"&ocirc;",
					"&ucirc;",
					"&Auml;",
					"&Euml;",
					"&Iuml;",
					"&Ouml;",
					"&Uuml;",
					"&auml;",
					"&euml;",
					"&iuml;",
					"&ouml;",
					"&uuml;",
					"&yuml;",
					"&Ccedil;",
					"&ccedil;",
					"&Ntilde;",
					"&ntilde;"
					);
		
		for ($x=0; $x < count($b); $x++){
			$string = str_replace($a[$x], $b[$x], $string);
		}
		return $string;
	}
	
	function inserir_acentos2($string){
		$a = array("Á" => "&Aacute;", "É" => "&Eacute;", "Í" => "&Iacute;", "Ó" => "&Oacute;", "Ú" => "&Uacute;", "Ý" => "&Yacute;", "á" => "&aacute;", "é" => "&eacute;", "í" => "&iacute;", "ó" => "&oacute;", "ú" => "&uacute;", "ý" => "&yacute;", "À" => "&Agrave;", "È" => "&Egrave;", "Ì" => "&Igrave;", "Ò" => "&Ograve;", "Ù" => "&Ugrave;", "à" => "&agrave;", "è" => "&egrave;", "ì" => "&igrave;", "ò" => "&ograve;", "ù" => "&ugrave;", "Ã" => "&Atilde;", "Õ" => "&Otilde;", "ã" => "&atilde;", "õ" => "&otilde;", "Â" => "&Acirc;", "Ê" => "&Ecirc;", "Î" => "&Icirc;", "Õ" => "&Ocirc;", "Û" => "&Ucirc;", "â" => "&acirc;", "ê" => "&ecirc;", "î" => "&icirc;", "ô" => "&ocirc;", "û" => "&ucirc;", "Ä" => "&Auml;", "Ë" => "&Euml;", "Ï" => "&Iuml;", "Ö" => "&Ouml;", "Ü" => "&Uuml;", "ä" => "&auml;", "ë" => "&euml;", "ï" => "&iuml;", "ö" => "&ouml;", "ü" => "&uuml;", "ÿ" => "&yuml;", "Ç" => "&Ccedil;", "ç" => "&ccedil;", "Ñ" => "&Ntilde;", "ñ" => "&ntilde;");
		foreach ($a as $antes => $depois)
			$string = str_replace($antes, $depois, $string);
		return $string;
    }
	
	// função para retirar acentos e passar a frase para minúscula
    function normalizar($string){
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $permitidos = "abcdefghijklmnopqrstuvwxyz1234567890-";
        $array_permitidos = array();
        for ($i=0; $i<strlen($permitidos); $i++)
            array_push($array_permitidos, $permitidos[$i]);
        $string = utf8_decode($string);
        $string = strtr($string, utf8_decode($a), $b);
        $string = str_replace(" ", "-",$string);
        $string = str_replace("/", "", $string);
        $string = strtolower($string);
        for ($i=0; $i<strlen($string); $i++){
            if (!in_array(substr($string, $i, 1), $array_permitidos))
                $string = str_replace(substr($string, $i, 1), "", $string);
        }
		while (str_replace("--", "-", $string) != $string)
			$string = str_replace("--", "-", $string);
        return utf8_encode($string);
    }

?>