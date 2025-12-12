<?php

class TipologiaPiatto{

    public int $id;
    public string $nome;
    public ?string $descrizione;
    
    // Metadati
    public ?int $ID_admin;

    public ?string $data_inserimento;
    public ?string $data_ultima_modifica;

    public function __construct(int $id, string $nome, ?string $descrizione, ?int $ID_admin, ?string $data_inserimento, ?string $data_ultima_modifica ){
        $this->id = $id;
        $this->nome = $nome;
        $this->descrizione = $descrizione;
        $this->ID_admin = $ID_admin;
        $this->data_inserimento = $data_inserimento;
        $this->data_ultima_modifica = $data_ultima_modifica;
    }

}

?>