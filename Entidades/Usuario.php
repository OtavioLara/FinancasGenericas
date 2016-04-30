<?php

class Usuario implements JsonSerializable {

    private $id;
    private $nome;
    private $email;

    function __construct($id, $nome, $email) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function jsonSerialize() {
        $atributos = array(
            "id" => $this->id,
            "nome" => $this->nome,
            "email" => $this->email
        );
        return json_encode($atributos);
    }

}

?>
