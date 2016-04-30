<?php

if (isset($_GET['idUsuario'])) {
    include "../../scriptRequires.php";
    $idUsuario = $_GET['idUsuario'];
    $notificacaoDAO = new NotificacaoDAO();
    echo $notificacaoDAO->getQuantidadeNotificacoesNaoVisualizadas($idUsuario);
}

