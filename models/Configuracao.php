<?php
	class Configuracao extends Table{
		const TABLENAME = "configuracoes";
		
		static function config($atributo = ""){
			$config = self::first();
			if (vazio($atributo))
				return $config;
			else
				return $config[$atributo];
		}
	}
?>