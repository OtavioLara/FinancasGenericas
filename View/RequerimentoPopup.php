<?php
$contaDAO = new ContaDAO($conexao);
$requerimento = $contaDAO->getUmRequerimentoDestinatario($usuario->getId());

if (isset($requerimento)) {
    $pagante = $requerimento->getRemetente();
    $dividas = $contaDAO->getDividas($requerimento->getDestinatario()->getId(), $requerimento->getRemetente()->getId());
    echo "<script> " .
    "$(function () {" .
    "  $('#modalRequerimento').modal('show');" .
    "});" .
    "</script> ";
}
?>
