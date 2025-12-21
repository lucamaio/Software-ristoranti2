<?php

class Comune{
    public int $ID_citta;
    public string $comune, $regione, $provincia, $cap;

    public function __construct($ID_citta, $comune, $regione, $provincia, $cap){
        $this->ID_citta = $ID_citta;
        $this->comune = $comune;
        $this->regione = $regione;
        $this->provincia = $provincia;
        $this->cap = $cap;
    }
}
?>