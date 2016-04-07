<?php

class Controle {

    protected $conexao;

    function __construct($conexao = null) {
        if (!isset($conexao)) {
            $conexao = DbConexao::getConnection();
        }
        $this->conexao = $conexao;
    }

    protected function insereNotificacao($titulo, $usuario, $objeto, $mensagem, $data = null) {
        if (!isset($data)) {
            date_default_timezone_set('America/Sao_Paulo');
            $data = new DateTime(date('Y-m-d H:i:s'));
        }
        $notificacaoDAO = new NotificacaoDAO($this->conexao);
        $notificacao = new Notificacao($titulo, $usuario, $objeto, $mensagem, $data);
        $notificacaoDAO->insereNotificacao($notificacao);
    }

}

?>
