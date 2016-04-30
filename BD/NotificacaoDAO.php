<?php

class NotificacaoDAO extends DAO {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    private function getObjetoNotificacao($reg) {
        if ($reg["Tipo"] == "Requerimento") {
            $contaDAO = new ContaDAO($this->conexao);
            return $contaDAO->getRequerimentoPorId($reg["IdTipo"]);
        } else if ($reg["Tipo"] == "Conta") {
            $contaDAO = new ContaDAO($this->conexao);
            return $contaDAO->getContaSimplesPorIdConta($reg["IdTipo"]);
        } else if ($reg["Tipo"] == "Republica") {
            $republicaDAO = new RepublicaDAO($this->conexao);
            return $republicaDAO->getRepublicaSimplesPorId($reg["IdTipo"]);
        }
        return null;
    }

    private function getNotificacao($reg) {
        $id = $reg['Id'];
        $idUsuario = $reg['IdUsuario'];
        $mensagem = $reg['Mensagem'];
        $titulo = $reg['Titulo'];
        $data = new DateTime($reg['Data']);
        $visualizada = $reg['Visualizada'];
        $objeto = $this->getObjetoNotificacao($reg);
        $usuario = new Usuario($idUsuario, "", "");
        return new Notificacao($titulo, $usuario, $objeto, $mensagem, $data, $visualizada, $id);
    }

    public function insereNotificacao(Notificacao $notificacao) {
        if ($notificacao->getObjeto() instanceof Requerimento) {
            $sql = "Insert into notificacoesrequerimento(IdRequerimento,";
        } else if ($notificacao->getObjeto() instanceof Conta) {
            $sql = "Insert into notificacoesconta(IdConta,";
        } else if ($notificacao->getObjeto() instanceof Republica) {
            $sql = "Insert into notificacoesrepublica(IdRepublica,";
        }
        if (isset($sql)) {
            $mensagem = mysqli_real_escape_string($this->conexao, $notificacao->getMensagem());
            $titulo = mysqli_real_escape_string($this->conexao, $notificacao->getTitulo());
            $sql .= "IdUsuario,Titulo, Mensagem,Data,Visualizada) VALUES('?1','?2','?3','?4','?5','?6')";
            $sql = str_replace("?1", $notificacao->getObjeto()->getId(), $sql);
            $sql = str_replace("?2", $notificacao->getUsuario()->getId(), $sql);
            $sql = str_replace("?3", $titulo, $sql);
            $sql = str_replace("?4", $mensagem, $sql);
            $sql = str_replace("?5", $notificacao->getData()->format('Y-m-d H:i:s'), $sql);
            if ($notificacao->isVisualizada()) {
                $sql = str_replace("?6", 1, $sql);
            } else {
                $sql = str_replace("?6", 0, $sql);
            }
            $this->executaSQL($sql);
        }
    }

    public function atualizaNotificacaoParaVisualizada($idNotificacao, $tipoNotificacao) {
        if ($tipoNotificacao == "Requerimento") {
            $sql = "Update notificacoesrequerimento ";
        } else if ($tipoNotificacao == "Conta") {
            $sql = "Update notificacoesconta ";
        } else if ($tipoNotificacao == "Republica") {
            $sql = "Update notificacoesrepublica ";
        }
        $sql .= "set Visualizada = 1 where Id='$idNotificacao'";
        $this->executaSQL($sql);
    }

    public function getNotificacoes($idUsuario, $limiteInicio = -1, $limiteFim = -1) {
        $sql = "Select * from notificacoes where IdUsuario='$idUsuario'";
        if ($limiteInicio >= 0 && $limiteFim >= 0) {
            $sql .= " LIMIT $limiteInicio , $limiteFim";
        }
        $rs = $this->executaSQL($sql);
        $notificacoes = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $notificacao = $this->getNotificacao($reg);
            array_push($notificacoes, $notificacao);
        }
        return $notificacoes;
    }
    
    public function getNotificacoesNaoVisualizadas($idUsuario, $limiteInicio = -1, $limiteFim = -1) {
        $sql = "Select * from notificacoes where IdUsuario='$idUsuario' and Visualizada = false";
        if ($limiteInicio >= 0 && $limiteFim >= 0) {
            $sql .= " LIMIT $limiteInicio , $limiteFim";
        }
        $rs = $this->executaSQL($sql);
        $notificacoes = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $notificacao = $this->getNotificacao($reg);
            array_push($notificacoes, $notificacao);
        }
        return $notificacoes;
    }
    
    public function getQuantidadeNotificacoesNaoVisualizadas($idUsuario) {
        $sql = "Select count(*) as Quantidade from notificacoes where IdUsuario='$idUsuario' and Visualizada='false'";
        $rs = $this->executaSQL($sql);
        $notificacoes = array();
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $reg['Quantidade'];
        }
        return 0;
    }
}
