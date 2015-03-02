<?php
	class Permissao extends Table{
		const TABLENAME = "permissoes";
		static $has_many = array("admins" => array("through" => "AdminPermissao", "field" => "permissao_id", "foreign_key" => "admin_id", "class" => "Admin"));
	}
?>