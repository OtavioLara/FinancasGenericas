<?php

if (isset($_GET['idUsuario'])) {
    include "../../scriptRequires.php";
    $idUsuario = $_GET['idUsuario'];
    $limiteInicio = $_GET['limiteInicio'];
    $limiteFim = $_GET['limiteFim'];
    $notificacaoDAO = new NotificacaoDAO();
    $notificacoes = $notificacaoDAO->getNotificacoesNaoVisualizadas($idUsuario, $limiteInicio, $limiteFim);
    foreach ($notificacoes as $notificacao) {
        if (!$notificacao->isVisualizada()) {
            $notificacaoDAO->atualizaNotificacaoParaVisualizada($notificacao->getId(), $notificacao->getTipoNofitificacao());
        }
    }
    echo json_encode($notificacoes);
}
?>
