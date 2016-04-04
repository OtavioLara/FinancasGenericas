<?php

include "../../scriptRequires.php";
$conexao = DbConexao::getConnection();
if (isset($conexao)) {
    $usuarioDAO = new UsuarioDAO($conexao);
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $usuario = $usuarioDAO->verificaSenha($email, $senha);
    if ($usuario != null) {
        session_start();
        $_SESSION['logado'] = true;
        $_SESSION['Usuario'] = base64_encode(serialize($usuario));
        header('Location: ../Perfil.php');
    } else {
        header('Location: ../Login.php?login=false');
    }
}
?>