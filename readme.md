[ PSEUDO MVC]
	- Estrutuda baseada na arquitetura Model-View-Controller
	- Models são as tabelas do banco de dados mapeadas em forma de classes. Desta forma, 95% das tarefas que dizem respeito ao banco ficam concentradas nos modelos
	- Views são as páginas propriamente ditas. Rotas que tenham como resultado a exibição de alguma informação sempre utilizarão Views
	- Controllers são responsáveis por gerenciar rotas e executar tarefas que não tenham necessidade de renderização de Views. Todos os arquivos localizados na pasta controllers, mesmo que dentro de subpastas, serão automaticamente carregados
	- Helpers estão em uma quarta camada, onde encontram-se funções auxiliares usadas em toda a aplicação, desde formatações e validações até manipulação do banco. Todos os arquivos desta pasta serão automaticamente carregados, portanto caso haja necessidade de criar um novo helper, basta jogá-lo nessa pasta


[ USO DO RECAPTCHA ]
	- Configurar as chaves do recaptcha no arquivo lib/pacote.php
	- Incluir a biblioteca através da função: pacote("recaptcha")
	- Para exibir o campo no formulário: echo recaptcha_get_html($_GET["publickey"])
	- Para validar o campo: if (validar_recaptcha($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]))
	- Outra forma de validar o campo é: if (validar_recaptcha()), sem parâmetros


[ USO DE TEMPLATES ]
	- Criar os arquivos desejados na pasta templates
	- Para carregar a template, usar a função: template("header", "body")
		* Não é necessário informar o .php no nome dos arquivos
		* Podem ser passados até 3 parâmetros com templates para serem incluídas


[ IDENTIFICAÇÃO DE ACESSO VIA MOBILE ]
	- Todo acesso é automaticamente validado para verificar a origem do acesso
	- Se desejar que uma página tenha uma versão mobile, deve ser criado um arquivo com a mesma estrutura de diretórios do original, porém na pasta application_mobile. Ex.: application/home/index.php deve ter um arquivo application_mobile/home/index.php
	- Caso o acesso seja feito de um celular e não existir o arquivo referente na pasta application_mobile, a versão normal do arquivo é carregada
	- Por convenção, as templates mobile seguem o padrão arquivo_mobile.php. Ex.: header_mobile.php, footer_mobile.php


[ MODELS ]
	- Cada tabela do banco de dados deve possuir um arquivo contendo uma classe na pasta de modelo. Por convenção, nomes de tabelas sempre no plural e nomes de classes sempre no singular. Ex.: tabela clientes, classe Cliente
	- A classe deve herdar da classe Table, que contém os métodos básicos para manipulação de tabelas. Ex.: class Cliente extends Table{ }
	- Deve haver obrigatoriamente na classe uma constante TABLENAME que define o nome da tabela no banco. Ex.: const TABLENAME = "clientes"
	[ RELACIONAMENTOS ]
		$belongs_to: usado quando existe uma chave estrangeira na tabela que faz referência a outro registro de uma classe qualquer. Ex.: Classe Cliente $belongs_to = array("cidade_natal" => array("class" => "Cidade", "field" => "cidade_id"))
		$has_many: usado de forma reversa ao $belongs_to. Ex.: Classe Cidade $has_many = array("clientes" => array("class" => "Cliente", "field" => "cidade_id"))
		$has_many through: usado para relacionamentos muitos-para-muitos. Caso um Cliente possa ter várias cidades e um Cidade possa pertencer a vários clientes, teríamos como exemplo: Classe Cliente $has_many = array("cidades" => array("through" => "ClienteCidade", "field" => "cidade_id", "foreign_key" => "cliente_id", "class" => "Cidade"))
		$has_one: similar ao $has_many mas para relacionamentos um-para-um. Também funciona de forma reversa ao $belongs_to. Ex.: Classe Cliente $has_one = array("endereco" => array("field" => "cliente_id", "class" => "Endereco"))
	[ COMPORTAMENTO ACTS_AS_LIST ]
		- Usado para tabelas que possuam a necessidade de funcionar como uma lista ordenada de registros
		- É obrigatório um campo que controle a posição e um parâmetro adicional scope define qual o contexto dentro da tabela deve ser considerado uma lista
		- Assim pode haver mais de uma lista dentro de uma mesma tabela
		- Ex.: Classe Cliente $acts_as_list = array("field" => "posicao", "scope" => array("sexo")). Nesse exemplo, a tabela terá um controle de lista diferente para cada sexo existente (uma lista para homens e uma para mulheres)
		- Usando $acts_as_list, estarão disponíveis vários métodos para manipulação dos registros na classe Table
	[ MÉTODOS DISPONÍVEIS ATRAVÉS DA CLASSE TABLE ]
		ignoredWords(): retorna array
			Retorna uma lista de palavras que podem ser usadas futuramente para melhorar buscas
		find($id [, $include_relationships = true]): retorna mysql_fetch_array
			Encontra o registro na tabela que possui $id passado como parâmetro. O parâmetro opicional $include_relationships define se os relacionamentos de has_many, belongs_to e has_one devem ser considerados e incluídos no resultado em forma de array multidimensional
		findBy($field, $value, [, $include_relationships = true]): retorna mysql_fetch_array
			Encontra o registro na tabela que possui o campo $field igual a $value. $include_relationships funcione de maneira análoga ao método find()
		findRandom([$include_relationships = true]): retorna mysql_fetch_array
			Retorna um registro aleatório da tabela. $include_relationships funcione de maneira análoga ao método find()
		listOf($id, $relationship [, $order = "nome"]): returna um array de mysql_fetch_array
			Retorna um array contendo os registros referentes aos relacionamentos has_many definidos previamente na classe
		fields(): retorna array
			Retorna a lista de campos da tabela
		all([$order = "default_field"]): returna mysql_query
			Retorna uma query contendo todos os registros da tabela ordenados pelo parâmetro $order. Caso não seja informado este parâmetro, a ordem será assumida primeiramente pelos campos do comportamento acts_as_list (quanto for o caso, para ordenar pela posição da lista), secundariamente pelo campo "nome" e terciariamente pelo "id"
		collection([$condicoes = array() [, $order = "default_field"]]): retorna mysql_query
			Retorna uma query contendo todos os registros da tabela que atendam as $condicoes passadas como parâmetro. As condições podem ser passadas como array("campo" => "valor") ou como string "campo > 'valor'". O parâmetro $order funciona analogamente ao método all()
		count(): retorna inteiro
			Retorna o número total de registros da tabela
		first(): retorna mysql_fetch_array
			Retorna o primeiro registro da tabela
		last(): retorna mysql_fetch_array
			Retorna o último registro da tabela
		criar($valores): retorna mysql_fetch_array ou false
			Executa a inserção de registros na tabela. Os $valores passados devem ser do tipo array("campo" => "valor"). Usando relacionamentos has_many through é possível salvar os registros das tabelas de ligação automaticamente caso sejam apenas ids e não contenha mais atributos, por exemplo $valores = array("clientes" => array(2, 6, 9)). Caso utilize o comportamento acts_as_list, o atributo que armazena a posição é automaticamente gerado. O retorno da função é o registro criado ou false caso haja erro
		atualizar($valores): retorna boolean
			Executa a atualização de registro na tabela. Juntamente com os $valores deve ser passado o id do registro a ser alterado. Os demais comportamentos são similares ao método criar()
		remover($id): retorna boolean
			Remove o registro da tabela que possua o $id passado como parâmetro. Caso use o comportamente acts_as_list, a posição dos elementos da lista é automaticamente ajustado
		mapsIds($id, $relationship): retorna array
			Retorna um array contendo somente os ids dos registros encontrados nos relacionamentos. Tem o mesmo retorno da função listOf, porém somente com os ids e não todos os campos
		removerDependentes($id, $relacionamento): sem retorno
			Remove os registros de acordo com o relacionamento $has_many passado. Não é aconselhável o uso deste método, pois é removido somente o registro da tabela e caso o método remover() tenha sido sobrescrito na classe do relacionamento ele não será levado em consideração
	[ MÉTODOS DISPONÍVEIS PARA O COMPORTAMENTO ACTS_AS_LIST ]
		moveUpOnList($id): retorna boolean
			Move para cima na lista o registro com o id igual ao $id passado como parâmetro
		moveDownOnList($id): retorna boolean
			Move para baixo na lista o registro com o id igual ao $id passado como parâmetro
		lastRecordOnList([$id = 0]): retorna mysql_fetch_array ou boolean
			Retorna o último registro da lista. O parâmetro $id deve ser passado somente quando utilizar scope no acts_as_list
		lastPositionOnList([$id = 0]): retorna inteiro
			Retorna a última posição ocupada na lista. O parâmetro $id funciona de forma análoga ao método anterior
		isLastOnList($id): retorna boolean
			Retorna se o registro com o $id passado como parâmetro é o último da lista
		isFirstOnList($id): retorna boolean
			Retorna se o registro com o $id passado como parâmetro é o primeiro da lista
		whereClauseFromScope($id): retorna string
			Retorna uma string contendo as condições a serem usadas nas consultas SQL para se obter corretamente os registros da lista. Usado somente se o acts_as_list tiver scope


[ IMAGENS, CSS e JAVASCRIPT ]
	- Esses arquivos são armazenados na pasta assets, em suas respectivas pastas
	- Os ícones utilizados devem estar na pasta assets/images/18x18, para que possam ser carregados pela função img()
		* A função img() carrega uma imagem da pasta 18x18. Ex.: echo img("edit", 'title="Editar"')
		* Não é obrigatório especificar a extensão da imagem. Por padrão será assumido que seja .png
		* Pode ser usada para carregar outras imagem informando o caminho relativo. Ex.: para carregar a imagem assets/images/template/logo.php, use: echo img("../template/logo")
	- Para utilizar favicon, basta existir um arquivo favicon.png ou favicon.ico na pasta assets/images e ele será carregado automaticamente


[ ENVIO DE EMAIL ]
	- O envio de email é feito através da função enviar_email($destinatario, $assunto, $corpo)
	- Pode ser feito através de SMTP ou pela função mail() do PHP. Para definir a forma de envio, edite o helper de email alterando a chamada da função enviar_email() para a função convencional. Por padrão o envio é feito por SMTP
	- A configuração dos dados do servidor SMTP deve ser feita também neste arquivo
	- As funções de envio são independentes, existindo enviar_email_smtp() e enviar_email_convencional(), e a enviar_email() faz uma chamada a uma dessas duas funções (SMTP por padrão)


[ FORMULÁRIOS ]
	- A geração de formulários pode ser facilitada através de funções.
	- A tag <form> pode ser substituida pela função form([$options = array()]). Dois parâmetros importantes podem ser passados através das $options:
		- values: um array de elementos do tipo "campo" => "valor" que será usado para preencher automaticamente os campos do formulários com os respectivos nomes. Ex.: echo form(array("values" => array("nome" => "Pedro", "nascimento" => "10/10/2000"))). Normalmente o array de values será o próprio $_POST
		- alinhamento: dita o alinhamento dos labels dentro de cada elemento do form
	- O fechamento da tag (</form>) pode ser substituido pela função end_form(). Ex.: echo end_form()
	- Os campos do formulário devem estar contidos em uma tag <table> para ser gerada a grade corretamente. As funções auxiliares para gerar campos são:
		- commom_field([$options = array()]): é a função genérica para geração de campos. Todas as demais funções utilizam ela para gerar o html necessário. Só deve ser usado quando for preciso criar um campo ou uma linha na grade do formulário diferente do padrão
		- text_field([$options = array()]): gera um campo de texto padrão
		- password_field([$options = array()]): gera um campo de senha padrão
		- telefone_field([$options = array()])
		- cnpj_field([$options = array()])
		- cpf_field([$options = array()])
		- cep_field([$options = array()])
		- textarea([$options = array()]): rows e cols podem ser passadas como "size" => "<cols>x<rows>"
		- button([$options = array()])
		- submit([$options = array()]): $options neste caso pode ser uma string, que será o value do button
		- select([$params = array()])
		- options_for_select($options[, $selecionado = ""[, $prompt = false]]): gera uma string de <options> de acordo com o array de $options passado por parâmetro. $prompt é o texto inicial a ser mostrado, por exemplo "-- SELECIONE --"
		- options_for_select_from_collection($collection, $value, $label[, $selecionado = ""[, $prompt = true]]): gera uma string de <options> de acordo com a query ou array passado na $collection. $value será campo (chave do array $collection) que será o atributo value da option e $label será o campo (chave do array $collection) que será o texto exibido nela
	- O uso mais detalhado de cada função pode ser visto no helper de formulários