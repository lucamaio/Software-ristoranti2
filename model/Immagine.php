<?php

class Immagine{
    public int $ID_immagine, $ID_ristorante;
    
    public string $percorso, $descrizione, $data_caricamento;

    public function __construct($ID_immagine, $ID_ristorante, $percorso, $descrizione, $data_caricamento){
        $this->$ID_immagine = $ID_immagine;
        $this->$ID_ristorante = $ID_ristorante;
        $this->$percorso = "../" . $percorso;
        $this->descrizione = $descrizione;
        $this->data_caricamento = $data_caricamento;
    }
}

?>