<?php
#########################################################
### funcoes genericas para manipular o banco de dados ###
#########################################################
     
    //trata uma variavel para previnir sql injection, e já deixa pronta para
    //ser usada no banco, codificando e removendo espaços desnecessários
    function x($str, $valores_monetarios = array()){
		if (is_array($str))
			return x_array($str, $valores_monetarios);
		else{
			if (!is_numeric($str)){
				$str = get_magic_quotes_gpc() ? stripslashes($str) : $str;
				$str = function_exists('mysql_real_escape_string') ? mysql_real_escape_string($str) : mysql_escape_string($str);
			}
			return utf8_decode(trim($str));
		}
	}
	
	//converte a string do banco de volta pra codificação correta
	function y($str){
		return utf8_encode($str);
	}
     
    //returna o último registro de uma tabela, em forma de array
    function ultimo_registro($tabela){
        return fetch(query("SELECT * FROM ".x($tabela)." ORDER BY id DESC LIMIT 1"));
    }
     
    //retorna o registro com id igual a $id na tabela $tabela
    function find($tabela, $id){
        return fetch(query("SELECT * FROM ".x($tabela)." WHERE id = '".x($id)."'"));
    }
	
	function query($sql){
		$query = mysql_query($sql);
		if (!vazio(mysql_error()))
			trigger_error("MYSQL QUERY: | ".$sql." | ERROR: ".mysql_error());
		return $query;
	}
	
	function fetch($query){
		if (gettype($query) == "string")
			$query = query($query);
		return mysql_fetch_array($query);
	}
	
	function rows($query){
		return mysql_num_rows($query);
	}
     
    //retorna uma cópia de um registro passado
    function copiar_registro($registro, $remover_id = true){
        $novo = $registro;
        if ($remover_id)
            unset($novo["id"]);
        foreach ($novo as $chave => $valor)
            if (is_numeric($chave))
                unset($novo[$chave]);
        unset($novo["criado_em"]);
        unset($novo["criado_por"]);
        unset($novo["atualizado_em"]);
        unset($novo["atualizado_por"]);
        return $novo;
    }
     
    function executar_insert($tabela, $hash_valores){
        $sql = "INSERT INTO ".x($tabela)." (";
        foreach ($hash_valores as $campo => $valor){
            $campos[] = $campo;
			if (is_null($valor))
				$valores = "NULL";
			else
	            $valores[] = "'".$valor."'";
        }
        //gerando os valores dos campos criado_em, criado_por e fabrica_id
        if (!in_array("criado_em", $campos)){
			if (in_array("criado_em", fields_of($tabela))){
                $campos[] = "criado_em";
                $valores[] = "'".now()."'";
            }
        }
        /*if (!in_array("criado_por", $campos)){
            $coluna = collection("information_schema.COLUMNS", array("TABLE_SCHEMA" => DB_NAME, "TABLE_NAME" => x($tabela), "COLUMN_NAME" => "criado_por"));
            if (mysql_num_rows($coluna) > 0){
                //$campos[] = "criado_por";
                //$valores[] = "'".$_SESSION["solution_user_id"]."'";
            }
        }*/
        $sql .= implode(", ", $campos).") VALUES (".implode(", ", $valores).")";
        if (query($sql))
            return ultimo_registro($tabela);
        else
            return false;
    }
     
    function executar_delete($tabela, $condicoes){
        $sql = "DELETE FROM ".$tabela;
        if (is_array($condicoes)){
            foreach ($condicoes as $condicao => $valor)
                $where[] = $condicao." = ".$valor;
            if (sizeof($where) > 0)
                $sql .= " WHERE ".implode(" AND ", $where);
            else
                $sql .= " WHERE 0 = 1 ";
        }
        else
            $sql .= " WHERE id = '".x($condicoes)."'";
        return query($sql);
    }
     
    function executar_update($tabela, $hash_valores, $where){
        $sql = "UPDATE ".x($tabela)." SET ";
        foreach ($hash_valores as $campo => $valor){
			if (is_null($valor))
				$valores[] = $campo."=NULL";
			else
	            $valores[] = $campo."='".$valor."'";
            $campos[] = $campo;
        }
        //gerando os valores dos campos atualizado_em e atualizado_por
        if (!in_array("atualizado_em", $campos)){
			if (in_array("atualizado_em", fields_of($tabela)))
                $valores[] = "atualizado_em = '".now()."'";
        }
        /*if (!in_array("atualizado_por", $campos)){
            $coluna = mysql_query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '".DB_NAME."' AND TABLE_NAME = '".x($tabela)."' AND COLUMN_NAME = 'atualizado_por'");
            if (mysql_num_rows($coluna) > 0)
                $valores[] = "atualizado_por = '".$_SESSION["solution_user_id"]."'";
        }*/
        $sql .= implode(", ", $valores)." WHERE ".$where;
		return query($sql);
    }
    
	#retorna uma mysql_query de acordo com o parâmetros passados.
	#as condições podem ser passadas como array("campo" => "valor") ou campo='valor'
    function collection($tabela, $condicoes = array(), $order = ""){
        $sql = "SELECT * FROM ".x($tabela);
		if (gettype($condicoes) == "array"){
			if (sizeof($condicoes) > 0 || $condicoes != ""){
				foreach ($condicoes as $condicao => $valor){
					if (is_null($valor))
						$where[] = $condicao." IS NULL";
					else
						$where[] = $condicao." = '".$valor."'";
				}
				if (sizeof($where) > 0)
					$sql .= " WHERE ".implode(" AND ", $where);
			}
		}
		elseif (!vazio($condicoes))
			$sql .= " WHERE ".$condicoes;
        if (!vazio($order))
            $sql .= " ORDER BY ".$order;
        return query($sql);
    }
	
	##retorna um array contendo os campos da tabela
	function fields_of($tabela){
		$fields = collection("information_schema.COLUMNS", array("TABLE_SCHEMA" => DB_NAME, "TABLE_NAME" => $tabela));
		$list = array();
		while ($field = fetch($fields))
			$list[] = $field["COLUMN_NAME"];
		return $list;
	}
?>