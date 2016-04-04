<?php
if (!defined('__ROOT__')) {
    define('__ROOT__', (dirname(__FILE__)));
}
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] == false) {
    session_destroy();
    header('Location: ' . __ROOT__ . '/View/Login.php');
} else {
    include "scriptRequires.php";
    $formato = new Formato();
    $usuario = unserialize(base64_decode($_SESSION['Usuario']));
    $conexao = DbConexao::getConnection();
}
?>
