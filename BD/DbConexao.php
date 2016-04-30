<?php

class DbConexao {  
    static function getConnection() {
        $conexao = new mysqli("localhost", "root", "", "republica"); 
        //$conexao = new mysqli("localhost", "1107484", "zappe1564","1107484");
        if ($conexao->connect_error) {
            die("Connection failed: " . $conexao->connect_error);
        }
        $conexao->set_charset("utf8");
        return $conexao;
    }    
  
}

?>