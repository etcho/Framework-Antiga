<?php
####################################################
### funcoes genericas para exibicao de mensagens ###
####################################################
     
    //exibe uma div colorida, da largura da página, com o texto passado e cores de acordo com
    //o tipo passado, que podem ser "error"(vermelho), "warning"(amarelo) ou "ok"(verde)
    function mensagem_alerta($tipo, $mensagem){
        $id = "alerta_".md5(uniqid(time()));
        $bgcolor = $tipo == "error" ? "#FFDDDD" : ($tipo == "warning" ? "#FFFFEE" : ($tipo == "obs" ? "#FFFFFF" : "#EEFFEE"));
        $border = $tipo == "error" ? "#990000" : ($tipo == "warning" ? "#444400" : ($tipo == "obs" ? "#FFFFFF" : "#033300"));
        $imagem = $tipo == "error" ? "alert_error.png" : ($tipo == "warning" ? "alert_warning.png" : ($tipo == "obs" ? "blanc.png" : "alert_ok.png"));
        $conteudo = "
        <div class=\"noprint\" style=\"border: 1px solid ".$border."; padding-top: 5px; padding-bottom: 5px; background: ".$bgcolor."; font-family: Verdana, Geneva, sans-serif; font-size: 12px; margin-bottom: 5px; width: 95%; border-radius: 5px; -webkit-border-radius: 5px; -moz-border-radius: 5px; box-shadow: 1px 1px 3px #999; -webkit-box-shadow: 1px 1px 3px #999; -moz-box-shadow: 1px 1px 3px #999;\" id=\"".$id."\">
            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
                <tr>
                    <td width=\"30\" style=\"padding-left: 10px; vertical-align: middle; border-style: none;\">
                        <img src=\"".URL."assets/images/".$imagem."\" />
                    </td>
                    <td style='border-style: none; font-size: 13px;'>
                        ".$mensagem."
                    </td>
                    <td align=\"center\" width=\"30\" style='border-style: none'>
                        <img src=\"".URL."assets/images/bullet_cancel.png\" title=\"Ocultar\" style=\"cursor: pointer\" onclick=\"smart_fade('".$id."')\" />
                    </td>
                </tr>
            </table>
        </div>
        ";
        return $conteudo;
    }
     
    //mostra as mensagens carregadas nas variaveis de sessao para erros ou avisos de sucesso
    function mensagens(){
        $conteudo = "";
        if (!vazio($_SESSION["msg_erro"])){
            $conteudo .= mensagem_alerta("error", $_SESSION["msg_erro"]);
            $_SESSION["msg_erro"] = "";
        }
        if (!vazio($_SESSION["msg_alerta"])){
            $conteudo .= mensagem_alerta("warning", $_SESSION["msg_alerta"]);
            $_SESSION["msg_alerta"] = "";
        }
        if (!vazio($_SESSION["msg_sucesso"])){
            $conteudo .= mensagem_alerta("ok", $_SESSION["msg_sucesso"]);
            $_SESSION["msg_sucesso"] = "";
        }
        return $conteudo;
    }
	
	//trata os erros provenientes de validações de formulários
	function erros_validacao($erros){
		$saida = array();
		foreach ($erros as $campo => $erro){
			if (gettype($erro) != "array")
				$erro = array($erro);
			foreach ($erro as $tipo){
				switch ($tipo){
					case "vazio": $saida[] = ucwords($campo)." deve ser informado"; break;
					case "vazia": $saida[] = ucwords($campo)." deve ser informada"; break;
					case "invalido": $saida[] = ucwords($campo)." é inválido"; break;
					case "invalida": $saida[] = ucwords($campo)." é inválida"; break;
					case "indisponivel" : $saida[] = ucwords($campo)." não está disponível"; break;
					default: $saida[] = $tipo;
				}
			}
		}
		return $saida;
	}
	
	function cep_valido($cep){
		if (vazio($cep))
			return false;
		elseif (strlen($cep) != 9)
			return false;
		elseif (!is_numeric(substr($cep, 0, 5)))
			return false;
		elseif (substr($cep, 5, 1) != "-")
			return false;
		elseif (!is_numeric(substr($cep, 6, 3)))
			return false;
		return true;
	}
	
	//retorna true se a string passada for vazia. tambem vale para textarea somente com enters
    function vazio($string){
        switch (gettype($string)){
            case "resource": return rows($string) == 0; break;
            case "array": return empty($string); break;
            default: return strlen(trim(str_replace("\n", "", $string))) == 0; break;
        }
    }
	
	//verifica se determinada string é vazia e retorna o valor da variável $se_vazio
    function tratar($string, $se_vazio = "-"){
        if (strlen(trim(str_replace("\n", "", $string))) == 0)
            return $se_vazio;
        else
            return trim($string);
    }
     
    //retorna um vetor com todos os elementos passando pela funcao utf8_encode, convertendo datas
	//para o padrão br e formatando os valores monetários
    function utf8_encode_array($array, $valores_monetarios = array()){
        $array = gettype($array) == "array" ? $array : array($array);
		foreach ($array as $chave => $valor){
			if (data_valida($valor, "bd"))
				$array[$chave] = data_bd_to_br($valor);
			elseif (in_array($chave, $valores_monetarios))
				$array[$chave] = moeda($valor);
			elseif (is_array($valor))
				$array[$chave] = utf8_encode_array($valor);
			else
	            $array[$chave] = utf8_encode($valor);
		}
        return $array;
    }
	
	function x_array($array, $valores_monetarios = array()){
        foreach ($array as $chave => $valor){
			if (data_valida($valor, "br"))
				$array[$chave] = data_br_to_bd($valor);
			elseif (in_array($chave, $valores_monetarios))
				$array[$chave] = desformatar_moeda($valor);
			elseif (is_array($valor))
				$array[$chave] = x_array($valor);
			else
	            $array[$chave] = x($valor);
		}
        return $array;
    }
	
	function y_array($array, $valores_monetarios = array()){
		return utf8_encode_array($array, $valores_monetarios);
	}
	 
    function formatar($string, $tipo){
        switch ($tipo){
        case "cpf":
            if (strlen($string) != 11)
                return $string;
            else
                return substr($string, 0, 3).".".substr($string, 3, 3).".".substr($string, 6, 3)."-".substr($string, 9, 2);
            break;  
        case "cnpj":
            if (strlen($string) != 14)
                return $string;
            else
                return substr($string, 0, 2).".".substr($string, 2, 3).".".substr($string, 5, 3)."/".substr($string, 8, 4)."-".substr($string, 12, 2);
            break;
        case "cep":
            if (strlen($string) != 8)
                return $string;
            else
                return substr($string, 0, 5)."-".substr($string, 5, 3);
            break;
        case "telefone":
            if (strlen($string) != 10)
                return $string;
            else
                return "(".substr($string, 0, 2).") ".substr($string, 2, 4)."-".substr($string, 6, 4);
            break;
        }
    }
	
	//formata um valor como moeda com vírgula e pontos corretamente
    function moeda($valor){
        return number_format($valor, 2, ",", ".");
    }
	
	function desformatar_moeda($valor){
		return str_replace(",", ".", str_replace(".", "", $valor));
	}
	
	function map($array, $chave){
		return array_map(create_function('$array', 'return $array["'.$chave.'"];'), $array);
	}
	
	function validar_recaptcha($captcha = "default", $response = "default"){
		$captcha = $captcha == "default" ? $_POST["recaptcha_challenge_field"] : $captcha;
		$response = $response == "default" ? $_POST["recaptcha_response_field"] : $response;
		$captcha = recaptcha_check_answer($_GET["privatekey"], $_SERVER["REMOTE_ADDR"], $captcha, $response);
        return $captcha -> is_valid;
	}
	
	function numero_palavras($string){
		$explode = explode(" ", trim($string));
		$i = 0;
		foreach ($explode as $palavra)
			if (!vazio($palavra))
				$i++;
		return $i;
	}
	
	function array_to_json($array){
		$str = '{';
		$tuplas = array();
		foreach ($array as $key => $value){
			$tuplas[] = '"'.$key.'": ';
			if (gettype($value) == "array")
				$tuplas[count($tuplas) - 1] .= array_to_json($value);
			else
				$tuplas[count($tuplas) - 1] .= '"'.$value.'"';
		}
		$str .= implode(", ", $tuplas);
		$str .= '}';
		return $str;
	}
	
	function remover_chaves_numericas($array, $remover_sub_arrays = false){
		$novo = array();
		foreach ($array as $key => $value)
			if (!is_numeric($key))
				if ((gettype($value) != "array") || (gettype($value) == "array" && !$remover_sub_arrays))
					$novo[$key] = $value;
		return $novo;
	}
?>