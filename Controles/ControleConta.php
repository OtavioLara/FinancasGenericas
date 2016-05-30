<?php

class ControleConta extends Controle {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    private function geraConta($params) {
        $contaBuilder = new ContaBuilder();
        $contaBuilder->setContadorIntegrantes($params['totalIntegrantesItem']);
        $contaBuilder->setIdsProprietarios($params['idUsuarioProprietario']);
        $contaBuilder->setIdsIntegrantes($params['idUsuarioItem']);
        $contaBuilder->setIdConta($params['idConta']);
        $contaBuilder->setNomeConta($params['nomeConta']);
        $contaBuilder->setNomesItens($params['nomeItem']);
        $contaBuilder->setValorIntegranteItens($params['valorPagoUsuarioItem']);
        $contaBuilder->setValoresProprietarios($params['valorPagoProprietario']);
        $contaBuilder->setValoresItens($params['valorItem']);
        $contaBuilder->setDescricaoAdicional($params['descricaoAdicional']);
        $contaBuilder->setDataAlerta($params["dataAlerta"]);
        $contaBuilder->setData($params['dataConta']);
        return $contaBuilder->gerarConta();
    }

    function insereConta($params) {
        // Gera a conta de acordo com os parâmetros 
        $conta = $this->geraConta($params);

        // Recupera a república que a conta pertence
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republica = $republicaDAO->getRepublicaSimplesPorId($params['idGrupo']);
        $conta->setRepublica($republica);



        // Insere a conta no banco de dados
        $contaDAO = new ContaDAO($this->conexao);
        $idConta = $contaDAO->inserirConta($conta);
        $conta->setId($idConta);

        // Cria notificação da conta para os integrantes
        $nomeRepublica = (isset($republica)) ? $republica->getNome() : "";
        $mensagem = "Conta [" . $conta->getNome() . "] foi criada no grupo " . $nomeRepublica;
        foreach ($conta->getIntegrantes() as $integrante) {
            if ($integrante->getUsuario()->getId() != $params['idCriador']) {
                $this->insereNotificacao("Conta", $integrante->getUsuario(), $conta, $mensagem);
            }
        }

        return $idConta;
    }

    function alteraConta($params) {
        /* Gera a conta de acordo com os parâmetros */
        $conta = $this->geraConta($params);
        $conta->setId($params["idConta"]);

        /* Recupera a república que a conta pertence */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republica = $republicaDAO->getRepublicaSimplesPorId($params['idGrupo']);
        $conta->setRepublica($republica);

        echo $conta->imprimeContaTeste();

        /* Altera conta no banco de dados */
        $contaDAO = new ContaDAO($this->conexao);
        $contaDAO->deletaConta($conta->getId());
        $contaDAO->inserirConta($conta);

        /* Cria notificação */
        $nomeRepublica = (isset($republica)) ? $republica->getNome() : "";
        $mensagem = "Conta [" . $params["nomeAntigoConta"] . "] foi refeita no grupo " . $nomeRepublica;
        foreach ($conta->getIntegrantes() as $integrante) {
            if ($integrante->getUsuario()->getId() != $params['idCriador']) {
                $this->insereNotificacao("Conta", $integrante->getUsuario(), $conta, $mensagem);
            }
        }

        return $params['idConta'];
    }

    function atualizaConta($idRemetente, $idDestinatario, $pagamento, $idConta) {
        $formato = new Formato();
        $pagamento = $formato->numeroControle($pagamento);

        
        // Recupera remetente 
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $remetente = $usuarioDAO->getUsuarioPorId($idRemetente);

        // Recupera conta 
        $contaDAO = new ContaDAO($this->conexao);
        $conta = $contaDAO->getContaSimplesPorIdConta($idConta);

        // Atualiza pagamento no banco de dados 
        $contaDAO->atualizaPagamento($idRemetente, $idDestinatario, $pagamento, $idConta);

        // Cria notificação do pagamento 
        $mensagem = "Seu pagamento na conta [" . $conta->getNome() . "] no valor R$ " . $formato->numeroInterface($pagamento)." foi registrado.";
        $this->insereNotificacao("Conta", $remetente, $conta, $mensagem);
        
    }

    function atualizaDiversasContas($idRemetente, $idDestinatario, $pagamentos, $idContas, $idRequerimento = -1) {
        /* Atualiza todos os pagamentos */
        $formato = new Formato();
        for ($i = 0; $i < count($pagamentos); $i++) {
            $pagamento = $formato->numeroControle($pagamentos[$i]);
            if ($pagamento > 0) {
                $idConta = $idContas[$i];
                $this->atualizaConta($idRemetente, $idDestinatario, $pagamento, $idConta);
            }
        }
        
        /* Caso os pagamentos foram por requerimento, cria notificação de requerimento aceito */
        if ($idRequerimento >= 0) {
            $controleRequerimento = new ControleRequerimento($this->conexao);
            $controleRequerimento->aceitaRequerimento($idRequerimento);
        }
    }

}
