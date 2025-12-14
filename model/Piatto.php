<?php
class Piatto{
    // Informazioni principali
    public int $ID_piatto;
    public string $nome;
    public string $descrizione;
    public float $prezzo;

    // Metadati

    public int $ID_ristorante;
    public int $ID_stato_piatto;
    public int $ID_tipologia_piatto;
    public ?int $ID_ristoratore;
    public ?int $ID_cuoco;

    public function __construct(int $ID_piatto, string $nome, string $descrizione, float $prezzo, int $ID_ristorante, int $ID_stato_piatto, int $ID_tipologia_piatto, ?int $ID_ristoratore, ?int $ID_cuoco){
        $this->ID_piatto = $ID_piatto; 
        $this->nome = $nome;
        $this->descrizione = $descrizione;
        $this->prezzo = $prezzo;
        $this->ID_ristorante = $ID_ristorante;
        $this->ID_stato_piatto = $ID_stato_piatto;
        $this->ID_tipologia_piatto = $ID_tipologia_piatto;
        $this->ID_ristoratore = $ID_ristoratore;
        $this->ID_cuoco = $ID_cuoco;
    }

}
?>