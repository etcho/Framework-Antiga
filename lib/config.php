<?php
	//fazendo o redirecionamento caso o www esteja omitido na url
	if ($_SERVER["SERVER_NAME"] == "dominio.com.br"){
		header("location: http://www.dominio.com.br".$_SERVER["REQUEST_URI"]);
		exit;
	}

	session_start();
	
	###############
	### globais ###
	###############

	define(DB_HOST, "");
	define(DB_USER, "");
	define(DB_PASSWORD, "");
	define(DB_NAME, "");
    define(URL, "http://www.dominio.com.br/site/");
	
	###########################
	### conexao com o banco ###
	###########################

    mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Não foi possível conectar-se com o banco de dados");
    mysql_select_db(DB_NAME)or die("Não foi possível conectar-se com o banco de dados");

	#########################################################
	### carregando as funções auxiliares da pasta helpers ###
	#########################################################

	require_once("lib/mobile_detect/MobileDetect.php");
	if (file_exists("helpers")){
		$diretorio = dir("helpers/");
		while ($arquivo = $diretorio -> read())
			if (file_exists("helpers/".$arquivo) && $arquivo != "." && $arquivo != "..")
				require_once("helpers/".$arquivo);
	}

	#############################
    ### carregando os modelos ###
    #############################
 
    require_once("lib/Table.class.php");
    carregar_arquivos_do_diretorio("models");
     
    #################################
    ### carregando os controladores ###
    #################################
     
    carregar_arquivos_do_diretorio("controllers");
?>