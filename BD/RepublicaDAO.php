<?php

class RepublicaDAO extends DAO {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    function getRepublica($reg) {
        $id = $reg['Id'];
        $nome = $reg['Nome'];
        return new Republica($id, $nome, null);
    }

    function getIntegranteRepublica($reg) {
        $idUsuario = $reg['IdUsuario'];
        $administrador = $reg['Administrador'];
        $daoUsuario = new UsuarioDAO($this->conexao);
        $usuario = $daoUsuario->getUsuarioPorId($idUsuario);
        return new Integrante($usuario, $administrador);
    }

    function getConvite($reg) {
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $id = $reg['Id'];
        $usuario = $usuarioDAO->getUsuarioPorId($reg['IdUsuario']);
        $republica = $this->getRepublicaPorId_Incompleto($reg['IdRepublica']);
        $destinatario = $reg['Destinatario'];
        return new Convite($republica, $usuario, $destinatario, $id);
    }

    public function inserirRepublica($idCriador, $nomeRepublica) {
        $nomeRepublica = mysql_real_escape_string($nomeRepublica);
        $sql = "Insert into republica (Nome) values ('?1')";
        $sql = str_replace("?1", $nomeRepublica, $sql);
        $this->executaSQL($sql);
        $idRepublica = mysqli_insert_id($this->conexao);
        $sql = "Insert into integrantes(IdUsuario, IdRepublica, Administrador) values ('?1','?2','?3')";
        $sql = str_replace("?1", $idCriador, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        $sql = str_replace("?3", 1, $sql);
        $this->executaSQL($sql);
    }

    public function getRepublicasPorIdUsuario_Incompleto($idUsuario) {
        $sql = "Select R.Nome, R.Id from republica R inner join integrantes I "
                . "on R.Id = I.IdRepublica where I.IdUsuario = '?1'";
        $sql = str_replace("?1", $idUsuario, $sql);
        $rs = $this->executaSQL($sql);
        $republicas = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $republica = $this->getRepublica($reg);
            array_push($republicas, $republica);
        }
        return $republicas;
    }

    public function getRepublicaPorIdUsuario_Completa($idUsuario) {
        $republica = $this->getRepublicaPorIdUsuario_Incompleto($idUsuario);
        if (isset($republica)) {
            $republica->setIntegrantes($this->getIntegrantesRepublica($republica->getId()));
        }
        return $republica;
    }

    public function getRepublicaPorId_Incompleto($idRepublica) {
        $sql = "Select * from republica where Id='?1'";
        $sql = str_replace("?1", $idRepublica, $sql);
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $this->getRepublica($reg);
        }
        return null;
    }

    public function getRepublicaPorId_Completa($idRepublica) {
        $republica = $this->getRepublicaPorId_Incompleto($idRepublica);
        if (isset($republica)) {
            $republica->setIntegrantes($this->getIntegrantesRepublica($republica->getId()));
        }
        return $republica;
    }

    public function isAdministrador($idUsuario, $idRepublica) {
        $sql = "Select Administrador from integrantes where IdUsuario='?1' and IdRepublica='?2'";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $reg['Administrador'];
        }
        return false;
    }

    public function setIntegranteAdministrador($idUsuario, $idRepublica, $isAdministrador) {
        $sql = "Update integrantes set Administrador = '?1' where IdUsuario='?2' and IdRepublica='?3'";
        if ($isAdministrador) {
            $sql = str_replace("?1", "1", $sql);
        } else {
            $sql = str_replace("?1", "0", $sql);
        }
        $sql = str_replace("?2", $idUsuario, $sql);
        $sql = str_replace("?3", $idRepublica, $sql);
        $this->executaSQL($sql);
       
    }

    public function removeIntegrante($idUsuario, $idRepublica) {
        $sql = "Delete from integrantes where IdUsuario='?1' and IdRepublica='?2'";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        $this->executaSQL($sql);
    }

    public function inserirIntegrante($idRepublica, $idUsuario) {
        $sql = "Insert into integrantes(IdUsuario, IdRepublica, Administrador) values (?1,?2,?3)";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        $sql = str_replace("?3", "false", $sql);
        $this->executaSQL($sql);
    }

    public function inserirConvite($idRepublica, $idUsuario, $destinatario) {
        $sql = "Insert into convite (IdRepublica, IdUsuario, Destinatario) values ('?1','?2','?3')";
        $sql = str_replace("?1", $idRepublica, $sql);
        $sql = str_replace("?2", $idUsuario, $sql);
        $sql = str_replace("?3", $destinatario, $sql);
        $this->executaSQL($sql);
    }

    public function getConvitePorId($idConvite) {
        $sql = "Select * from convite where Id='$idConvite'";
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $this->getConvite($reg);
        }
        return null;
    }

    public function getConviteParaUsuario($idUsuario) {
        $sql = "Select * from convite where IdUsuario = '?1' and Destinatario='?2'";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", "U", $sql);
        $rs = $this->executaSQL($sql);
        $convites = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $convite = $this->getConvite($reg);
            array_push($convites, $convite);
        }
        return $convites;
    }

    public function removeConviteDeUsuario($idRepublica, $idUsuario) {
        $sql = "Delete from convite where IdUsuario='?1' and IdRepublica='?2' ";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        $this->executaSQL($sql);
    }

    public function getIntegrantesRepublica($idRepublica) {
        $sql = "Select * from integrantes where IdRepublica='?1'";
        $sql = str_replace("?1", $idRepublica, $sql);
        $rs = $this->executaSQL($sql);
        $integrantes = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $integrante = $this->getIntegranteRepublica($reg);
            $integrantes[$integrante->getUsuario()->getId()] = $integrante;
        }
        return $integrantes;
    }

}
