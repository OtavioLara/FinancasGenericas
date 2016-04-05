<?php

class NotificacaoDAO extends DAO {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    private function getObjetoNotificacao($reg) {
        if ($reg["Tipo"] == "Conta") {
            $contaDAO = new ContaDAO($this->conexao);
            return $contaDAO->getRequerimentoPorId($reg["IdTipo"]);
        } else if ($reg["Tipo"] == "Republica") {
            $contaDAO = new ContaDAO($this->conexao);
            return $contaDAO->getContaSimples($reg["IdTipo"]);
        } else if ($reg["Tipo"] == "Requerimento") {
            $republicaDAO = new RepublicaDAO($this->conexao);
            return $republicaDAO->getRepublicaPorId_Incompleto($reg["IdTipo"]);
        }
        return null;
    }

    private function getNotificacao($reg) {
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $id = $reg['Id'];
        $idUsuario = $reg['IdUsuario'];
        $mensagem = $reg['Mensagem'];
        $data = new DateTime($reg['Data']);
        $visualizada = $reg['Visualizada'];
        $objeto = $this->getObjetoNotificacao($reg);
        $usuario = $usuarioDAO->getUsuarioPorId($idUsuario);
        return new Notificacao($usuario, $objeto, $mensagem, $data, $visualizada, $id);
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
            $mensagem = $nome = mysql_real_escape_string($notificacao->getMensagem());
            $sql .= "IdUsuario,Mensagem,Data,Visualizada) VALUES('?1','?2','?3','?4','?5')";
            $sql = str_replace("?1", $notificacao->getObjeto()->getId(), $sql);
            $sql = str_replace("?2", $notificacao->getUsuario()->getId(), $sql);
            $sql = str_replace("?3", $mensagem, $sql);
            $sql = str_replace("?4", $notificacao->getData()->format('Y-m-d H:i:s'), $sql);
            if ($notificacao->isVisualizada()) {
                $sql = str_replace("?5", 1, $sql);
            } else {
                $sql = str_replace("?5", 0, $sql);
            }
            $this->executaSQL($sql);
        }
    }

    public function atualizaNotificacaoParaVisualizada($idNotificacao, $tipoNotificacao) {
        if ($tipoNotificacao == "Requerimento") {
            $sql = "Update requerimento ";
        } else if ($tipoNotificacao == "Conta") {
            $sql = "Update conta ";
        } else if ($tipoNotificacao == "Republica") {
            $sql = "Update republica ";
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
