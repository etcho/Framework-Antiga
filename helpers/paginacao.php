<?php
	function usar_paginacao($resultados_por_pagina = 10, $tamanho_paginacao = 2){
		define(TAMANHO_PAGINACAO, $tamanho_paginacao);
    	define(RESULTADOS_POR_PAGINA, $resultados_por_pagina);
		$_GET["pagina"] = $_GET["pagina"] > 0 ? $_GET["pagina"] : 1;
	}
	
	function paginacao_limit(){
	    return " LIMIT ".(($_GET["pagina"]-1) * RESULTADOS_POR_PAGINA ).",".RESULTADOS_POR_PAGINA;
	}
	
	function gerar_paginacao($total_resultados, $pagina, $url, $parametros = ""){
        $paginacao = "";
        if ($total_resultados > RESULTADOS_POR_PAGINA){
            if ($pagina > TAMANHO_PAGINACAO+1)
                $paginacao .= "<a class=\"paginacao_setas\" href=\"".url_para($url."/?pagina=1".$parametros)."\">&lsaquo;</a>";
            for ($pag=1; $pag<=ceil($total_resultados / RESULTADOS_POR_PAGINA); $pag++){
                if ($pag >= $pagina-TAMANHO_PAGINACAO && $pag <= $pagina+TAMANHO_PAGINACAO)
                    if ($pag == $pagina)
                        $paginacao .= "<span class=\"paginacao_selecionada\">".$pag."</span>";
                    else
                        $paginacao .= "<a class=\"paginacao\" href=\"".url_para($url."/?pagina=".$pag.$parametros)."\">".$pag."</a>";
            }
            if ($pagina < ceil($total_resultados / RESULTADOS_POR_PAGINA))
                $paginacao .= "<a class=\"paginacao_setas\" href=\"".url_para($url."/?pagina=".(ceil($total_resultados / RESULTADOS_POR_PAGINA)).$parametros)."\">&rsaquo;</a>";
            $paginacao = "<div align=\"center\" style=\"margin-top: 10px;\">".$paginacao;
            $paginacao .= "</div>";
        }
        return $paginacao;
    }
?>