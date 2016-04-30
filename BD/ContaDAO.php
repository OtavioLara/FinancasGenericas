<?php

class ContaDAO extends DAO {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    /* Pesquisas de conta */

    /* Aliases
     * Conta: IdConta, NomeConta, ValorTotal, IdRepublica, Data, DescricaoAdicional, DataAlerta
     * República: IdRepublica, NomeRepublica
     * Item: IdContaItem, IdConta, NomeContaItem, ValorContaItem
     * Distribuição: IdDistribuicao, IdContaItem, IdUsuarioDistribuicao, ValorDistribuicao
     * Usuário(Distribuição): NomeUsuarioDistribuicao, EmailDistribuicao
     * Usuário(Integrante): NomeUsuarioIntegrante, EmailIntegrante
     */

    public function getSQLPesquisaConta($innerRepublica, $innerItem, $innerDistribuicao, $innerUsuarioDistribuicao, $innerIntegrante, $innerUsuarioIntegrante) {
        $select = "Select C.Id as IdConta, C.Nome as NomeConta, C.DescricaoAdicional, C.ValorTotal, C.Data, C.DataAlerta ";
        $innerJoin = "";
        if ($innerRepublica) {
            $select.= ",R.Nome as NomeRepublica, R.Id as IdRepublica ";
            $innerJoin .= "left join republica R on R.Id = C.IdRepublica ";
        }
        if ($innerItem) {
            $select .= ",CI.Id as IdContaItem, CI.Nome as NomeContaItem, CI.Valor as ValorContaItem ";
            $innerJoin .= "inner join contaitem CI on CI.IdConta = C.Id ";
            if ($innerDistribuicao) {
                $select .= ",D.Valor as ValorDistribuicao, D.IdUsuario as IdUsuarioDistribuicao ";
                $innerJoin .= "inner join distribuicao D on D.IdContaItem = CI.Id ";
                if ($innerUsuarioDistribuicao) {
                    $select .= ",U.Nome as NomeUsuarioDistribuicao, U.Email as EmailDistribuicao ";
                    $innerJoin .= "inner join usuario U on U.Id = D.IdUsuario ";
                }
            }
        }
        if ($innerIntegrante) {
            $select .= ",IC.ValorPagoConta, IC.ValorTotalReceber, IC.ValorJaRecebido, IC.ValorTotalPagar, IC.ValorJaPagou,IC.IdUsuario as IdUsuarioIntegrante ";
            $innerJoin .= "inner join integranteconta IC on IC.IdConta = C.Id ";
            if ($innerUsuarioIntegrante) {
                $select .= ",UI.Nome as NomeUsuarioIntegrante, UI.Email as EmailIntegrante ";
                $innerJoin .= "inner join usuario UI on UI.Id = IC.IdUsuario ";
            }
        }
        return $select . " from conta C " . $innerJoin;
    }

    /* Função que obtem qualquer conta */

    public function getAtributosRepublica($reg) {
        $id = $reg["IdRepublica"];
        $nome = $reg["NomeRepublica"];
        return new Republica($id, $nome, null);
    }

    public function getAtributosConta($reg) {
        $idConta = $reg['IdConta'];
        $nome = $reg['NomeConta'];
        $descricaoAdicional = $reg['DescricaoAdicional'];
        $valorTotal = $reg['ValorTotal'];
        $data = new DateTime($reg['Data']);
        if (isset($reg["DataAlerta"])) {
            $dataAlerta = new DateTime($reg['DataAlerta']);
        } else {
            $dataAlerta = null;
        }
        $conta = new Conta($nome, $dataAlerta, $descricaoAdicional, $valorTotal, $data);
        $conta->setId($idConta);
        return $conta;
    }

    public function getAtributosContaItem($reg) {
        $idItem = $reg['IdContaItem'];
        $nomeItem = $reg['NomeContaItem'];
        $valorItem = $reg['ValorContaItem'];
        $contaItem = new ContaItem($nomeItem, $valorItem);
        $contaItem->setId($idItem);
        return $contaItem;
    }

    public function getAtributosDistribuicao($reg, $innerUsuario) {
        $idUsuario = $reg["IdUsuarioDistribuicao"];
        $valor = $reg["ValorDistribuicao"];
        if ($innerUsuario) {
            $nomeUsuario = $reg["NomeUsuarioDistribuicao"];
            $email = $reg["EmailDistribuicao"];
            $usuario = new Usuario($idUsuario, $nomeUsuario, $email);
        } else {
            $usuario = new Usuario($idUsuario, "", "");
        }
        return new Distribuicao($usuario, $valor);
    }

    public function getAtributosIntegranteConta($reg, $innerUsuario) {
        $idUsuario = $reg["IdUsuarioIntegrante"];
        $valorPago = $reg["ValorPagoConta"];
        $valorTotalReceber = $reg["ValorTotalReceber"];
        $valorJaRecebido = $reg["ValorJaRecebido"];
        $valorTotalPagar = $reg["ValorTotalPagar"];
        $valorJaPagou = $reg["ValorJaPagou"];
        if ($innerUsuario) {
            $nomeUsuario = $reg["NomeUsuarioIntegrante"];
            $email = $reg["EmailIntegrante"];
            $usuario = new Usuario($idUsuario, $nomeUsuario, $email);
        } else {
            $usuario = new Usuario($idUsuario, "", "");
        }
        return new IntegrantesConta($usuario, $valorPago, $valorTotalReceber, $valorJaRecebido, $valorTotalPagar, $valorJaPagou);
    }

    public function getArrayContas($clausulaWhere, $innerRepublica, $innerItem, $innerDistribuicao, $innerUsuarioDistribuicao, $innerIntegrante, $innerUsuarioIntegrante, $nomeFuncao = null) {
        $sql = $this->getSQLPesquisaConta($innerRepublica, $innerItem, $innerDistribuicao, $innerUsuarioDistribuicao, $innerIntegrante, $innerUsuarioIntegrante);
        $sql .= $clausulaWhere;
        $rs = $this->executaSQL($sql);
        $reg = mysqli_fetch_assoc($rs);
        $contas = array();
        while (isset($reg)) {
            $idConta = $reg["IdConta"];
            /* Obtem Conta */
            if (!isset($contas[$idConta])) {
                $contas[$idConta] = $this->getAtributosConta($reg);
                if ($innerRepublica && isset($reg["IdRepublica"])) {
                    $republica = $this->getAtributosRepublica($reg);
                    $contas[$idConta]->setRepublica($republica);
                }
            }
            /* Obtem item */
            if ($innerItem) {
                $idItem = $reg["IdContaItem"];
                if (!$contas[$idConta]->possuiItem($idItem)) {
                    $item = $this->getAtributosContaItem($reg);
                    $contas[$idConta]->adicionaItem($item);
                }
                if ($innerDistribuicao) {
                    $idUsuario = $reg["IdUsuarioDistribuicao"];
                    if (!$contas[$idConta]->getContaItem($idItem)->possuiUsuario($idUsuario)) {
                        $distribuicao = $this->getAtributosDistribuicao($reg, $innerUsuarioDistribuicao);
                        $contas[$idConta]->getContaItem($idItem)->adicionaDistribuicao($distribuicao);
                    }
                }
            }
            if ($innerIntegrante) {
                $idUsuario = $reg["IdUsuarioIntegrante"];
                if (!$contas[$idConta]->possuiIntegrante($idUsuario)) {
                    $integrante = $this->getAtributosIntegranteConta($reg, $innerUsuarioIntegrante);
                    $contas[$idConta]->adicionaIntegrante($integrante);
                }
            }
            $reg = mysqli_fetch_assoc($rs);
        }
        return $contas;
    }

    public function getContasAlerta($idUsuario, $limiteInicio = null, $limiteFim = null) {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date('Y-m-d');
        $where = " where IC.ValorTotalPagar > IC.ValorJaPagou and '$data' >= C.DataAlerta and IC.IdUsuario='$idUsuario' ";
        if (isset($limiteInicio) && isset($limiteFim)) {
            $where.= "LIMIT" . $limiteInicio . "," . $limiteFim;
        }
        $contasAlerta = $this->getArrayContas($where, false, false, false, false, true, false, "contasAlerta");
        return $contasAlerta;
    }

    public function getValoresDividas($idUsuario) {
        $sql = "Select sum(ValorTotalPagar - ValorJaPagou) as ValorAPagar, sum(ValorTotalReceber - ValorJaRecebido) as ValorAReceber " .
                "from integranteconta " .
                "where IdUsuario='$idUsuario'";
        $rs = $this->executaSQL($sql);
        $reg = mysqli_fetch_assoc($rs);
        $valores = array();
        if (isset($reg)) {
            $valores[0] = $reg["ValorAPagar"];
            $valores[1] = $reg["ValorAReceber"];
        }
        return $valores;
    }

    public function getContasSimplesPorIdUsuario($idUsuario, $innerRepublica) {
        $where = " where C.Id in (Select IdConta from integranteconta where IdUsuario='$idUsuario')";
        $contas = $this->getArrayContas($where, $innerRepublica, false, false, false, false, false, "contasSimplesPorIdUsuario");
        return $contas;
    }

    public function getContaSimplesPorIdConta($idConta) {
        $where = " where C.Id='$idConta'";
        $contas = $this->getArrayContas($where, false, false, false, false, false, false, "contaSimplesPorIdConta");
        if (count($contas) > 0) {
            return reset($contas);
        } else {
            return null;
        }
    }

    public function getContaCompletaPorIdConta($idConta) {
        $where = " where C.Id='$idConta'";
        $contas = $this->getArrayContas($where, true, true, true, true, true, true, "contaSompletaPorIdConta");
        if (count($contas) > 0) {
            return reset($contas);
        } else {
            return null;
        }
    }

    public function getContasPendentes($idUsuario) {
        $where = "where C.Id in" .
                "(Select IdConta from integranteconta where IdUsuario='$idUsuario' " .
                "and (ValorTotalPagar > ValorJaPagou or ValorTotalReceber > ValorJaRecebido))";
        $contas = $this->getArrayContas($where, true, false, false, false, true, true, "contasPendentes");
        return $contas;
    }

    public function getHistoricoConta($idUsuario, $nome = null, $dataInicio = null, $dataFim = null, $idGrupo = null, $limiteInicio = null, $limiteFim = null) {
        $where = "where IC.IdUsuario='$idUsuario' ";
        if (isset($nome)) {
            $where .= "and C.Nome LIKE '%$nome%' ";
        }
        if (isset($idGrupo) && $idGrupo > 0) {
            $where .= "and C.IdRepublica='$idGrupo' ";
        }
        if (isset($dataInicio) && isset($dataFim) && $dataInicio != "" && $dataFim != "") {
            $where .= " and C.Data between '$dataInicio' and '$dataFim' ";
        } else if (isset($dataInicio) && $dataInicio != "") {
            $where .= " and C.Data >= '$dataInicio' ";
        } else if (isset($dataFim) && $dataFim != "") {
            $where .= " and C.Data <= '$dataFim' ";
        }
        $where .= "ORDER BY Data DESC";
        if (isset($limiteInicio) && isset($limiteFim)) {
            $where .= " LIMIT " . $limiteInicio . "," . $limiteFim;
        }
        $contas = $this->getArrayContas($where, true, false, false, false, true, false, "historicoConta");
        return $contas;
    }

    public function getDividas($idReceptor, $idPagante) {
        $where = "where C.Id in" .
                "(Select IC1.IdConta from integranteconta IC1 " .
                "left join integranteconta IC2 on IC1.IdConta = IC2.IdConta " .
                "where IC1.IdUsuario='$idPagante' " .
                "and IC1.ValorTotalPagar > IC1.ValorJaPagou " .
                "and IC2.IdUsuario='$idReceptor' and IC2.ValorTotalReceber > IC2.ValorJaRecebido " .
                "and IC2.IdConta IS NOT NULL)";
        $contas = $this->getArrayContas($where, false, false, false, false, true, false, "dividas");
        return $contas;
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

    public function getUmRequerimentoDestinatario($idUsuario) {
        $sql = "Select * from requerimento where IdDestinatario='$idUsuario' and Situacao='L' LIMIT 01";
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

    //#ContaItem


    private function geraSQLInserirContaItem($item, $idConta) {
        $nome = mysqli_real_escape_string($this->conexao, $item->getNome());
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

    //#Conta
    private function geraSQLInserirConta(Conta $conta) {
        $nome = mysqli_real_escape_string($this->conexao, $conta->getNome());
        $descricao = mysqli_real_escape_string($this->conexao, $conta->getDescricaoAdicional());
        $sql = "";
        if ($conta->getId() >= 1) {
            $sql = "Insert into conta (Data,Id,Nome, DescricaoAdicional, ValorTotal, IdRepublica, DataAlerta) values (";
            $sql = $sql . "'" . $conta->getData()->format('Y-m-d H:i:s') . "',";
            $sql = $sql . "'" . $conta->getId() . "',";
        } else {
            date_default_timezone_set('America/Sao_Paulo');
            $sql = "Insert into conta (Data,Nome, DescricaoAdicional, ValorTotal, IdRepublica, DataAlerta) values (";
            $sql = $sql . "'" . date('Y-m-d H:i:s') . "',";
        }
        $sql = $sql . "'" . $nome . "',";
        $sql = $sql . "'" . $descricao . "',";
        $sql = $sql . "'" . $conta->getValorTotal() . "',";
        $republica = $conta->getRepublica();
        if (isset($republica)) {
            $sql = $sql . "'" . $conta->getRepublica()->getId() . "',";
        } else {
            $sql = $sql . "NULL,";
        }

        $dataAlerta = $conta->getDataAlerta();
        if (isset($dataAlerta)) {
            $sql = $sql . "'" . $conta->getDataAlerta()->format('Y-m-d H:i:s') . "')";
        } else {
            $sql = $sql . "NULL)";
        }

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

    /* Pesquisas */

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
