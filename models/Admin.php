<?php
	class Admin extends Table{
		const TABLENAME = "admins";
		static $has_many = array("permissoes" => array("through" => "AdminPermissao", "field" => "admin_id", "foreign_key" => "permissao_id", "class" => "Permissao"));
		
		static function logado(){
            return self::find($_SESSION["admin_id"]);
        }
         
        static function findByCredenciais($login, $senha){
            return fetch(self::collection(array("login" => $login, "senha" => md5($senha))));
        }
         
        static function validarSessao($permissao = array()){
            if (gettype($permissao) != "array")
                $permissao = array($permissao);
            if (!self::logado()){
                header("location: ".URL."admin.php/login");
                exit;
            }
            elseif (!empty($permissao)){
                if (!self::autorizadoPara($permissao)){
                    incluir("403_denied.php");
                    exit;
                }
            }
        }
		
		static function remover($id){
			$permissoes = self::permissoes($id);
			while ($permissao = fetch($permissoes))
				self::removerPermissao($id, $permissao["id"]);
			return executar_delete(self::TABLENAME, $id);
		}
         
        static function autorizadoPara($secoes){
            $admin = self::logado();
            if (gettype($secoes) != "array")
                $secoes = array($secoes);
            if (!$admin)
                return false;
            else{
                $autorizado = false;
                foreach ($secoes as $secao){
                    $permissao = fetch(query("SELECT * FROM admins a, permissoes p, admins_permissoes ap WHERE ap.admin_id = a.id AND ap.permissao_id = p.id AND a.id = '".$admin["id"]."' AND (p.id = '".$secao."' OR p.nome = '".$secao."' OR p.identificador = '".$secao."')"));
                    if ($permissao)
                        $autorizado = true;
                    else
                        $autorizado = fetch(Admin::collection(array("id" => $admin["id"], "super" => "1")));
                    if ($autorizado)
                        return true;
                }
            }
            return false;
        }

		static function permissoes($id){
            return query("SELECT p.* FROM permissoes p, admins_permissoes up WHERE p.id = up.permissao_id AND up.admin_id='".$id."'");
		}
		
		static function adicionarPermissao($admin_id, $permissao_id){
			if (vazio(AdminPermissao::collection(array("admin_id" => $admin_id, "permissao_id" => $permissao_id))))
	            return AdminPermissao::criar(array("admin_id" => $admin_id, "permissao_id" => $permissao_id));
			else
				return false;
        }
         
        static function removerPermissao($admin_id, $permissao_id){
			$permissao = fetch(AdminPermissao::collection(array("admin_id" => $admin_id, "permissao_id" => $permissao_id)));
			return AdminPermissao::remover($permissao["id"]);
        }
	}
?>