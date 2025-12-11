<?php

    // Crazione connessione al database MySQL
    $link = new mysqli("localhost", "root", "", "ristoranti_db");
    if ($link->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Connessione fallita: ' . $link->connect_error]);
        exit();
    }
    return $link;

?>