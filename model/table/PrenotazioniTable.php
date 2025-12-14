<?php 

class PrenotazioniTable{

    public Prenotazione $prenotazione;
    public string $nomeRistorante;

    public string $nominativo;

    public ?string $tavolo;
    public string $statoPrenotazione;

    public function __construct(Prenotazione $prenotazione, string $nomeRistorante,  string $nominativo, ?string $tavolo, ?string $statoPrenotazione ){
        $this->prenotazione = $prenotazione;
        $this->nomeRistorante = $nomeRistorante;
        $this->nominativo = $nominativo;
        $this->tavolo = $tavolo;
        $this->statoPrenotazione = $statoPrenotazione;
    }


}

?>