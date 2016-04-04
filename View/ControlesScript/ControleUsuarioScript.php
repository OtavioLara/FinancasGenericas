<?php
include "../../scriptRequires.php";
$comando = $_POST['comando'];
$controleUsuario = new ControleUsuario();

if ($comando == "inserir") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $controleUsuario->insereUsuario($nome, $email, $senha);
    header('Location: ../Login.php?contaCriada=true');
}

?>
