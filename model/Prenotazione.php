<?php

class Prenotazione{
    // Informazioni di base
    public int $ID_prenotazione;
    public string $data_prenotazione;
    public string $ora_prenotazione;
    public int $numero_persone;

    // Altre informazioni
    public int $ID_ristorante;
    public int $ID_cliente;
    public int $ID_tavolo;
    public int $ID_stato_prenotazione;

    // Costruttore con i campi principali  
    public function __construct(int $ID_prenotazione, string $data_prenotazione, string $ora_prenotazione, int $numero_persone, int $ID_ristorante, int $ID_cliente){
        $this->ID_prenotazione = $ID_prenotazione;
        $this->data_prenotazione = $data_prenotazione;
        $this->ora_prenotazione = $ora_prenotazione;
        $this->numero_persone = $numero_persone;
        $this->ID_ristorante = $ID_ristorante;
        $this->ID_cliente = $ID_cliente;
    }

    // Metodi get
    public function getID_prenotazione(): int {
        return $this->ID_prenotazione;
    }
    public function getData_prenotazione(): string {
        return $this->data_prenotazione;
    }
    public function getOra_prenotazione(): string {
        return $this->ora_prenotazione;
    }
    public function getNumero_persone(): int {
        return $this->numero_persone;
    }
    public function getID_ristorante(): int {
        return $this->ID_ristorante;
    }
    public function getID_cliente(): int {
        return $this->ID_cliente;
    }
    public function getID_tavolo(): int {
        return $this->ID_tavolo;
    }
    public function getID_stato_prenotazione(): int {
        return $this->ID_stato_prenotazione;
    }

    // Metodi set
    public function setID_tavolo(int $ID_tavolo): void {
        $this->ID_tavolo = $ID_tavolo;
    }

    public function setID_stato_prenotazione(int $ID_stato_prenotazione): void {
        $this->ID_stato_prenotazione = $ID_stato_prenotazione;
    }

    // Metodo toString
    public function __toString(): string {
        return "Prenotazione [ID: $this->ID_prenotazione, Data: $this->data_prenotazione, Ora: $this->ora_prenotazione, Numero Persone: $this->numero_persone, Ristorante ID: $this->ID_ristorante, Cliente ID: $this->ID_cliente, Tavolo ID: $this->ID_tavolo, Stato Prenotazione ID: $this->ID_stato_prenotazione]";
    }  

}
?>