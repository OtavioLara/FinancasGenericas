<?php

if (!defined('__ROOT__')) {
    define('__ROOT__', (dirname(__FILE__)));
}

/* Entidades */
require_once(__ROOT__ . '/Entidades/Conta.php');
require_once(__ROOT__ . '/Entidades/ContaBuilder.php');
require_once(__ROOT__ . '/Entidades/ContaItem.php');
require_once(__ROOT__ . '/Entidades/Convite.php');
require_once(__ROOT__ . '/Entidades/Distribuicao.php');
require_once(__ROOT__ . '/Entidades/Formato.php');
require_once(__ROOT__ . '/Entidades/GraficoBuilder.php');
require_once(__ROOT__ . '/Entidades/Integrante.php');
require_once(__ROOT__ . '/Entidades/IntegrantesConta.php');
require_once(__ROOT__ . '/Entidades/Republica.php');
require_once(__ROOT__ . '/Entidades/Usuario.php');
require_once(__ROOT__ . '/Entidades/Requerimento.php');
require_once(__ROOT__ . '/Entidades/Notificacao.php');

/* Controles */
require_once(__ROOT__ . '/Controles/Controle.php');
require_once(__ROOT__ . '/Controles/ControleConta.php');
require_once(__ROOT__ . '/Controles/ControleConvite.php');
require_once(__ROOT__ . '/Controles/ControleRepublica.php');
require_once(__ROOT__ . '/Controles/ControleRequerimento.php');
require_once(__ROOT__ . '/Controles/ControleUsuario.php');

/* BD */
require_once(__ROOT__ . '/BD/DbConexao.php');
require_once(__ROOT__ . '/BD/DAO.php');
require_once(__ROOT__ . '/BD/ContaDAO.php');
require_once(__ROOT__ . '/BD/RepublicaDAO.php');
require_once(__ROOT__ . '/BD/UsuarioDAO.php');
require_once(__ROOT__ . '/BD/NotificacaoDAO.php');
?>
