<?php
include "../ScriptLogin.php";
$contaDAO = new ContaDAO($conexao);
$valoresDivida = $contaDAO->getValoresDividas($usuario->getId());
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>SB Admin 2 - Bootstrap Admin Theme</title>

        <style>
            .dropdown-menu {
                max-height: 390px;
                overflow-y: scroll;
            }
        </style>
        <!-- Bootstrap Core CSS -->
        <link href="Componentes/bower_components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="Componentes/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="Componentes/dist/css/sb-admin-2.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="Componentes/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <script src="Componentes/bower_components/jquery/dist/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="Componentes/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="Componentes/bower_components/metisMenu/dist/metisMenu.min.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="Componentes/dist/js/sb-admin-2.js"></script>

        <script src="Componentes/js/matchHeight.js"></script>

        <!-- Menu JS -->
        <script src="js/menu.js"></script>
        <script>idUsuario = <?php echo $usuario->getId(); ?>;</script>

        <script>
            $(function () {
                var options = {
                    byRow: true,
                    property: 'height',
                    target: null,
                    remove: false
                }
                $('.item').matchHeight(options);
            });
        </script>
    </head>

    <body>
        <div id="wrapper">

            <?php include "./menu.php"; ?>

            <!-- Page Content -->
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Informações Gerais</h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <div class="col-md-4 col-md-offset-1">
                            <span>Você precisa pagar:</span>
                            <h3><?php echo "R$ " . $formato->numeroInterface($valoresDivida[0]); ?></h3>
                        </div>
                        <div class="col-md-4 col-md-offset-3">
                            <span>Você precisa receber:</span>
                            <h3><?php echo "R$ " . $formato->numeroInterface($valoresDivida[1]); ?></h3>
                        </div>
                    </div>
                    <hr>
                   
                    <div class="row">
                        <!-- Cadastrar Conta -->
                        <div class="col-md-2 col-md-offset-2">
                            <a href="CadastroConta.php">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-center item">
                                                <i class="fa fa-comments fa-5x"></i>
                                                Cadastrar Conta<br/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <!-- Solicitar Pagamento -->
                        <div class="col-md-2">
                            <a href="Pagamento.php">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-center item">
                                                <i class="fa fa-comments fa-5x"></i>
                                                Solicitar Pagamento
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                            </a>
                        </div>


                        <!-- Soolicitar Pagamento -->
                        <div class="col-md-2">
                            <a href="Pagamento.php">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-center item">
                                                <i class="fa fa-comments fa-5x"></i>
                                                Receber Pagamento
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- Cadastrar Conta -->
                        <div class="col-md-2 col-md-offset-2">
                            <a href="ContasPendentes.php">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-center item">
                                                <i class="fa fa-comments fa-5x"></i>
                                                Contas Pendentes
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <!-- Histórico -->
                        <div class="col-md-2">
                            <a href="Historico.php">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-center item">
                                                <i class="fa fa-comments fa-5x"></i>
                                                Histórico
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Grupos -->
                        <div class="col-md-2">
                            <a href="Grupos.php">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-center item">
                                                <i class="fa fa-comments fa-5x"></i>
                                                Grupos
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-md-2 col-md-offset-8">
                            <form id="sair" action="../abort.php">
                                <button class="btn btn-primary btn-block" type="submit" form="sair">
                                    <span class='glyphicon glyphicon-off' aria-hidden='true'></span> 
                                    Sair
                                </button>
                            </form>

                        </div>

                    </div>

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->

    </body>

</html>
