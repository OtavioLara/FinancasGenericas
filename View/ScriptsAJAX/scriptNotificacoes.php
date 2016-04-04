<?php


if (isset($_GET['idUsuario'])) {
    include "../../scriptRequires.php";
    $idUsuario = $_GET['idUsuario'];
    $limiteInicio = $_GET['limiteInicio'];
    $limiteFim = $_GET['limiteFim'];
    $notificacaoDAO = new NotificacaoDAO();
    $notificacoes = $notificacaoDAO->getNotificacoes($idUsuario, $limiteInicio, $limiteFim);
    foreach ($notificacoes as $notificacao) {
        echo $notificacao->getMensagem() . "<br/>";
    }
}
?>
