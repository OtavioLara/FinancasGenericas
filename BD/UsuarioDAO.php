<?php


class UsuarioDAO extends DAO {

    function __construct($conexao = null) {
        parent::__construct($conexao);
    }

    function getUsuario($reg) {
        $email = $reg['Email'];
        $nome = $reg['Nome'];
        $id = $reg['Id'];
        return new Usuario($id, $nome, $email);
    }
    
    public function verificaSenha($email, $senha) {
        $sql = "Select Nome,Email,Id from usuario where Email= '?1' and Senha = '?2'";
        $sql = str_replace("?1", $email, $sql);
        $sql = str_replace("?2", $senha, $sql);
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $this->getUsuario($reg);
        }
        return null;
    }

    public function inserirUsuario(Usuario $usuario, $senha) {
        $email = mysql_real_escape_string($usuario->getEmail());
        $nome = mysql_real_escape_string($usuario->getNome());
        $senha = mysql_real_escape_string($senha);
        $sql = "Insert into usuario (Email, Nome, Senha) values ('?1','?2','?3')";
        $sql = str_replace("?1", $email, $sql);
        $sql = str_replace("?2", $nome, $sql);
        $sql = str_replace("?3", $senha, $sql);
        $this->executaSQL($sql);
    }

    public function getUsuarioPorId($idUsuario) {
        $sql = "Select Id, Nome, Email from usuario where Id='?1'";
        $sql = str_replace("?1", $idUsuario, $sql);
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $this->getUsuario($reg);
        }
        return null;
    }

    public function getUsuarioPorEmail($email) {
        $sql = "Select Id, Nome, Email from usuario where Email='?1'";
        $sql = str_replace("?1", $email, $sql);
        $rs = $this->executaSQL($sql);
        if ($reg = mysqli_fetch_assoc($rs)) {
            return $this->getUsuario($reg);
        }
        return null;
    }

}

?>
