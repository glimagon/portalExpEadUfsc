<?php
    //Conecta ao BD, carrega todas as classes e funções
    require_once 'core/init.php';

    //Caso dados tenham sido enviados, em método get ou post...
    if (Input::exists()) {

        echo '<script>  alert("Existe input!") </script>';

        //... verifica se o token gerado no carregamento da página é válido (verifica se ainda é a mesma pessoa tentando cadastrar um usuário)
        if(Token::check(Input::get('token'))) {

            echo '<script>  alert("Token é valido!") </script>';

            //Cria um objeto de validação...
            $validate = new Validate();
            //.. e define as regras.
            $validation = $validate->check($_POST, array(
                'txtNome' => array(
                    'name'      => 'Name',
                    'required'  => true,
                    'min'       => 2,
                    'max'       => 50
                ),
                'txtApelido' => array(
                    'name'      => 'Username',
                    'required'  => true,
                    'min'       => 2,
                    'max'       => 20,
                    'uniqu'     => 'usuario'
                ),
                'txtEmail' => array(
                    'email'        => 'Email', 
                    'required'     => true,
                    'min'          => 2,
                    'max'          => 20,
                    'unique_email' => 'usuario'
                ),
                'txtSenha' => array(
                    'name'      => 'Password',
                    'required'  => true,
                    'min'       => 6
                ),
                'txtConfirmaSenha' => array(
                    'required'  => true,
                    'matches'   => 'password'
                ),
            ));

            //Caso o preenchimento do formulário tenha sido feito de maneira correta...
            if ($validate->passed()) {

                echo '<script>  alert("Passou na validação!") </script>';

                $user = new User();         //Cria um novo usuário
                $salt = Hash::salt(32);     //Gera uma hash que vai ser concatenada à string de senha (criptografada)

                try {
                    //Tenta inserir os dados de usuário no banco
                    $user->create(array(
                        'nome'              => Input::get('txtNome'),
                        'email'             => Input::get('txtEmail'),
                        'apelido'           => Input::get('txtApelido'),
                        'id_nivel_acesso'   => 1,
                        'id_depto'          => Input::get('cbxDepto'),
                        'senha'             => Hash::make(Input::get('txtSenha'), $salt),
                        'salt_hash'         => $salt,
                        'dt_cadastro'       => date('Y-m-d H:i:s'),
                        'ativo'             => 1
                    ));

                    //Caso não ocorra nenhuma merda, informa sucesso ao usuário...
                    Session::flash('home', 'Bem vindo ' . Input::get('txtApelido') . '! Sua conta foi registrada com sucesso. Você pode agora realizar o login.');

                    //... e em seguida o redireciona para a página principal
                    Redirect::to('index.php');

                } catch(Exception $e) {
                    //Informa erros ao usuário
                    //php_alert('Ocorreu o seguinte erro: ' . $e->getMessage());                    
                    //Dialog::error('Ocorreu o seguinte erro:', $e->getMessage());
                    echo '<script> alert("Segue o erro abaixo: \n " ' . $e->getMessage() . ' ) </script>';
                }
            } else {

                echo '<script>  alert("Não passou na validação!") </script>';

                echo '<script type="text/javascript"> alert(' . print_r($validate->errors()) . ') </script>';

            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Cadastro de Usuário</title>

        <!-- Biblioteca Bootstrap -->
        <link href="css/sticky-footer.css" rel="stylesheet">
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="bootstrap/css/style.css" rel="stylesheet">
        <script src="bootstrap/js/bootstrap.min.js"></script>

        <!-- Biblioteca jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.0.0/jquery-migrate.js"></script>

    </head>
    <body> 
        <!-- Menu principal (navbar) da página -->
        <nav class="navbar navbar-inverse navbar-fixed-top">        
             <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Experiências Profissionais em EaD</a>
                </div>

                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="index.php">Início</a></li>
                        <li><a href="#">Opções</a></li>
                        <li><a href="profile.php">Perfil</a></li>
                        <li><a href="help.php">Ajuda</a></li>
                    </ul>
                </div>
             </div>         
        </nav>

        <!-- area de campos do form -->
        <div id="main" class="container-fluid">
            <hr />
            <h3 class="page-header">Cadastro Inicial</h3>
            <form id="frmCadastro" name="frmCadastro" class="form-horizontal" method="post" action="" align="center">
                                
                <div class="form-group">
                    <label class="control-label col-sm-2" for="txtNome">Nome completo*</label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control" id="txtNome" name="txtNome" value="<?php echo escape( Input::get('txtNome') ); ?>" />
                    </div> 
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="txtApelido">Usuário*</label>
                    <div class="col-sm-5">
                      <input type="text" class="form-control" id="txtApelido" name="txtApelido" value="<?php echo escape( Input::get('txtApelido') ); ?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="txtEmail">E-mail*</label>
                    <div class="col-sm-5">
                      <input type="email" class="form-control" id="txtEmail" name="txtEmail" value="<?php echo escape( Input::get('txtEmail') ); ?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="txtNome">Senha*</label>
                    <div class="col-sm-2">
                      <input type="password" class="form-control" id="txtSenha" name="txtSenha" />
                    </div>
                    
                    <label class="control-label col-sm-1" for="txtNome">Repita a senha*</label>
                    <div class="col-sm-2">
                      <input type="password" class="form-control" id="txtConfirmaSenha" name="txtConfirmaSenha" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="cbxCampus">Câmpus universitário*</label>
                    <div class="col-sm-5">
                      <select class="form-control" id="cbxCampus" name="cbxCampus">
                            <?php include "lista_campus.php"; ?>                          
                      </select>
                    </div>
                </div>
                  
                <div class="form-group">
                    <label id="labelCbxCentro" class="control-label col-sm-2" for="cbxCentro">Centro de ensino*</label>
                    <div class="col-sm-5">
                      <select class="form-control" id="cbxCentro" name="cbxCentro">
                            

                      </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="cbxDepto">Departamento*</label>
                    <div class="col-sm-5">
                      <select class="form-control" id="cbxDepto" name="cbxDepto">
                            

                      </select>
                    </div>
                </div>
                
                
<!--
                <div class="form-group">
                    <label class="control-label col-sm-2" for="dtIngresso">Data de ingresso na UFSC:</label>
                    <div class="col-sm-2">
                        <?php                           
                            //  Aqui faz a verificação do tipo de navegador, pelo fato de o Firefox não
                            //  suportar 'input date' do html5 (fuck this shit btw)
                            
                            $binfo = get_browser(null);

                            if ($binfo["name"] == "Mozilla Firefox") {
                                echo "<input type='text' class='form-control' placeholder='DD/MM/AAAA' id='dtIngresso' name='dtIngresso'/>";
                            } else {
                                echo "<input type='date' class='form-control' id='dtIngresso' name='dtIngresso'/>";
                            }
                        ?>                      
                    </div>
                </div>
-->
                <div id="actions" class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                        <a href="index.html" class="btn btn-default">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>

        <footer class="footer">
          <div class="container">
            <p class="text-muted">Rua Dom Joaquim, 757 - Centro - CEP 88015-310 - Florianópolis - SC - Fone (48) 3721.6221</p>
          </div>
        </footer>

        <script src="js/populaCombos.js"></script>     
    </body>
</html>