<?php

if (isset($_GET['idUsuario'])) {
    include "../../scriptRequires.php";
    $idUsuario = $_GET['idUsuario'];
    $limiteInicio = $_GET['limiteInicio'];
    $limiteFim = $_GET['limiteFim'];
    $notificacaoDAO = new NotificacaoDAO();
    $notificacoes = $notificacaoDAO->getNotificacoes($idUsuario, $limiteInicio, $limiteFim);
    $qtdNotificacoesNaoVisualizadas = 0;
    echo "<ul>";
    foreach ($notificacoes as $notificacao) {
        echo "<li>" . $notificacao->getTitulo() . ": " . $notificacao->getMensagem() . "</li>";
        if (!$notificacao->isVisualizada()) {
            $qtdNotificacoesNaoVisualizadas++;
        }
    }
    echo "<li id='qtdNotificacaoNaoVisualizadas'>" . $qtdNotificacoesNaoVisualizadas . "</li>";
    echo "</ul>";
}
?>
