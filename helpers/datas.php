<?php
###################################################
### funcoes genericas para manipulacao de datas ###
###################################################

    //transforma uma data do tipo dd/mm/aaaa para o tipo aaaa-mm-dd
    function data_br_to_bd($data){
        return substr($data, 6, 4)."-".substr($data, 3, 2)."-".substr($data, 0, 2);
    }
     
    //transforma uma data do tipo aaaa-mm-dd para o tipo dd/mm/aaaa
    function data_bd_to_br($data){
        return substr($data, 8, 2)."/".substr($data, 5, 2)."/".substr($data, 0, 4);
    }
     
    //retorna hora no formato hh:mm de uma datetime
    function hora_from_datetime($data){
        return substr($data, 11, 5);
    }
     
    //converte algo do tipo "2012-03-22 11:14:30" para "22/03/2012 às 11:14"
    function datetime_to_br($datetime){
        return data_bd_to_br($datetime)." às ".hora_from_datetime($datetime);
    }
     
    //retorna a data de hoje, no padrao do banco de dados ou br
    function hoje($padrao = "bd"){
        $conteudo = $padrao == "bd" ? date("Y-m-d") : date("d/m/Y");
        return $conteudo;
    }
     
    //retorna a hora atual
    function agora(){
        return date("H:i:s");
    }
     
    //returna o dia e hora atual no padrao datetime
    function now(){
        return date("Y-m-d")." ".date("H:i:s");
    }
     
    //retorna true se a $data1 for maior que a $data2. as datas devem estar no padrão aaaa-mm-dd
    function data1_maior($data1, $data2){
        if (str_replace("-", "", $data1) > str_replace("-", "", $data2))
            return true;
        else
            return false;
    }
     
    //verifica se a data é válida
    function data_valida($data, $padrao = "br"){
        if ($padrao == "br"){
            $data = explode("/", $data);
            return checkdate($data[1]+0, $data[0]+0, $data[2]+0) == 1;
        }
        else{
            $data = explode("-", $data);
            return checkdate($data[1]+0, $data[2]+0, $data[0]+0) == 1;
        }
    }
     
    //retorna algo do tipo '10 minutos atrás' em relação ao datetime passado
    function tempo_relativo($datetime){
        $timestamp = mktime(substr($datetime, 11, 2), substr($datetime, 14, 2), substr($datetime, 17, 2), substr($datetime, 5, 2), substr($datetime, 8, 2), substr($datetime, 0, 4));
        $segundos = strtotime("+0 minutes") - $timestamp;
        if ($segundos < 60) // < 1 minuto
            $retorno = $segundos == 1 ? "1 segundo" : $segundos." segundos";
        elseif ($segundos < 3600){ // < 1 hora
            $minutos = floor($segundos / 60);
            $retorno = $minutos == 1 ? "1 minuto" : $minutos." minutos";
        }
        elseif ($segundos < 86400){ // < 1 dia
            $horas = floor($segundos / 60 / 60);
            $retorno = $horas == 1 ? "1 hora" : $horas." horas";
        }
        elseif ($segundos < 2592000){ // < 1 mes
            $dias = floor($segundos / 60 / 60 / 24);
            $retorno = $dias == 1 ? "1 dia" : $dias." dias";
        }
        elseif ($segundos < 31536000){ // < 1 ano
            $meses = floor($segundos / 60 / 60 / 24 / 30);
            $retorno = $meses == 1 ? "1 mês" : $meses." meses";
        }
        else{
            $anos = floor($segundos / 60 / 60 / 24 / 30 / 12);
            $retorno = $anos == 1 ? "1 ano" : $anos." anos";
        }
        return $retorno." atrás";
    }
     
    //converte 3 para Março
    function mes_extenso($mes){
        $meses = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
        return $meses[$mes-1];
    }
     
    function addDayIntoDate($date,$days) {
        $thisyear = substr ( $date, 0, 4 );
        $thismonth = substr ( $date, 5, 2 );
        $thisday =  substr ( $date, 8, 2 );
        $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday + $days, $thisyear );
        return strftime("%Y-%m-%d", $nextdate);
    }
 
    function subDayIntoDate($date,$days) {
        $thisyear = substr ( $date, 0, 4 );
        $thismonth = substr ( $date, 5, 2 );
        $thisday =  substr ( $date, 8, 2 );
        $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday - $days, $thisyear );
        return strftime("%Y-%m-%d", $nextdate);
    }
     
    //retorna o dia da semana relativo a uma data
    function dia_semana($data, $padrao = "bd"){
        if ($padrao == "br")
            $data = data_br_to_bd($data);
        $dias = array("domingo", "segunda", "terça", "quarta", "quinta", "sexta", "sábado");
        $w = date("w", mktime(0, 0, 0, substr($data, 5, 2), substr($data, 8, 2), substr(0, 4)));
        $w = $w == 6 ? 0 : $w + 1;
        return $dias[$w];
    }
	
	function dias_entre_datas($data1, $data2){
		$time_inicial = strtotime($data1);
		$time_final = strtotime($data2);
		$diferenca = $time_final - $time_inicial;
		$dias = (int)floor( $diferenca / (60 * 60 * 24));
		return $dias;
	}
	
	//executa um strtotime sobre a data passada, por exemplo '+3 years'
	function operacao_data($data, $operacao){
		$timestamp = strtotime($data);
		return date("Y-m-d", strtotime($operacao, $timestamp));
	}
?>