<?php

class ControleConta extends Controle {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    private function geraConta($params) {
        $contaBuilder = new ContaBuilder();
        $contaBuilder->setContadorIntegrantes($params['contadorDistribuicao']);
        $contaBuilder->setIdsProprietarios($params['idProp']);
        $contaBuilder->setIdsIntegrantes($params['idDistribuicaoItem']);
        $contaBuilder->setNomeConta($params['nomeConta']);
        $contaBuilder->setNomesItens($params['nomeItem']);
        $contaBuilder->setValorIntegranteItens($params['valorDistribuicaoItem']);
        $contaBuilder->setValoresProprietarios($params['valorProp']);
        $contaBuilder->setValoresItens($params['valorItem']);
        $contaBuilder->setDescricaoAdicional($params['descricaoAdicional']);
        return $contaBuilder->gerarConta();
    }

    function insereConta($params) {
        /* Gera a conta de acordo com os parâmetros */
        $conta = $this->geraConta($params);

        /* Recupera a república que a conta pertence */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republica = $republicaDAO->getRepublicaPorId_Incompleto($params['idRepublica']);
        $conta->setRepublica($republica);

        /* Insere a conta no banco de dados */
        $contaDAO = new ContaDAO($this->conexao);
        $idConta = $contaDAO->inserirConta($conta);
        $conta->setId($idConta);

        /* Cria notificação da conta para os integrantes */
        $mensagem = "Conta [" . $conta->getNome() . "] foi criada no grupo " . $republica->getNome();
        foreach ($conta->getIntegrantes() as $integrante) {
            $this->insereNotificacao($integrante->getUsuario(), $conta, $mensagem);
        }

        return $idConta;
    }

    function alteraConta($params) {
        /* Gera a conta de acordo com os parâmetros */
        $conta = $this->geraConta($params);
        $conta->setId($params["idConta"]);
        $conta->setData(new DateTime($params["data"]));
        /* Recupera a república que a conta pertence */
        $republicaDAO = new RepublicaDAO($this->conexao);
        $republica = $republicaDAO->getRepublicaPorId_Incompleto($params['idRepublica']);
        $conta->setRepublica($republica);

        /* Altera conta no banco de dados */
        $contaDAO = new ContaDAO($this->conexao);
        $contaDAO->deletaConta($conta->getId());
        $contaDAO->inserirConta($conta);
        
        /* Cria notificação */
        $mensagem = "Conta [" . $params["nomeAntigo"] . "] foi alterada " . $republica->getNome();
        foreach ($conta->getIntegrantes() as $integrante) {
            $this->insereNotificacao($integrante->getUsuario(), $conta, $mensagem);
        }
        
        return $params['idConta'];
    }

    function atualizaConta($idRemetente, $idDestinatario, $pagamento, $idConta) {
        $formato = new Formato();
        $pagamento = $formato->numeroControle($pagamento);

        /* Recupera remetente */
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $remetente = $usuarioDAO->getUsuarioPorId($idRemetente);

        /* Recupera conta */
        $contaDAO = new ContaDAO($this->conexao);
        $conta = $contaDAO->getContaSimples($idConta);

        /* Atualiza pagamento no banco de dados */
        $contaDAO->atualizaPagamento($idRemetente, $idDestinatario, $pagamento, $idConta);

        /* Cria notificação do pagamento */
        $mensagem = "Conta [" . $conta->getNome() . "] atualizada ";
        $this->insereNotificacao($remetente, $conta, $mensagem);
    }

    function atualizaDiversasContas($idRemetente, $idDestinatario, $pagamentos, $idContas, $idRequerimento = -1) {
        /* Atualiza todos os pagamentos */
        $formato = new Formato();
        for ($i = 0; $i < count($pagamentos); $i++) {
            $pagamento = $formato->numeroControle($pagamentos[$i]);
            $idConta = $idContas[$i];
            $this->atualizaConta($idRemetente, $idDestinatario, $pagamento, $idConta);
        }
        /* Caso os pagamentos foram por requerimento, cria notificação de requerimento aceito */
        if ($idRequerimento >= 0) {
           $controleRequerimento = new ControleRequerimento($this->conexao);
           $controleRequerimento->aceitaRequerimento($idRequerimento);
        }
    }

}
