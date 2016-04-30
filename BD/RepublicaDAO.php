<?php

class RepublicaDAO extends DAO {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    /* Obtem qualquer republica */

    function getSQLPesquisaRepublica($innerIntegrantes, $innerUsuario) {
        $select = "Select R.Id as IdRepublica, R.Nome as NomeRepublica ";
        $innerJoin = "";
        if ($innerIntegrantes) {
            $select .= ",I.IdUsuario, I.Administrador ";
            $innerJoin .= " left join integrantes I on I.IdRepublica = R.Id ";
            if ($innerUsuario) {
                $select .= ",U.Nome as NomeUsuario, U.Email ";
                $innerJoin .= " left join usuario U on U.Id = I.IdUsuario ";
            }
        }
        return $select . " from Republica R " . $innerJoin;
    }

    function getAtributosRepublica($reg) {
        $id = $reg["IdRepublica"];
        $nome = $reg["NomeRepublica"];
        return new Republica($id, $nome);
    }

    function getAtributosIntegrante($reg, $innerUsuario) {
        $idUsuario = $reg["IdUsuario"];
        $administrador = $reg["Administrador"];
        if ($innerUsuario) {
            $nomeUsuario = $reg["NomeUsuario"];
            $email = $reg["Email"];
            $usuario = new Usuario($idUsuario, $nomeUsuario, $email);
        } else {
            $usuario = new Usuario($idUsuario, "", "");
        }
        return new Integrante($usuario, $administrador);
    }

    function getArrayRepublicas($clausulaWhere, $innerIntegrantes, $innerUsuario, $function = null) {
        $sql = $this->getSQLPesquisaRepublica($innerIntegrantes, $innerUsuario);
        $sql .= $clausulaWhere;
        //echo $sql . " " . $function;
        $rs = $this->executaSQL($sql);
        $reg = mysqli_fetch_assoc($rs);
        $republicas = array();
        while (isset($reg)) {
            $idRepublica = $reg["IdRepublica"];
            if (!isset($republicas[$idRepublica])) {
                $republicas[$idRepublica] = $this->getAtributosRepublica($reg);
            }
            if ($innerIntegrantes && isset($reg["IdUsuario"])) {
                $idUsuario = $reg["IdUsuario"];
                if (!$republicas[$idRepublica]->possuiIntegrante($idUsuario)) {
                    $integrante = $this->getAtributosIntegrante($reg, $innerUsuario);
                    $republicas[$idRepublica]->adicionaIntegrante($integrante);
                }
            }
            $reg = mysqli_fetch_assoc($rs);
        }
        return $republicas;
    }

    function getConvite($reg) {
        $usuarioDAO = new UsuarioDAO($this->conexao);
        $id = $reg['Id'];
        $usuario = $usuarioDAO->getUsuarioPorId($reg['IdUsuario']);
        $republica = $this->getRepublicaSimplesPorId($reg['IdRepublica']);
        $destinatario = $reg['Destinatario'];
        return new Convite($republica, $usuario, $destinatario, $id);
    }

    /* inserções */

    public function inserirRepublica(Republica $republica, $idCriador) {
        /* Cria república */
        $nomeRepublica = mysql_real_escape_string($republica->getNome());
        $sql = "Insert into republica (Nome) values ('?1')";
        $sql = str_replace("?1", $nomeRepublica, $sql);
        $this->executaSQL($sql);
        $idRepublica = mysqli_insert_id($this->conexao);

        /* Adiciona criador na república */
        $this->inserirIntegrante($idRepublica, $idCriador, true);

        /* Manda convite para os demais */
        foreach ($republica->getIntegrantes() as $integrante) {
            if ($integrante->getUsuario()->getId() != $idCriador) {
                $this->inserirConvite($idRepublica, $integrante->getUsuario()->getId(), 'U');
            }
        }
        return $idRepublica;
    }

    public function inserirIntegrante($idRepublica, $idUsuario, $admin) {
        $sql = "Insert into integrantes(IdUsuario, IdRepublica, Administrador) values (?1,?2,?3)";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        if ($admin) {
            $sql = str_replace("?3", "true", $sql);
        } else {
            $sql = str_replace("?3", "false", $sql);
        }

        $this->executaSQL($sql);
    }

    public function inserirConvite($idRepublica, $idUsuario, $destinatario) {
        $sql = "Insert into convite (IdRepublica, IdUsuario, Destinatario) values ('?1','?2','?3')";
        $sql = str_replace("?1", $idRepublica, $sql);
        $sql = str_replace("?2", $idUsuario, $sql);
        $sql = str_replace("?3", $destinatario, $sql);
        $this->executaSQL($sql);
    }

    /* ./ inserções */


    /* Pesquisas Repúblicas */

    public function getRepublicasSimplesPorIdUsuario($idUsuario) {
        $clausulaWhere = "where I.IdUsuario = '$idUsuario'";
        $republicas = $this->getArrayRepublicas($clausulaWhere, true, false, "republicasSimplesPorIdUsuario");
        return $republicas;
    }

    public function getRepublicasCompletaPorIdUsuario($idUsuario) {
        $clausulaWhere = "where R.Id in (Select IdRepublica from integrantes where IdUsuario='$idUsuario')";
        $republicas = $this->getArrayRepublicas($clausulaWhere, true, true, "republicasCompletaPorIdUsuario");
        return $republicas;
    }

    public function getRepublicaSimplesPorId($idRepublica) {
        $clausulaWhere = "where R.Id = '$idRepublica'";
        $republicas = $this->getArrayRepublicas($clausulaWhere, false, false, "republicaSimplesPorId");
        if (count($republicas) > 0) {
            return reset($republicas);
        } else {
            return null;
        }
    }

    public function getRepublicaCompletaPorId($idRepublica) {
        $clausulaWhere = "where R.Id = '$idRepublica'";
        $republicas = $this->getArrayRepublicas($clausulaWhere, true, true, "republicaCompletaPorId");
        if (count($republicas) > 0) {
            return reset($republicas);
        } else {
            return null;
        }
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

    /* Pesquisas convites */

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

    public function removeRepublica($idRepublica) {
        $sql = "Delete from republica where Id='$idRepublica'";
        $this->executaSQL($sql);
    }

    public function removeIntegrante($idUsuario, $idRepublica) {
        $sql = "Delete from integrantes where IdUsuario='?1' and IdRepublica='?2'";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        $this->executaSQL($sql);
    }

    public function removeConviteDeUsuario($idRepublica, $idUsuario) {
        $sql = "Delete from convite where IdUsuario='?1' and IdRepublica='?2' ";
        $sql = str_replace("?1", $idUsuario, $sql);
        $sql = str_replace("?2", $idRepublica, $sql);
        $this->executaSQL($sql);
    }

    public function getUsuariosRepublica($idRepublica) {
        $sql = "Select U.* from integrantes I inner join Usuario U on U.Id = I.IdUsuario ".
                "where I.IdRepublica='$idRepublica' order by U.Nome";
        $rs = $this->executaSQL($sql);
        $usuarios = array();
        while ($reg = mysqli_fetch_assoc($rs)) {
            $idUsuario = $reg["Id"];
            $nomeUsuario = $reg["Nome"];
            $emailUsuario = $reg["Email"];
            $usuario = new Usuario($idUsuario, $nomeUsuario, $emailUsuario);
            array_push($usuarios, $usuario);
        }
        return $usuarios;
    }

}
