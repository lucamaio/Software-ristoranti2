<?php
#package model;

class Ristorante{

    // Informazioni di base
    public int $ID_ristorante;
    public string $nome;
    public string $indirizzo;
    public string $numero_civico;
    public string $telefono;
    public string $email;
    // Descrizioni
    private string $decrizione_breve;
    private string $decrizione_estesa;

    // Informazioni fiscali
    private string $codice_fiscale;
    private string $partita_iva;
    private string $ragione_sociale;

    // altre informazioni
    private int $capienza;
    private int $id_ristoratore;

    private int $id_citta;

    // Informazioni mappa
    private float $latitudine;
    private float $longitudine;

    // Costruttore

    public function __construct(int $ID_ristorante, string $nome, string $indirizzo, string $telefono, string $email){
        $this->ID_ristorante = $ID_ristorante;
        $this->nome = $nome;
        $this->indirizzo = $indirizzo;
        $this->telefono = $telefono;
        $this->email = $email;
    }

    // Costruttore completo
    // public function __construct_full(int $id, string $nome, string $indirizzo, string $numero_civico, string $telefono, string $email,
    // string $decrizione_breve, string $decrizione_estesa, string $codice_fiscale, string $partita_iva, string $ragione_sociale,
    // int $capienza, int $id_ristoratore){
    //     $this->id = $id;
    //     $this->nome = $nome;
    //     $this->indirizzo = $indirizzo;
    //     $this->numero_civico = $numero_civico;
    //     $this->telefono = $telefono;
    //     $this->email = $email;
    //     $this->decrizione_breve = $decrizione_breve;
    //     $this->decrizione_estesa = $decrizione_estesa;
    //     $this->codice_fiscale = $codice_fiscale;
    //     $this->partita_iva = $partita_iva;
    //     $this->ragione_sociale = $ragione_sociale;
    //     $this->capienza = $capienza;
    //     $this->id_ristoratore = $id_ristoratore;
    // }

    // metodi set e get

    
    public function gatID_ristorante(): int {
        return $this->ID_ristorante;
    }

    public function getNome(): string {
        return $this->nome;
    }
    public function getIndirizzo(): string {
        return $this->indirizzo;
    }
    public function getTelefono(): string {
        return $this->telefono;
    }
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getNumeroCivico(): string {
        return $this->numero_civico;
    }
    
    public function getDescrizioneBreve(): string {
        return $this->decrizione_breve;
    }
    public function getDescrizioneEstesa(): string {
        return $this->decrizione_estesa;
    }
    public function getCodiceFiscale(): string {
        return $this->codice_fiscale;
    }
    public function getPartitaIva(): string {
        return $this->partita_iva;
    }
    public function getRagioneSociale(): string {
        return $this->ragione_sociale;
    }
    public function getCapienza(): int {
        return $this->capienza;
    }
    public function getIdRistoratore(): int {
        return $this->id_ristoratore;
    }
    public function getIdCitta(): int {
        return $this->id_citta;
    }
    public function getLatitudine(): float {
        return $this->latitudine;
    }
    public function getLongitudine(): float {
        return $this->longitudine;
    }

    // Metodo toString per rappresentazione testuale
    public function __toString(){
        return "Ristorante: id: $this->id, $this->nome, Indirizzo: $this->indirizzo, Telefono: $this->telefono, Email: $this->email";
    }
}?>