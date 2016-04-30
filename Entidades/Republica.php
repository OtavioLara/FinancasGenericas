<?php

class Republica {

    private $id;
    private $nome;
    private $integrantes;

    function __construct($id, $nome, $integrantes = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->integrantes = $integrantes;
        if (!isset($integrantes)) {
            $this->integrantes = array();
        }
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getIntegrantes() {
        return $this->integrantes;
    }

    public function adicionaIntegrante($integrante) {
        if (isset($integrante)) {
            $this->integrantes[$integrante->getUsuario()->getId()] = $integrante;
        }
    }

    public function setIntegrantes($integrantes) {
        return $this->integrantes = $integrantes;
    }

    public function possuiIntegrante($idUsuario) {
        return isset($this->integrantes[$idUsuario]);
    }

    public function getIntegrante($idUsuario) {
        if (isset($this->integrantes[$idUsuario])) {
            return $this->integrantes[$idUsuario];
        }
        return null;
    }

    public function podeSair($idUsuario) {
        if ($this->possuiIntegrante($idUsuario)) {
            return !$this->isUnicoAdministrador($idUsuario) ||
                    ($this->isUnicoAdministrador($idUsuario) && count($this->integrantes) == 1);
        }
        return false;
    }

    public function isUnicoAdministrador($idUsuario) {
        $integranteUsuario = $this->getIntegrante($idUsuario);
        if (isset($integranteUsuario)) {
            if ($integranteUsuario->isAdministrador()) {
                $contadorAdministradores = 0;
                foreach ($this->integrantes as $integrante) {
                    if ($integrante->isAdministrador()) {
                        $contadorAdministradores++;
                        if ($contadorAdministradores > 1) {
                            return false;
                        }
                    }
                }
                return true;
            }
        }
        return false;
    }

}
