<?php
    if(!function_exists('get_called_class')) {
        function get_called_class($bt = false, $l = 1) {
            if (!$bt) $bt = debug_backtrace();
            if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep.");
            if (!isset($bt[$l]['type']))
                throw new Exception ('type not set');
            else switch ($bt[$l]['type']){
                case '::':
                    $lines = file($bt[$l]['file']);
                    $i = 0;
                    $callerLine = '';
                    do{
                        $i++;
                        $callerLine = $lines[$bt[$l]['line']-$i] . $callerLine;
                    } while (stripos($callerLine,$bt[$l]['function']) === false);
                    preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/', $callerLine, $matches);
                    if (!isset($matches[1]))
                        throw new Exception ("Could not find caller class: originating method call is obscured.");
                    switch ($matches[1]){
                        case 'self':
                        case 'parent':
                            return get_called_class($bt,$l+1);
                        default:
                            return $matches[1];
                    }
                case '->': switch ($bt[$l]['function']){
                        case '__get':
                            if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object.");
                            return get_class($bt[$l]['object']);
                        default: return $bt[$l]['class'];
                    }       
                default: throw new Exception ("Unknown backtrace method type");
            }
        }
    }
     
    //função que gera os relacionamentos entre as tabelas, definidos previamente em cada classe
    //através do atributos $belongs_to
    function link_relationships($record, $class, $depth = 1){
        if ($depth <= 5){
            eval('if (isset('.$class.'::$belongs_to)) $belongs_to = '.$class.'::$belongs_to;');
            eval('if (isset('.$class.'::$has_one)) $has_one = '.$class.'::$has_one;');
            if (isset($belongs_to)){
                foreach ($belongs_to as $name => $relation){
                    $field = $relation["field"];
                    $class = $relation["class"];
                    if ($record[$field] > 0){
                        eval('$table = '.$class.'::TABLENAME;');
                        $record[$name] = find($table, $record[$field]);
                        $record[$name] = link_relationships($record[$name], $class, $depth + 1);
                    }
                }
            }
             
            if (isset($has_one)){
                foreach ($has_one as $name => $relation){
                    $field = $relation["field"];
                    $class = $relation["class"];
                    eval('$table = '.$class.'::TABLENAME;');
                    $target_record = fetch(collection($table, array($field => $record["id"])));
                    if ($target_record){
                        $record[$name] = $target_record;
                        $record[$name] = link_relationships($record[$name], $class, $depth + 1);
                    }
                }
            }
        }
        return $record;
    }
 
    abstract class Table{
        const TABLENAME = "TABLE";
         
        //lista de palavras que podem posteriormente ser usadas para melhorar buscas
        static function ignoredWords(){
            return array("a", "o", "de", "da", "do", "em", "e", "para", "por", "que");
        }
         
        static function find($id, $include_relationships = true){
            eval('$tablename = '.get_called_class().'::TABLENAME;');
            $record = find($tablename, $id);
            if ($include_relationships)
                $record = link_relationships($record, get_called_class());
            return $record;
        }
         
        static function findBy($field, $value, $include_relationships = true){
            eval('$tablename = '.get_called_class().'::TABLENAME;');
            $record = fetch(collection($tablename, array($field => $value), $field." LIMIT 1"));
            if ($include_relationships)
                $record = link_relationships($record, get_called_class());
            return $record;
        }
         
        static function findRandom($include_relationships = true){
            eval('$tablename = '.get_called_class().'::TABLENAME;');
            $record = fetch(collection($tablename, array(), "RAND() LIMIT 1"));
            if ($include_relationships)
                $record = link_relationships($record, get_called_class());
            return $record;
        }
         
        static function listOf($id, $relationship, $order = "default_field"){
            $list = array();
            eval('if (isset('.get_called_class().'::$has_many)) $has_many = '.get_called_class().'::$has_many;');
            if (isset($has_many)){
                foreach ($has_many as $name => $relation){
                    if ($name == $relationship){
                        //many to many
                        if (isset($relation["through"])){
                            $field = $relation["field"];
                            $class = $relation["class"];
                            $association_class = $relation["through"];
                            $foreign_key = $relation["foreign_key"];
                            eval('$table = '.$class.'::TABLENAME;');
                            eval('$association_table = '.$association_class.'::TABLENAME;');
                            $records = collection($association_table, array($foreign_key => $id));
                            while ($record = fetch($records)){
                                $list[] = find($table, $record[$field]);
                                //agregando os campos da tabela associativa, quando existirem
                                $fields = fields_of($table);
                                $fields_association = fields_of($association_table);
                                foreach ($fields_association as $f)
                                    if (!in_array($f, $fields))
                                        $list[count($list)-1][$f] = $record[$f];
                            }
                        }
                        //one to many
                        else{
                            $field = $relation["field"];
                            $class = $relation["class"];
                            eval('$table = '.$class.'::TABLENAME;');
                            if ($order == "default_field"){
                                eval('if (isset('.$class.'::$acts_as_list)) $acts_as_list = '.$class.'::$acts_as_list;');
                                if (isset($acts_as_list)){
                                    $order = array();
                                    if (!vazio($acts_as_list["scope"])){
                                        $scope = gettype($acts_as_list["scope"]) == "array" ? $acts_as_list["scope"] : array($acts_as_list["scope"]);
                                        foreach ($scope as $attr)
                                            $order[] = $attr;
                                    }
                                    $order[] = $acts_as_list["field"];
                                    $order = implode(", ", $order);
                                }
                                else
                                    $order = "nome";
                            }
                            if ($order == "nome")
                                $order = in_array($order, fields_of($table)) ? $order : "id";
                            eval('$collection = collection("'.$table.'", array($field => $id), $order);');
                            while ($record = fetch($collection))
                                $list[] = $record;
                        }
                    }
                }
            }
            return $list;
        }
         
        static function fields(){
            eval('$tablename = '.get_called_class().'::TABLENAME;');
            return fields_of($tablename);
        }
         
        static function all($order = "default_field"){
            eval('$tablename = '.get_called_class().'::TABLENAME;');
            if ($order == "default_field"){
                eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
                if (isset($acts_as_list)){
                    $order = array();
                    if (!vazio($acts_as_list["scope"])){
                        $scope = gettype($acts_as_list["scope"]) == "array" ? $acts_as_list["scope"] : array($acts_as_list["scope"]);
                        foreach ($scope as $attr)
                            $order[] = $attr;
                    }
                    $order[] = $acts_as_list["field"];
                    $order = implode(", ", $order);
                }
                else
                    $order = "nome";
            }
            $split = explode(" ", $order);
            if (!in_array(str_replace(",", "", $split[0]), self::fields())){
                $split[0] = "id";
                $order = implode(" ", $split);
            }
            return self::collection(array(), $order);
        }
         
        static function collection($condicoes = array(), $order = "default_field"){
            eval('$tablename = '.get_called_class().'::TABLENAME;');
            if ($order == "default_field"){
                eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
                if (isset($acts_as_list)){
                    $order = array();
                    if (!vazio($acts_as_list["scope"])){
                        $scope = gettype($acts_as_list["scope"]) == "array" ? $acts_as_list["scope"] : array($acts_as_list["scope"]);
                        foreach ($scope as $attr)
                            $order[] = $attr;
                    }
                    $order[] = $acts_as_list["field"];
                    $order = implode(", ", $order);
                }
                else
                    $order = "nome";
            }
            $split = explode(" ", $order);
            if (!in_array(str_replace(",", "", $split[0]), self::fields())){
                $split[0] = "id";
                $order = implode(" ", $split);
            }
            return collection($tablename, $condicoes, $order);
        }
         
        static function count(){
            return rows(self::collection(array(), "id"));
        }
         
        static function first(){
            return fetch(self::collection(array(), "id LIMIT 1"));
        }
         
        static function last(){
            return fetch(self::collection(array(), "id DESC LIMIT 1"));
        }
         
        static function criar($valores){
			eval('$tablename = '.get_called_class().'::TABLENAME;');
			foreach ($valores as $chave => $valor)
				if (gettype($valor) == "array"){
					$valores_has_many[$chave] = $valor;
					unset($valores[$chave]);
				}
			//acertando a posição quando usar acts_as_list
			eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
			if (isset($acts_as_list)){
				$scope = $acts_as_list["scope"];
				if (vazio($scope))
					$ultimo = self::lastPositionOnList();
				else
					$ultimo = self::lastPositionOnList($valores);
				$valores[$acts_as_list["field"]] = $ultimo + 1;
			}
			$objeto_salvo = executar_insert($tablename, $valores);
			
			//salvando os itens de relacionamentos many_to_many
			if ($objeto_salvo){
				$valores_has_many = isset($valores_has_many) ? $valores_has_many : array();
				foreach ($valores_has_many as $chave => $ids){
					eval('if (isset('.get_called_class().'::$has_many)) $has_many = '.get_called_class().'::$has_many;');
					if (isset($has_many)){
						foreach ($has_many as $name => $relation){
							if (isset($relation["through"])){
								if ($name == $chave){
									$field = $relation["field"];
									$class = $relation["class"];
									$association_class = $relation["through"];
									$foreign_key = $relation["foreign_key"];
									eval('$table = '.$class.'::TABLENAME;');
									eval('$association_table = '.$association_class.'::TABLENAME;');
									foreach ($ids as $id){
									   if (gettype($id) == "array"){
										   $fields = array($foreign_key => $objeto_salvo["id"]);
										   foreach ($id as $a_field => $a_value)
										    	if ($a_field != $foreign_key)
													$fields[$a_field] = $a_value;
										   executar_insert($association_table, $fields);
									   }
									   else
											executar_insert($association_table, array($foreign_key => $objeto_salvo["id"], $field => $id));
							   
									}
								}
							}
							else {
								if ($name == $chave){
									$field = $relation["field"];
									$class = $relation["class"];
									eval('$table = '.$class.'::TABLENAME;');
									foreach ($ids as $id){
									   if (gettype($id) == "array"){
										   $fields = array($field => $objeto_salvo["id"]);
										   foreach ($id as $a_field => $a_value)
												$fields[$a_field] = $a_value;
										   executar_insert($table, $fields);
									   }
									}
								}
							}
						}						
					}
					
					eval('if (isset('.get_called_class().'::$has_one)) $has_one = '.get_called_class().'::$has_one;');
					if (isset($has_one)){
						foreach ($has_one as $name => $relation){
							if ($name == $chave){
								$field = $relation["field"];
								$class = $relation["class"];
								eval('$table = '.$class.'::TABLENAME;');
								$values = $ids;
								$values[$field] = $objeto_salvo["id"];
								executar_insert($table, $values);
							}
						}
					}
				}
			}
			return $objeto_salvo;
		}
         
        static function atualizar($valores){
			eval('$tablename = '.get_called_class().'::TABLENAME;');
			foreach ($valores as $chave => $valor)
				if (gettype($valor) == "array"){
					$valores_has_many[$chave] = $valor;
					unset($valores[$chave]);
				}
			$objeto_velho = find($tablename, $valores["id"]);
			$where_velho = self::whereClauseFromScope($objeto_velho["id"]);
			$objeto_salvo = executar_update($tablename, $valores, "id = '".$valores["id"]."'");
			
			//salvando os itens de relacionamentos many_to_many
			if ($objeto_salvo){
				$valores_has_many = isset($valores_has_many) ? $valores_has_many : array();
				foreach ($valores_has_many as $chave => $ids){
					eval('if (isset('.get_called_class().'::$has_many)) $has_many = '.get_called_class().'::$has_many;');
					if (isset($has_many)){
						foreach ($has_many as $name => $relation){
							if (isset($relation["through"])){
								if ($name == $chave){
									$field = $relation["field"];
									$class = $relation["class"];
									$association_class = $relation["through"];
									$foreign_key = $relation["foreign_key"];
									eval('$table = '.$class.'::TABLENAME;');
									eval('$association_table = '.$association_class.'::TABLENAME;');
									foreach ($ids as $z=>$id){
										if (gettype($id) == "array"){
										   $fields = array($foreign_key => $valores["id"]);
											foreach ($id as $a_field => $a_value)
												if ($a_field != $foreign_key)
													$fields[$a_field] = $a_value;
											if ($fields["id"] == 0)
												$ids[$z]=executar_insert($association_table, $fields);
									   }
									   else
											if (!fetch(collection($association_table, array($foreign_key => $valores["id"], $field => $id))))
												executar_insert($association_table, array($foreign_key => $valores["id"], $field => $id));
									}
										
									$all_associations = collection($association_table, array($foreign_key => $valores["id"]));
									while ($record = fetch($all_associations)){
										if (gettype($id) == "array"){
											if (!in_array($record["id"], map($ids, "id")))
												executar_delete($association_table, "".$record["id"]);
											else {
												$map = map($ids, "id");
												$i = -1;
												foreach ($map as $y=>$x)
													if ($x == $record["id"]){
														$i = $y;
														break;
													}
												if ($i >= 0){
													$update = array();
													foreach ($ids[$i] as $a => $b)
														if (!is_numeric($a))
															$update[$a] = $b;
													executar_update($association_table, $update, "id='".$ids[$i]['id']."'");
												}
											}
										}
										else
											if (!in_array($record[$field], $ids))
												executar_delete($association_table, $record["id"]);
									}
								}
							}
							else {
								//********************************* INÍCIO
								if ($name == $chave){
									$field = $relation["field"];
									$class = $relation["class"];
									eval('$table = '.$class.'::TABLENAME;');
									foreach ($ids as $z=>$id){
										if (gettype($id) == "array"){
										   $fields = array($field => $valores["id"]);
											foreach ($id as $a_field => $a_value)
												$fields[$a_field] = $a_value;
											if ($fields["id"] == 0)
												$ids[$z] = executar_insert($table, $fields);
									   	}
									}
										
									$all = collection($table, array($field => $valores["id"]));
									while ($record = fetch($all)){
										if (gettype($id) == "array"){
											if (!in_array($record["id"], map($ids, "id")))
												executar_delete($table, "".$record["id"]);
											else {
												$map = map($ids, "id");
												$i = -1;
												foreach ($map as $y=>$x)
													if ($x == $record["id"]){
														$i = $y;
														break;
													}
												if ($i >= 0){
													$update = array();
													foreach ($ids[$i] as $a => $b)
														if (!is_numeric($a))
															$update[$a] = $b;
													executar_update($table, $update, "id='".$ids[$i]['id']."'");
												}
											}
										}
									}
								}
								//********************************* FIM
							}
						}						
					}

					eval('if (isset('.get_called_class().'::$has_one)) $has_one = '.get_called_class().'::$has_one;');
					if (isset($has_one)){
						foreach ($has_one as $name => $relation){
							if ($name == $chave){
								$field = $relation["field"];
								$class = $relation["class"];
								eval('$table = '.$class.'::TABLENAME;');
								$target_record = fetch(collection($table, array($field => $valores["id"])));
								if ($target_record)
									executar_update($table, $ids, 'id = "'.$target_record["id"].'"');
								else{
									$values = $ids;
									$values[$field] = $valores["id"];
									executar_insert($table, $values);
								}
							}
						}
					}
				}
			}
			
			//corrigindo a posicao na lista, quando utilizar acts_as_list (para o caso de um registro ser movido para uma lista diferente)
			if ($objeto_salvo){
				eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
				if (isset($acts_as_list)){
					$field = $acts_as_list["field"];
					if (gettype($acts_as_list["scope"]) == "array" && count($acts_as_list["scope"]) > 1){
						$objeto_novo = find($tablename, $objeto_velho["id"]);
						$where_novo = self::whereClauseFromScope($objeto_novo["id"]);
						if ($where_velho != $where_novo){
							$posteriores = self::collection($field." > '".$objeto_velho[$field]."' AND ".$where_velho);
							while ($posterior = fetch($posteriores))
								self::atualizar(array("id" => $posterior["id"], $field => $posterior[$field] - 1));
							$ultimo_registro_lista_nova = fetch(self::collection("id <> '".$objeto_novo["id"]."' AND ".$where_novo, $field." DESC LIMIT 1"));
							if ($ultimo_registro_lista_nova)
								self::atualizar(array("id" => $objeto_novo["id"], $field => $ultimo_registro_lista_nova[$field] + 1));
							else
								self::atualizar(array("id" => $objeto_novo["id"], $field => 1));
						}
					}
				}
			}
			return $objeto_salvo;
		}
         
        static function remover($id){
            eval('$tablename = '.get_called_class().'::TABLENAME;');
            $self = self::find($id);
             
            //reconstruindo as posições quando usar acts_as_list
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list) && $self){
                $field = $acts_as_list["field"];
                if (vazio($acts_as_list["scope"]))
                    $posteriores = self::collection($field." > '".$self[$field]."'");
                else
                    $posteriores = self::collection($field." > '".$self[$field]."' AND ".self::whereClauseFromScope($id));
                while ($registro = fetch($posteriores))
                    self::atualizar(array("id" => $registro["id"], $field => $registro[$field] -1));
            }
             
            return executar_delete($tablename, $id);
        }
         
        //returna um array contendo somente os ids de determinado relacionamento
        static function mapIds($id, $relationship){
            return map(self::listOf($id, $relationship), "id");
        }
         
        static function removerDependentes($id, $relacionamento){
            eval('if (isset('.get_called_class().'::$has_many)) $has_many = '.get_called_class().'::$has_many;');
            if (isset($has_many)){
                foreach ($has_many as $name => $relation){
                    if (isset($relation["through"])){
                        if ($name == $relacionamento){
                            $association_class = $relation["through"];
                            $foreign_key = $relation["foreign_key"];
                            eval('$association_table = '.$association_class.'::TABLENAME;');
                            $all_associations = collection($association_table, array($foreign_key => $id));
                            while ($record = fetch($all_associations))
                                executar_delete($association_table, $record["id"]);
                        }
                    }
                    else{
                        if ($name == $relacionamento){
                            $field = $relation["field"];
                            $class = $relation["class"];
                            eval('$table = '.$class.'::TABLENAME;');
                            $all_associations = collection($table, array($field => $id));
                            while ($record = fetch($all_associations))
                                executar_delete($table, $record["id"]);
                        }
                    }
                }                       
            }
        }
         
        ###########################################################################
        ## MÉTODOS PARA MANIPULAR TABELAS COM CAMPO 'POSICAO', EM FORMA DE LISTA ##
        ###########################################################################
         
        static function moveUpOnList($id){
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list)){
                $field = $acts_as_list["field"];
                eval('$tablename = '.get_called_class().'::TABLENAME;');
                $record = find($tablename, $id);
                if ($record[$field] > 1){
                    if (vazio($acts_as_list["scope"])){
                        $anterior = fetch(collection($tablename, array($field => $record[$field] - 1)));
                        if ($anterior)
                            if (executar_update($tablename, array($field => $anterior[$field] + 1), "id = '".$anterior["id"]."'"))
                                return executar_update($tablename, array($field => $record[$field] - 1), "id = '".$record["id"]."'");
                    }
                    else{
                        $where = self::whereClauseFromScope($id);
                        $anterior = fetch(collection($tablename, $field." = '".($record[$field] - 1)."' AND ".$where));
                        if ($anterior)
                            if (executar_update($tablename, array($field => $anterior[$field] + 1), "id = '".$anterior["id"]."'"))
                                return executar_update($tablename, array($field => $record[$field] - 1), "id = '".$record["id"]."'");
                    }
                }
            }
            return false;
        }
         
        static function moveDownOnList($id){
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list)){
                $field = $acts_as_list["field"];
                eval('$tablename = '.get_called_class().'::TABLENAME;');
                $record = find($tablename, $id);
                if (vazio($acts_as_list["scope"])){
                    $ultimo = self::lastRecordOnList();
                    if ($record[$field] < $ultimo[$field]){
                        $posterior = fetch(collection($tablename, array($field => $record[$field] + 1)));
                        if ($posterior)
                            if (executar_update($tablename, array($field => $posterior[$field] - 1), "id = '".$posterior["id"]."'"))
                                return executar_update($tablename, array($field => $record[$field] + 1), "id = '".$record["id"]."'");
                    }
                }
                else{
                    $where = self::whereClauseFromScope($id);
                    $ultimo = self::lastPositionOnList($id);
                    if ($record[$field] < $ultimo){
                        $posterior = fetch(collection($tablename, $field." = '".($record[$field] + 1)."' AND ".$where));
                        if ($posterior)
                            if (executar_update($tablename, array($field => $posterior[$field] - 1), "id = '".$posterior["id"]."'"))
                                return executar_update($tablename, array($field => $record[$field] + 1), "id = '".$record["id"]."'");
                    }
                }
            }
            return false;
        }
         
        static function lastRecordOnList($id = 0){
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list)){
                $field = $acts_as_list["field"];
                eval('$tablename = '.get_called_class().'::TABLENAME;');
                if (vazio($acts_as_list["scope"])){
                    $ultimo = fetch(collection($tablename, array(), $field." DESC LIMIT 1"));
                }
                else{
                    $where = self::whereClauseFromScope($id);
                    $ultimo = fetch(collection($tablename, $where, $field." DESC LIMIT 1"));
                }
                return $ultimo;
            }
            else
                return false;
        }
         
        static function lastPositionOnList($id = 0){
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list)){
                $field = $acts_as_list["field"];
                eval('$tablename = '.get_called_class().'::TABLENAME;');
                $ultimo = self::lastRecordOnList($id);
                return $ultimo[$field];
            }
            else
                return 0;
        }
         
        static function isLastOnList($id){
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list)){
                $field = $acts_as_list["field"];
                eval('$tablename = '.get_called_class().'::TABLENAME;');
                $ultimo = self::lastRecordOnList($id);
                return $ultimo["id"] == $id;
            }
            else
                return false;
        }
         
        static function isFirstOnList($id){
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list)){
                $field = $acts_as_list["field"];
                eval('$tablename = '.get_called_class().'::TABLENAME;');
                $self = self::find($id);
                return $self[$field] == 1;
            }
            else
                return false;
        }
         
        static function whereClauseFromScope($id){
            eval('if (isset('.get_called_class().'::$acts_as_list)) $acts_as_list = '.get_called_class().'::$acts_as_list;');
            if (isset($acts_as_list)){
                if (!vazio($acts_as_list["scope"])){
                    $field = $acts_as_list["field"];
                    eval('$tablename = '.get_called_class().'::TABLENAME;');
                    if (gettype($id) != "array")
                        $record = find($tablename, $id);
                    $scope = gettype($acts_as_list["scope"]) == "array" ? $acts_as_list["scope"] : array($acts_as_list["scope"]);
                    foreach ($scope as $attr)
                        if (gettype($id) == "array")
                            $where[] = $attr." = '".$id[$attr]."'";
                        else
                            $where[] = $attr." = '".$record[$attr]."'";
                    $where = implode(" AND ", $where);
                    return $where;
                }
            }
            return "";
        }
         
        ##############################
        ## FIM DOS MÉTODOS DE LISTA ##
        ##############################
    }
?>