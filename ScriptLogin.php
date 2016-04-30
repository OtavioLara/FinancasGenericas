<?php
if (!defined('__ROOT__')) {
    define('__ROOT__', (dirname(__FILE__)));
}

session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] == false) {
    session_destroy();
    header('Location: Login.php');
} else {
    include "scriptRequires.php";
    date_default_timezone_set('America/Sao_Paulo');
    $formato = new Formato();
    $usuario = unserialize(base64_decode($_SESSION['Usuario']));
    $conexao = DbConexao::getConnection();
}
?>
