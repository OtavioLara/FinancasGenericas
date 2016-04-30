<?php

class ControleRequerimento extends Controle {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    function insereRequerimento($idRemetente, $idDestinatario, $valor) {
        $formato = new Formato();
        $valor = $formato->numeroControle($valor);

        /* Recupera data atual */
        date_default_timezone_set('America/Sao_Paulo');
        $data = new DateTime(date('Y-m-d H:i:s'));

        /* Recupera destinatário */
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $destinatario = $usuarioDAO->getUsuarioPorId($idDestinatario);
        $remetente = $usuarioDAO->getUsuarioPorId($idRemetente);

        /* Cadastra requerimento */
        $contaDAO = new ContaDAO($this->conexao);
        $requerimento = new Requerimento($destinatario, $remetente, $valor, $data, 'L');
        $id = $contaDAO->insereRequerimento($requerimento);
        $requerimento->setId($id);
        

        /* Cria notificação */
        $mensagem = $remetente->getNome() . " enviou um requerimento no valor de R$ " . $formato->numeroInterface($valor);
        $this->insereNotificacao("Requerimento",$destinatario, $requerimento, $mensagem);
    }

    function aceitaRequerimento($idRequerimento) {
        $formato = new Formato();
        /* Atualiza requerimento para aceitado */
        $contaDAO = new ContaDAO($this->conexao);
        $contaDAO->atualizaRequerimentoParaAceitado($idRequerimento);

        /* Recupera requerimento */
        $requerimento = $contaDAO->getRequerimentoPorId($idRequerimento);

        /* Cria notificação sobre o requerimento */
        $mensagem = "Seu requerimento para " . $requerimento->getDestinatario()->getNome()
                . " no valor de R$" . $formato->numeroInterface($requerimento->getValor())
                . " foi aceito";
        $this->insereNotificacao("Requerimento",$requerimento->getRemetente(), $requerimento, $mensagem);
    }

    function rejeitaRequerimento($idRequerimento) {
        $formato = new Formato();
        /* Atualiza requerimento para rejeitado */
        $contaDAO = new ContaDAO($this->conexao);
        $contaDAO->atualizaRequerimentoParaRejeitado($idRequerimento);

        /* Recupera requerimento */
        $requerimento = $contaDAO->getRequerimentoPorId($idRequerimento);

        /* Cria notificação */
        $mensagem = "Requerimento para " . $requerimento->getDestinatario()->getNome()
                . " no valor de R$" . $formato->numeroInterface($requerimento->getValor())
                . " foi reijeitado";
        $this->insereNotificacao("Requerimento",$requerimento->getRemetente(), $requerimento, $mensagem);
    }

}

?>
