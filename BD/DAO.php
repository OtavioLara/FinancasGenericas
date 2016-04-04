<?php

class DAO {

    protected $conexao;

    function __construct($conexao = null) {
        if (!isset($conexao)) {
            $conexao = DbConexao::getConnection();
        }
        $this->conexao = $conexao;
    }

    protected function idInserido($id = -1) {
        if ($id >= 0) {
            return $id;
        } else {
            return mysqli_insert_id($this->conexao);
        }
    }

    protected function executaSQL($sql) {
        $rs = mysqli_query($this->conexao, $sql);
        return $rs;
    }

}

?>
