<?php

class Ristorante {

    // Informazioni di base
    public int $ID_ristorante;
    public string $nome, $indirizzo, $numero_civico, $telefono, $email, $descrizione_breve, $descrizione_estesa;

    // Informazioni fiscali
    public string $codice_fiscale, $partita_iva, $ragione_sociale;

    // altre informazioni
    public int $capienza;
    public int $id_ristoratore;

    public int $id_citta;

    // Informazioni mappa
    public float $latitudine, $longitudine;

    // Costruttore completo
    public function __construct(int $ID_ristorante, string $nome, string $indirizzo, string $numero_civico, string $telefono, string $email,
    string $descrizione_breve, string $descrizione_estesa, string $codice_fiscale, string $partita_iva, string $ragione_sociale,
    int $capienza, int $id_ristoratore){
        $this->ID_ristorante = $ID_ristorante;
        $this->nome = $nome;
        $this->indirizzo = $indirizzo;
        $this->numero_civico = $numero_civico;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->descrizione_breve = $descrizione_breve;
        $this->descrizione_estesa = $descrizione_estesa;
        $this->codice_fiscale = $codice_fiscale;
        $this->partita_iva = $partita_iva;
        $this->ragione_sociale = $ragione_sociale;
        $this->capienza = $capienza;
        $this->id_ristoratore = $id_ristoratore;
    }


    // Metodo toString per rappresentazione testuale
    public function __toString(){
        return "Ristorante: id: $this->id, $this->nome, Indirizzo: $this->indirizzo, Telefono: $this->telefono, Email: $this->email";
    }

}?>