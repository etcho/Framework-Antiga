<?php
#############################################
### outras funções auxiliares para o HTML ###
#############################################

	function script($script){
		return '<script language="javascript"> '.$script.' </script>';
	}
	
	//gera um * vermelho indicando que o campo é obrigatório
    function campo_obrigatorio(){
        $conteudo = "<span class=\"obrigatorio\" title=\"Preenchimento obrigatório\">*</span>";
        return $conteudo;
    }
	
	//gera um determinado número de tags <br>
    function brs($numero){
        $conteudo = "";
        for ($i=1; $i<=$numero; $i++)    
            $conteudo .= "<br />";
        return $conteudo;
    }
	
	//corta o texto no comprimento passado e gera os links 'mais' e 'menos' qndo necessário
	function leia_mais_automatico($texto, $limite, $mostrar_link = true){
		$conteudo = "";
		$count = 1;
		$texto_original = $texto;
		if ($limite == 0)
			$restante = $texto_original;
		else{
			$texto = str_split($texto);
			foreach ($texto as $letra){
				if ($count < $limite)
					$conteudo .= $letra;
				elseif ($letra != " ")
					$conteudo .= $letra;
				else{
					$restante = substr($texto_original, $count, strlen($texto_original));
					break;
				}
				$count++;
			}
		}
		$rand = rand(1, 99999999);
		if (!vazio($restante))
			if ($mostrar_link)
				$conteudo = trim($conteudo)."<span id='reticencias_".$rand."'>... <a href='#' onclick='$(\"#reticencias_".$rand."\").hide(); $(\"#texto_restante_".$rand."\").show(); return false'>[mais]</a></span>"."<span style='display: none' id='texto_restante_".$rand."'> ".$restante." <a href='#' onclick='$(\"#reticencias_".$rand."\").show(); $(\"#texto_restante_".$rand."\").hide(); return false'>[menos]</a></span>";
			else
				$conteudo .= "...";
		return $conteudo;
	}
	
	function img($src, $atributos = ""){
		if (!strpos($src, ".png") && !strpos($src, ".gif") && !strpos($src, ".jpg") && !strpos($src, ".bmp"))
			$src .= ".png";
		$img = '<img src="'.URL.'assets/images/18x18/'.$src.'" '.$atributos.' />';
		return $img;
	}
	
	//gera o botão de curtir para a url passada
	function curtir_facebook($url){
		return '<iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&amp;send=true&amp;layout=standard&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=25" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:25px;" allowTransparency="true"></iframe>';
	}
	
	//cria a tag <script> para o js passado
	function incluir_js($js){
		if (!strpos($js, ".js") && !strpos($js, ".php"))
			$js .= ".js";
		if (strpos($js, ".js"))
			echo '<script language="javascript" src="'.URL.'assets/js/'.$js.'"></script>';
		else
			echo '<script language="javascript">'.carregar_arquivo("assets/js/".$js).'</script>';
	}
	
	function link_to($rotulo, $url, $atributos = ""){
		if (strpos($_SERVER["REQUEST_URI"], "admin.php"))
			return '<a href="'.url_para_admin($url).'" '.$atributos.'>'.$rotulo.'</a>';
		else
			return '<a href="'.url_para($url).'" '.$atributos.'>'.$rotulo.'</a>';
	}
	
	function button_to($label, $url, $atributos = array()){
		$atributos["value"] = $label;
		if (strpos($_SERVER["REQUEST_URI"], "admin.php"))
			$atributos["onclick"] = isset($atributos["onclick"]) ? $atributos["onclick"] : "window.location.replace('".url_para_admin($url)."')";
		else
			$atributos["onclick"] = isset($atributos["onclick"]) ? $atributos["onclick"] : "window.location.replace('".url_para($url)."')";
		return button($atributos);
	}
	
	//gera um breadcrumb de acordo com o array passado
    function breadcrumb($itens){
		$conteudo = '<ul class="breadcrumb"><li><a href="'.url_para("").'">Início</a></li>';
		foreach ($itens as $link => $rotulo)
			$conteudo .= vazio($link) || is_numeric($link) ? '<li>'.$rotulo.'</li>' : '<li><a href="'.$link.'">'.$rotulo.'</a></li>';
		$conteudo .= '</ul>';
		return $conteudo;
    }
	
	//gera um botão para copiar o conteudo do elemento para a área de transferência
	function copiar($elemento){
		$conteudo = "
		<object width=\"14\" height=\"14\" class=\"clippy\" >
		  <param name=\"movie\" value=\"".URL."images/clippy.swf\"/>
		  <param name=\"allowScriptAccess\" value=\"always\" />
		  <param name=\"quality\" value=\"high\" />
		  <param name=\"scale\" value=\"noscale\" />
		  <param NAME=\"FlashVars\" value=\"id=".$elemento."&amp;copied=copiado!&amp;copyto=copiar\">
		  <param name=\"bgcolor\" value=\"#FFFFFF\">
		  <param name=\"wmode\" value=\"opaque\">
		  <embed src=\"".URL."admin/images/clippy.swf\"
				 width=\"14\"
				 height=\"14\"
				 name=\"clippy\"
				 quality=\"high\"
				 allowScriptAccess=\"always\"
				 type=\"application/x-shockwave-flash\"
				 pluginspage=\"http://www.macromedia.com/go/getflashplayer\"
				 FlashVars=\"id=".$elemento."&amp;copied=copiado!&amp;copyto=copiar\"
				 bgcolor=\"#FFFFFF\"
				 wmode=\"opaque\"
		  />
		</object>
		";
		return $conteudo;
	}
	
	function ajuda($mensagem, $atributos = ""){
		return img("help", 'class="tipTip" title="'.$mensagem.'" '.$atributos);
    }
?>