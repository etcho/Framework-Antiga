<?php
###########################################
### funções para geração de formulários ###
###########################################

	//inicializa a tag <form>
	function form($options = array()){
		$options["method"] = isset($options["method"]) ? $options["method"] : "post";
		if (isset($options["values"]))
			$_GET["form_values"] = $options["values"];
		if (isset($options["alinhamento"]))
			$_GET["form_alinhamento"] = $options["alinhamento"];
		$conteudo = '<form';
        foreach ($options as $atributo => $valor)
            if (!in_array($atributo, array("alinhamento", "values")))
                $conteudo .= ' '.$atributo.'="'.$valor.'"';
        $conteudo .= '>';
		return $conteudo;
	}
	
	//fecha a tag <form>
	function end_form(){
		unset($_GET["form_values"]);
		unset($_GET["form_alinhamento"]);
		return '</form>';
	}

	//gera a estrutura da tabela para qualquer campo do form
	function commom_field($options = array()){
		$options["alinhamento"] = !isset($options["alinhamento"]) ? (isset($_GET["form_alinhamento"]) ? $_GET["form_alinhamento"] : "left") : $options["alinhamento"];
		$options["label"] = !isset($options["label"]) ? ucwords($options["name"]) : $options["label"];
		$options["obrigatorio"] = !isset($options["obrigatorio"]) ? false : $options["obrigatorio"];
		$conteudo = '<tr>';
		$conteudo .= '<td class="nowrap" align="'.$options["alinhamento"].'"><label for="'.$options["id"].'">'.$options["label"].'</label>'.($options["obrigatorio"] ? campo_obrigatorio() : "").'</td>';
		$conteudo .= '<td>';
		$conteudo .= $options["conteudo"];
		$conteudo .= '</td></tr>';
		return $conteudo;
	}

	//gera um <input type="text"> padrão
	function text_field($options = array()){
		$options["size"] = !isset($options["size"]) ? 60 : $options["size"];
		$options["type"] = !isset($options["type"]) ? "text" : $options["type"];
		$options["id"] = !isset($options["id"]) ? $options["name"] : $options["id"];
		$options["value"] = !isset($options["value"]) ? (isset($_GET["form_values"][$options["name"]]) ? $_GET["form_values"][$options["name"]] : "") : $options["value"];
		$conteudo = '<input';
		foreach ($options as $atributo => $valor)
			if (!in_array($atributo, array("alinhamento", "label", "obrigatorio", "calendario", "script")))
				$conteudo .= ' '.$atributo.'="'.$valor.'"';
		$conteudo .= '>';
		if ($options["calendario"])
			$conteudo .= " ".calendar_for($options["id"]);
		if (!vazio($options["script"]))
			$conteudo .= "<script> ".$options["script"]." </script>";
		$options["conteudo"] = $conteudo;
		return commom_field($options);
	}
	
	//gera um <input type="password"> padrão
	function password_field($options = array()){
		$options["type"] = "password";
		return text_field($options);
	}
	
	//gera um campo padrão para data, com possível calendário se passado como parâmetro
	function date_field($options = array()){
		$options["type"] = "text";
		$options["size"] = !isset($options["size"]) ? 7 : $options["size"];
		$options["maxlength"] = !isset($options["maxlength"]) ? 10 : $options["maxlength"];
		$options["onKeyUp"] = !isset($options["onKeyUp"]) ? "formata_data(this)" : $options["onKeyUp"];
		$options["onBlur"] = !isset($options["onBlur"]) ? "formata_data(this)" : $options["onBlur"];
		return text_field($options);
	}
	
	//gera um campo padrão para telefone
	function telefone_field($options = array()) {
		$options["type"] = "text";
		$options["size"] = !isset($options["size"]) ? 15 : $options["size"];
		$options["maxlength"] = !isset($options["maxlength"]) ? 15 : $options["maxlength"];
		$options["onKeyUp"] = !isset($options["onKeyUp"]) ? "formata_telefone(this)" : $options["onKeyUp"];
		$options["onBlur"] = !isset($options["onBlur"]) ? "formata_telefone(this)" : $options["onBlur"];
		return text_field($options);
	}
	
	function cnpj_field($options = array()) {
		$options["type"] = "text";
		$options["size"] = !isset($options["size"]) ? 20 : $options["size"];
		$options["maxlength"] = !isset($options["maxlength"]) ? 18 : $options["maxlength"];
		$options["onKeyUp"] = !isset($options["onKeyUp"]) ? "formata_cnpj(this)" : $options["onKeyUp"];
		$options["onBlur"] = !isset($options["onBlur"]) ? "formata_cnpj(this)" : $options["onBlur"];
		return text_field($options);
	}
	
	function cpf_field($options = array()) {
		$options["type"] = "text";
		$options["size"] = !isset($options["size"]) ? 15 : $options["size"];
		$options["maxlength"] = !isset($options["maxlength"]) ? 14 : $options["maxlength"];
		$options["onKeyUp"] = !isset($options["onKeyUp"]) ? "formata_cpf(this)" : $options["onKeyUp"];
		$options["onBlur"] = !isset($options["onBlur"]) ? "formata_cpf(this)" : $options["onBlur"];
		return text_field($options);
	}
	
	function cep_field($options = array()) {
		$options["type"] = "text";
		$options["size"] = !isset($options["size"]) ? 15 : $options["size"];
		$options["script"] = !isset($options["script"]) ? "$('input[name=\"".$options["name"]."\"]').mask('99999-999')" : $options["script"];
		return text_field($options);
	}
	
	function integer_field($options = array()) {
		$options["type"] = "text";
		$options["size"] = !isset($options["size"]) ? 15 : $options["size"];
		$options["onKeyUp"] = !isset($options["onKeyUp"]) ? "somente_numero(this)" : $options["onKeyUp"];
		$options["onBlur"] = !isset($options["onBlur"]) ? "somente_numero(this)" : $options["onBlur"];
		return text_field($options);
	}
	
	//gera um <textarea> padrão
	function textarea($options = array()){
		if (isset($options["size"]))
			list($options["cols"], $options["rows"]) = explode("x", $options["size"]);
		else{
			$options["cols"] = !isset($options["cols"]) ? 60 : $options["cols"];
			$options["rows"] = !isset($options["rows"]) ? 6 : $options["rows"];
		}
		$options["id"] = !isset($options["id"]) ? $options["name"] : $options["id"];
		$options["value"] = !isset($options["value"]) ? (isset($_GET["form_values"][$options["name"]]) ? $_GET["form_values"][$options["name"]] : "") : $options["value"];
		$conteudo = '<textarea';
		foreach ($options as $atributo => $valor)
			if (!in_array($atributo, array("alinhamento", "label", "obrigatorio", "size")))
				$conteudo .= ' '.$atributo.'="'.$valor.'"';
		$conteudo .= '>'.$options["value"].'</textarea>';
		$options["conteudo"] = $conteudo;
		return commom_field($options);
	}
	
	//gera um ícone que ao ser clicado abre um calendário flutuante para que possa ser escolhida a data e
    //automaticamente é enviada pra o campo de texto passado em $id_elemento
    function calendar_for($id_elemento, $mascara = "dd/mm/yyyy"){
		return img("calendar", 'style="cursor: pointer; vertical-align: middle; margin-top: -3px;" title="Escolher data" onclick="displayCalendar(document.getElementById(\''.$id_elemento.'\'), \''.$mascara.'\', this)"');
    }
	
	//gera um <input type="button">
	function button($options = array()){
		$options["class"] = !isset($options["class"]) ? "blue_button" : $options["class"];
		$options["type"] = !isset($options["type"]) ? "button" : $options["type"];
		$conteudo = '<input';
		foreach ($options as $atributo => $valor)
			$conteudo .= ' '.$atributo.'="'.$valor.'"';
		$conteudo .= '>';
		return $conteudo;
	}
	
	//gera um <input type="submit">
	function submit($options = array()){
		if (gettype($options) != "array")
			$options = array("value" => $options);
		$options["type"] = "submit";
		return button($options);
	}
	
	//gera um select de acordo com as options e parâmetros passados
	function select($params = array()){
		$params["id"] = !isset($params["id"]) ? $params["name"] : $params["id"];
		$conteudo = '<select';
		foreach ($params as $atributo => $valor)
			if (!in_array($atributo, array("options", "alinhamento", "label", "obrigatorio")))
				$conteudo .= ' '.$atributo.'="'.$valor.'"';
		$conteudo .= '>'.$params["options"].'</select>';
		$params["conteudo"] = $conteudo;
		return commom_field($params);
	}
	
	//gera um conjunto de options a partir de um array
    function options_for_select($options, $selecionado = "", $prompt = false){
		if ($prompt != false){
			if (gettype($prompt) == "boolean")
				$conteudo = array(options_for_select(array("" => "-- Selecione --")));
			else
				$conteudo = array(options_for_select(array("" => $prompt)));
		}
        foreach ($options as $valor => $texto)
            $conteudo[] = '<option value="'.$valor.'"'.($valor == $selecionado ? " selected" : "").'>'.$texto.'</option>';
        return implode("", $conteudo);
    }
	
	//gera um conjunto de options a partir de uma mysql_query
	function options_for_select_from_collection($collection, $value, $label, $selecionado = "", $prompt = true){
		if (gettype($collection) == "array")
			foreach ($collection as $registro)
				$options[$registro[$value]] = utf8_encode($registro[$label]);
		else
			while ($registro = fetch($collection))
				$options[$registro[$value]] = utf8_encode($registro[$label]);
		return options_for_select($options, $selecionado, $prompt);
	}
?>