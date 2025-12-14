<?php

class DettailsCart{

    public int $ID_carrello;
    public int $ID_piatto;

    public int $quantità;
    
    public function __construct(int $ID_carrello, int $ID_piatto, int $quantità ){
        $this->ID_carrello = $ID_carrello;
        $this->ID_piatto = $ID_piatto;
        $this->quantità = $quantità;
    }

}

?>