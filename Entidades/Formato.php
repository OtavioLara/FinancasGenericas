<?php

class Formato {

    public function numeroInterface($numero) {
        return number_format($numero, 2, ',', '.');
    }

    public function numeroControle($numero) {
        $numero = str_replace(".", "", $numero);
        $numero = str_replace(",", ".", $numero);
        return $numero;
    }

}
