<?php
$notificacaoDAO = new NotificacaoDAO($conexao);
$qtdNotificacoes = $notificacaoDAO->getQuantidadeNotificacoesNaoVisualizadas($usuario->getId());

$republicaDAO = new RepublicaDAO($conexao);
$gruposMenu = $republicaDAO->getRepublicasSimplesPorIdUsuario($usuario->getId());
$convitesMenu = $republicaDAO->getConviteParaUsuario($usuario->getId());

$contaDAO = new ContaDAO($conexao);
$contasAlerta = $contaDAO->getContasAlerta($usuario->getId());
?>

<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="Index.php">Finanças Genérica</a>
    </div>
    <!-- /.navbar-header -->

    <!-- Menu -->
    <ul class="nav navbar-top-links navbar-right">

        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="glyphicon glyphicon-exclamation-sign"></i>
                <span class="badge" ><?php echo count($contasAlerta); ?></span>
                <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-messages">
                <?php
                foreach ($contasAlerta as $conta) {
                    $integrante = $conta->getIntegrante($usuario->getId());
                    $porcentagem = ($integrante->getValorJaPagou() / $integrante->getValorTotalPagar()) * 100;
                    $porcentagem = floor($porcentagem) . "%";
                    $valorJaPagou = $formato->numeroInterface($integrante->getValorJaPagou());
                    $valorTotalPagar = $formato->numeroInterface($integrante->getValorTotalPagar());
                    echo "<li> " .
                    "<a href='#'> " .
                    "<div> " .
                    "<p> " .
                    "<strong>" . $conta->getNome() . "</strong> " .
                    "<span class='pull-right text-muted'>$porcentagem pago ($valorJaPagou / $valorTotalPagar)</span> " .
                    "</p> " .
                    "<div class='progress progress-striped active'> " .
                    "<div class='progress-bar progress-bar-danger' role='progressbar' style='width: $porcentagem'> " .
                    "<span class='sr-only'>$porcentagem pago</span> " .
                    "</div> " .
                    "</div> " .
                    "</div> ";
                    echo "<li class='divider'></li>";
                }

                if (count($contasAlerta) == 0) {
                    echo "<li class='divider'></li>";
                }
                ?>
                <li>
                    <a class="text-center" href="#">
                        <strong>Ver todos as contas em alerta</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            </ul>
            <!-- /.dropdown-messages -->
        </li>

        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-envelope fa-fw"></i>  
                <span class="badge" ><?php echo count($convitesMenu); ?></span>
                <i class="fa fa-caret-down"></i>
            </a>

            <ul class="dropdown-menu dropdown-messages">
                <?php
                foreach ($convitesMenu as $convite) {
                    $idConvite = $convite->getId();
                    echo "<li>";
                    echo "<div>" .
                    "<strong>Grupo " . $convite->getRepublica()->getNome() . " mandou um convite.</strong>" .
                    "</div>";
                    echo "<div class='text-right'>" .
                    "<form action='ControlesScript/ControleConviteScript.php' method='post' id='formMenuAceitaConvite'>" .
                    "<input type='hidden' name='idConvite' value='$idConvite' /> " .
                    "<input type='hidden' name='comando' value='aceitaConvite' /> " .
                    "</form>" .
                    "<form action='ControlesScript/ControleConviteScript.php' method='post' id='formMenuRejeitaConvite' >" .
                    "<input type='hidden' name='idConvite' value='$idConvite' /> " .
                    "<input type='hidden' name='comando' value='rejeitaConvite' /> " .
                    "</form>" .
                    "<input type='submit' class='btn btn-success btn-xs' value='Aceitar' form='formMenuAceitaConvite' /> " .
                    "<input type='submit' class='btn btn-danger btn-xs' value='Rejeitar' form='formMenuRejeitaConvite' />" .
                    "</div>";
                    echo "</li>";
                    echo "<li class='divider'></li>";
                }
                if (count($convitesMenu) == 0) {
                    echo "<li class='divider'></li>";
                }
                ?>
                <li>
                    <a class="text-center" href="Grupos.php">
                        <strong>Ver todos os convites de grupos</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            </ul>
            <!-- /.dropdown-messages -->
        </li>

        <!-- Notificações -->
        <li class="dropdown" id="liNotificacoes">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-bell fa-fw"></i>
                <span class="badge" id="qtdNotificacoes"><?php echo $qtdNotificacoes; ?></span>
                <i class="fa fa-caret-down"></i>
            </a>

            <ul class="dropdown-menu dropdown-messages" id="notificacoes">

            </ul>
            <!-- /.dropdown-messages -->
        </li>
        <!-- /.dropdown -->



        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="Index.php"><i class="fa fa-user fa-fw"></i> <?php echo $usuario->getNome(); ?></a>
                </li>
                <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                </li>
                <li class="divider"></li>
                <li><a href="../abort.php"><i class="fa fa-sign-out fa-fw"></i> Encerrar Sessão</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->

    <!-- Menu Lateral -->
    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li>
                    <a href="Componentes/Exemplos/Index.html"><i class="fa fa-dashboard fa-fw"></i> Template</a>
                </li>
                <li>
                    <a href="Index.php"><i class="fa fa-dashboard fa-fw"></i> Informações Gerais</a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Conta<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="CadastroConta.php">Cadastrar Conta</a>
                        </li>
                        <li>
                            <a href="Pagamento.php">Solicitar Pagamento</a>
                        </li>
                        <li>
                            <a href="Pagamento.php">Receber Pagamento</a>
                        </li>
                        <li>
                            <a href="ContasPendentes.php">Contas Pendentes</a>
                        </li>
                        <li>
                            <a href="Historico.php">Histórico</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="Grupos.php"><i class="fa fa-dashboard fa-fw"></i> Gerenciador de grupos</a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Meus grupos<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <?php
                        foreach ($gruposMenu as $grupo) {
                            echo
                            "<li>" .
                            "<a href='MinhaRepublica.php?id=" . $grupo->getId() . "'>" . $grupo->getNome() . "</a>" .
                            "</li>";
                        }
                        ?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="../abort.php"><i class="fa fa-dashboard fa-fw"></i> Encerrar Sessão</a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>

