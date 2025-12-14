<?php

class Cart{

    public int $ID_carrello;
    public int $ID_cliente;
    public int $ID_stato_carrello;

    public string $data_creazione;

    public int $ID_ristorante;

    public function __construct(int $ID_carrello, int $ID_cliente, int $ID_stato_carrello, string $data_creazione, int $ID_ristorante){
        $this->ID_carrello = $ID_carrello;
        $this->ID_cliente = $ID_cliente;
        $this->ID_stato_carrello = $ID_stato_carrello;
        $this->data_creazione = $data_creazione;
        $this->ID_ristorante = $ID_ristorante;
    }

}

?>