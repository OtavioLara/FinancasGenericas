<?php

class Formato {

    public function numeroInterface($numero) {
        return str_replace(".", ",", $numero);
    }

    public function numeroControle($numero) {
        return str_replace(",", ".", $numero);
    }

}
