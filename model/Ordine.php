<?php

class Ordine{
    public int $ID_ordine, $ID_cliente, $ID_ristorante, $ID_stato_ordine, $ID_carrello;

    public ?int $ID_prenotazione;
    public string $data_ordine;
    

    public function __construct(int $ID_ordine, int $ID_cliente, int $ID_ristorante, int $ID_stato_ordine, string $data_ordine, ?int $ID_prenotazione, int $ID_carrello){
        $this->ID_ordine = $ID_ordine;
        $this->ID_cliente = $ID_cliente;
        $this->ID_ristorante = $ID_ristorante;
        $this->ID_stato_ordine = $ID_stato_ordine;
        $this->data_ordine = $data_ordine;
        $this->ID_prenotazione = $ID_prenotazione;
        $this->ID_carrello = $ID_carrello;
    }
}