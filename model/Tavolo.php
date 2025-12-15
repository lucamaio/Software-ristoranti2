<?php

class Tavolo{

    public int $ID_tavolo, $numero_tavolo, $posti, $ID_ristorante, $ID_ristoratore;

    public function __construct(int $ID_tavolo, int $numero_tavolo, int $posti, int $ID_ristorante, int $ID_ristoratore ){
        $this->ID_tavolo = $ID_tavolo;
        $this->numero_tavolo = $numero_tavolo;
        $this->posti = $posti;
        $this->ID_ristorante = $ID_ristorante;
        $this->ID_ristoratore = $ID_ristoratore;
    }

}