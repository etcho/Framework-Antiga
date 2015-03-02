<?php
	function send_file($file){
        if (file_exists($file)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            return true;
        }
        else
            return false;
    }
	
	function remover_arquivo($arquivo){
		return file_exists($arquivo) ? unlink($arquivo) : false;
	}
	
	//gera a miniatura de uma imagem
    function gerar_minuatura($photo, $output, $new_width){
        $source = imagecreatefromstring(file_get_contents($photo));
        list($width, $height) = getimagesize($photo);
        if ($width>$new_width){
            $new_height = ($new_width/$width) * $height;
            $thumb = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($thumb, $output, 80);
        }
        else
            copy($photo, $output);
    }
	
	//função que returna uma string com o conteudo de um arquivo incluído
    function carregar_arquivo($arquivo, $params = array()){
        ob_start();
        include($arquivo);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
	
	//inclui o pacote.php
    function carregar_pacote($pacote){
        $pacote = $pacote;
        include("lib/pacote.php");
    }
	
	//inclui um arquivo a partir do diretório raiz das páginas
	function incluir($arquivo, $params = array()){
		if (!strpos($arquivo, "."))
			$arquivo .= ".php";
		if (file_exists("application/".$arquivo)){
			//echo remover_bom(carregar_arquivo("application/".$arquivo));
			include("application/".$arquivo);
			$_GET["pagina_incluida"] = "application/".$arquivo;
		}
		else{
			echo remover_bom(carregar_arquivo("application/".$_GET["not_found"]));
			$_GET["pagina_incluida"] = true;
			return false;
		}
		return true;
	}
	
	//faz a conversão de uma string de parâmetros GET para o array $_GET
	function converter_parametros($string){
		$string = str_replace("?", "&", $string);
		$strings = explode("&", $string);
		foreach ($strings as $string){
			$string = explode("=", $string);
			$_GET[$string[0]] = $string[1];
		}
	}
	
	function javascript_include($js){
		if (!strpos($js, ".js"))
			$js .= ".js";
		return '<script src="'.URL.'assets/js/'.$js.'" language="javascript"></script>';
	}
	
	function css_include($css, $media = "screen"){
		if (!strpos($css, ".css"))
			$css .= ".css";
		return '<link rel="stylesheet" href="'.URL.'assets/css/'.$css.'" media="'.$media.'" type="text/css">';
	}
	
	//remove o caracter de marcação no início de arquivos utf-8
	function remover_bom($string) {
		if (substr($string, 0,3) == pack("CCC", 0xef, 0xbb, 0xbf))
			$string=substr($string, 3); 
		return $string; 
	}
	
	function carregar_arquivos_do_diretorio($dir){
		$diretorio = dir($dir);
		while ($arquivo = $diretorio->read()){
			if ($arquivo != "." && $arquivo != "..")
				if (is_dir($dir."/".$arquivo))
					carregar_arquivos_do_diretorio($dir."/".$arquivo);
				else
					echo remover_bom(carregar_arquivo($dir."/".$arquivo));
		}
		$diretorio -> close();
	}
?>