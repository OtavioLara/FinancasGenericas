<?php

class ContaDAO extends DAO {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    //#Requerimento
    private function getRequerimento($reg) {
        $id = $reg['Id'];
        $idDestinatario = $reg['IdDestinatario'];
        $idRemetente = $reg['IdRemetente'];
        $valor = $reg['Valor'];
        $situacao = $reg['Situacao'];
        $data = $reg['Data'];
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $destinatario = $usuarioDAO->getUsuarioPorId($idDestinatario);
        $remetente = $usuarioDAO->getUsuarioPorId($idRemetente);
        return new Requerimento($destinatario, $remetente, $valor, $data, $situacao, $id);
    }

    public function insereRequerimento(Requerimento $requerimento) {
        $sql = "Insert into requerimento(IdDestinatario, IdRemetente, Valor, Situacao, Data)"
                . "values ('?1','?2','?3','?4','?5')";
        $sql = str_replace("?1", $requerimento->getDestinatario()->getId(), $sql);
        $sql = str_replace("?2", $requerimento->getRemetente()->getId(), $sql);
        $sql = str_replace("?3", $requerimento->getValor(), $sql);
        $sql = str_replace("?4", $requerimento->getSituacao(), $sql);
        $sql = str_replace("?5", $requerimento->getData()->format('Y-m-d H:i:s'), $sql);
        $this->executaSQL($sql);
        return $this->idInserido();
    }

    private function getRequerimentos($isRemetente, $idUsuario, $situacoes) {
        $sql = "Select * from requerimento where ";
        if ($isRemetente) {
            $sql .= "IdRemetente='$idUsuario' ";
        } else {
            $sql .= "IdDestinatario='$idUsuario' ";
        }
        $sqlSituacao = "";
        if (isset($situacoes) && is_array($situacoes)) {
            $sqlSituacao = " and Situacao in (";
            for ($i = 0; $i < count($situacoes) - 1; $i++) {
                $sqlSituacao .= "'$situacoes[$i]',";
            }
            $sqlSituacao .= "'$situacoes[$i]')";
        } else if (isset($situacoes)) {
            $sqlSituacao = " and Situacao ='$situacoes'";
        }
        $sql .= $sqlSituacao;
        $rs = $this->executaSQL($sql);
        $requerimentos = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $requerimento = $this->getRequerimento($reg);
            array_push($requerimentos, $requerimento);
        }
        return $requerimentos;
    }

    public function getRequerimentoPorId($idRequerimento) {
        $sql = "Select * from requerimento where Id='$idRequerimento'";
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $this->getRequerimento($reg);
        }
        return null;
    }

    public function getRequerimentosDestinatario($idUsuario, $situacoes = null) {
        return $this->getRequerimentos(false, $idUsuario, $situacoes);
    }

    public function getRequerimentosRemetente($idUsuario, $situacoes = null) {
        return $this->getRequerimentos(true, $idUsuario, $situacoes);
    }

    public function atualizaRequerimentoParaRejeitado($idRequerimento) {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date('Y-m-d H:i:s');
        $sql = "Update requerimento set Situacao='R',Data='$data' where Id='$idRequerimento'";
        $this->executaSQL($sql);
    }

    public function atualizaRequerimentoParaAceitado($idRequerimento) {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date('Y-m-d H:i:s');
        $sql = "Update requerimento set Situacao='A',Data='$data' where Id='$idRequerimento'";
        $this->executaSQL($sql);
    }

    public function removeRequerimento($idRequerimento) {
        $sql = "Delete from requerimento where Id='?1'";
        $sql = str_replace("?1", $idRequerimento, $sql);
        $this->executaSQL($sql);
    }

    //#Distribuição
    private function getDistribuicaoReg($reg) {
        $idUsuario = $reg['Id'];
        $email = $reg['Email'];
        $nome = $reg['Nome'];
        $usuario = new Usuario($idUsuario, $nome, $email);
        $valor = $reg['Valor'];
        return new Distribuicao($usuario, $valor);
    }

    private function inserirDistribuicao(Distribuicao $distribuicao, $idContaItem) {
        $sql = "Insert into distribuicao (IdContaItem, IdUsuario, Valor) values ('?1','?2','?3')";
        $sql = str_replace("?1", $idContaItem, $sql);
        $sql = str_replace("?2", $distribuicao->getUsuario()->getId(), $sql);
        $sql = str_replace("?3", $distribuicao->getValor(), $sql);
        $this->executaSQL($sql);
    }

    public function getDistribuicao($idContaItem) {
        $sql = "Select * from distribuicao D inner join usuario U on D.IdUsuario = "
                . " U.Id where D.IdContaItem = '?1'";
        $sql = str_replace("?1", $idContaItem, $sql);
        $rs = $this->executaSQL($sql);
        $distribuicoes = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $distribuicao = $this->getDistribuicaoReg($reg);
            array_push($distribuicoes, $distribuicao);
        }
        return $distribuicoes;
    }

    //#ContaItem
    private function getContaItemReg($reg) {
        $idItem = $reg['Id'];
        $nomeItem = $reg['Nome'];
        $valorItem = $reg['Valor'];
        $distribuicoes = $this->getDistribuicao($idItem);
        return new ContaItem($nomeItem, $valorItem, $distribuicoes, $idItem);
    }

    private function geraSQLInserirContaItem($item, $idConta) {
        $nome = mysql_real_escape_string($item->getNome());
        $sql = "";
        if ($item->getId() >= 0) {
            $sql = "Insert into contaitem (Id,IdConta, Nome, Valor) values (";
            $sql = $sql . "'" . $item->getId() . "',";
        } else {
            $sql = "Insert into contaitem (IdConta, Nome, Valor) values (";
        }
        $sql = $sql . "'" . $idConta . "',";
        $sql = $sql . "'" . $nome . "',";
        $sql = $sql . "'" . $item->getValor() . "')";
        return $sql;
    }

    private function inserirContaItem(ContaItem $item, $idConta) {
        $this->executaSQL($this->geraSQLInserirContaItem($item, $idConta));
        $idContaItem = $this->idInserido($item->getId());
        foreach ($item->getDistribuicoes() as $distribuicao) {
            $this->inserirDistribuicao($distribuicao, $idContaItem);
        }
    }

    public function getContaItem($idConta) {
        $sql = "Select * from contaitem where IdConta='?1'";
        $sql = str_replace("?1", $idConta, $sql);
        $rs = $this->executaSQL($sql);
        $itens = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $item = $this->getContaItemReg($reg);
            array_push($itens, $item);
        }
        return $itens;
    }

    //#IntegranteConta
    private function getIntegranteContaReg($reg) {
        $idUsuario = $reg['Id'];
        $email = $reg['Email'];
        $nome = $reg['Nome'];
        $usuario = new Usuario($idUsuario, $nome, $email);
        $valorPagoConta = $reg['ValorPagoConta'];
        $valorTotalReceber = $reg['ValorTotalReceber'];
        $valorJaRecebido = $reg['ValorJaRecebido'];
        $valorTotalPagar = $reg['ValorTotalPagar'];
        $valorJaPagou = $reg['ValorJaPagou'];
        return new IntegrantesConta($usuario, $valorPagoConta, $valorTotalReceber, $valorJaRecebido, $valorTotalPagar, $valorJaPagou);
    }

    private function inserirIntegrantesConta(IntegrantesConta $integrante, $idConta) {
        $sql = "Insert into integranteconta (IdUsuario, IdConta, ValorPagoConta, "
                . "ValorTotalReceber, ValorJaRecebido, ValorTotalPagar, ValorJaPagou) "
                . "values ('?1','?2','?3','?4','?5','?6','?7')";
        $sql = str_replace("?1", $integrante->getUsuario()->getId(), $sql);
        $sql = str_replace("?2", $idConta, $sql);
        $sql = str_replace("?3", $integrante->getValorPagoConta(), $sql);
        $sql = str_replace("?4", $integrante->getValorTotalReceber(), $sql);
        $sql = str_replace("?5", $integrante->getValorJaRecebido(), $sql);
        $sql = str_replace("?6", $integrante->getValorTotalPagar(), $sql);
        $sql = str_replace("?7", $integrante->getValorJaPagou(), $sql);
        $this->executaSQL($sql);
    }

    public function getIntegrantesConta($idConta) {
        $sql = "Select * from integranteconta IC inner join usuario U on IC.IdUsuario = U.Id"
                . " where IC.IdConta='?1'";
        $sql = str_replace("?1", $idConta, $sql);
        $rs = $this->executaSQL($sql);
        $integrantes = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $integrante = $this->getIntegranteContaReg($reg);
            $integrantes[$integrante->getUsuario()->getId()] = $integrante;
        }
        return $integrantes;
    }

    //#Conta
    private function getContaReg($reg) {
        $idConta = $reg['Id'];
        $nome = $reg['Nome'];
        $valorTotal = $reg['ValorTotal'];
        $data = new DateTime($reg['Data']);


        $republicaDAO = new RepublicaDAO($this->conexao);
        $integrantes = $this->getIntegrantesConta($idConta);
        $republica = $republicaDAO->getRepublicaPorId_Incompleto($reg['IdRepublica']);
        return new Conta($nome, $valorTotal, null, $integrantes, $republica, $data, $idConta);
    }

    private function geraSQLInserirConta(Conta $conta) {
        $nome = mysql_real_escape_string($conta->getNome());
        $sql = "";
        if ($conta->getId() >= 0) {
            $sql = "Insert into conta (Data,Id,Nome, ValorTotal, IdRepublica) values (";
            $sql = $sql . "'" . $conta->getData()->format('Y-m-d H:i:s') . "',";
            $sql = $sql . "'" . $conta->getId() . "',";
        } else {
            date_default_timezone_set('America/Sao_Paulo');
            $sql = "Insert into conta (Data,Nome, ValorTotal, IdRepublica) values (";
            $sql = $sql . "'" . date('Y-m-d H:i:s') . "',";
        }
        $sql = $sql . "'" . $nome . "',";
        $sql = $sql . "'" . $conta->getValorTotal() . "',";
        $sql = $sql . "'" . $conta->getRepublica()->getId() . "')";
        return $sql;
    }

    public function inserirConta(Conta $conta) {
        $this->executaSQL($this->geraSQLInserirConta($conta));
        $idConta = $this->idInserido($conta->getId());
        foreach ($conta->getIntegrantes() as $integrante) {
            $this->inserirIntegrantesConta($integrante, $idConta);
        }
        foreach ($conta->getItens() as $item) {
            $this->inserirContaItem($item, $idConta);
        }
        return $idConta;
    }

    public function getConta($idConta) {
        $sql = "Select * from conta where Id='$idConta'";
        $rs = $this->executaSQL($sql);
        while ($reg = mysqli_fetch_assoc($rs)) {
            return $this->getContaReg($reg);
        }
        return null;
    }

    public function getContaSimples($idConta) {
        $sql = "Select * from conta where Id='$idConta'";
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            $idConta = $reg['Id'];
            $nome = $reg['Nome'];
            $valorTotal = $reg['ValorTotal'];
            $data = new DateTime($reg['Data']);
            return new Conta($nome, $valorTotal, null, null, null, $data, $idConta);
        }
        return null;
    }

    public function getContaSimplesIntegrantes($idConta) {
        $conta = $this->getContaSimples($idConta);
        if (isset($conta)) {
            $integrantes = $this->getIntegrantesConta($conta->getId());
            $conta->setIntegrantes($integrantes);
            return $conta;
        }
        return null;
    }

    public function getHistoricoConta($dataInicio, $dataFim) {
        $sql = "Select Id from conta where Data between '?1' and '?2'"
                . " order by Data,Nome";
        $sql = str_replace("?1", $dataInicio, $sql);
        $sql = str_replace("?2", $dataFim, $sql);
        $rs = $this->executaSQL($sql);
        $contas = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $id = $reg['Id'];
            $conta = $this->getConta($id);
            array_push($contas, $conta);
        }
        return $contas;
    }

    public function getDividas($idReceptor, $idPagante) {
        $sql = "Select C.Id from integranteconta IC inner join conta C on "
                . "C.Id=IC.IdConta where IC.IdUsuario='" . $idPagante . "' "
                . "and IC.ValorTotalPagar > IC.ValorJaPagou and C.Id in"
                . "(Select IdConta from integranteconta where IdUsuario='" . $idReceptor . "' "
                . " and ValorTotalReceber > ValorJaRecebido)";
        $rs = $this->executaSQL($sql);
        $contas = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $id = $reg['Id'];
            $conta = $this->getConta($id);
            array_push($contas, $conta);
        }
        return $contas;
    }

    public function getContasAPagar($idUsuario) {
        $sql = " Select C.Id from integranteconta IC inner join conta C on"
                . " IC.IdConta = C.Id where IC.IdUsuario='" . $idUsuario . "'"
                . " and IC.ValorTotalPagar > IC.ValorJaPagou";
        $rs = $this->executaSQL($sql);
        $contas = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $id = $reg['Id'];
            $conta = $this->getConta($id);
            array_push($contas, $conta);
        }
        return $contas;
    }

    public function getContasAReceber($idUsuario) {
        $sql = " Select C.Id from integranteconta IC inner join conta C on"
                . " IC.IdConta = C.Id where IC.IdUsuario='" . $idUsuario . "'"
                . " and IC.ValorTotalReceber > IC.ValorJaRecebido";
        $rs = $this->executaSQL($sql);
        $contas = array();
        $index = 0;
        while ($reg = mysqli_fetch_assoc($rs)) {
            $id = $reg['Id'];
            $conta = $this->getConta($id);
            array_push($contas, $conta);
        }
        return $contas;
    }

    public function atualizaPagamento($idUsuarioPagando, $idUsuarioRecebendo, $valor, $idConta) {
        $sql = "Update integranteconta set ValorJaPagou = ValorJaPagou + " . $valor
                . " where IdUsuario='" . $idUsuarioPagando . "' and IdConta='"
                . $idConta . "'";
        $this->executaSQL($sql);
        $sql = "Update integranteconta set ValorJaRecebido = ValorJaRecebido + " . $valor
                . " where IdUsuario='" . $idUsuarioRecebendo . "' and IdConta='"
                . $idConta . "'";
        $this->executaSQL($sql);
    }

    public function existeIntegrante($idUsuario, $idConta) {
        $sql = "Select IdUsuario from integranteconta where IdUsuario='" . $idUsuario
                . "' and IdConta='" . $idConta . "'";
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return true;
        }
        return false;
    }

    public function deletaConta($idConta) {
        $sql = "Delete from conta where Id='" . $idConta . "'";
        $this->executaSQL($sql);
    }

}
