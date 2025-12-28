<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../model/Prenotazione.php';
require_once '../model/table/PrenotazioniTable.php';

$method = $_SERVER['REQUEST_METHOD'];
$table = 'prenotazioni';
session_start();

if(!isset($_SESSION['user_id'])){
     echo json_encode(['success' => false, 'error' => 'Devi accedere per poter eseguire la query']);
    exit;
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if(!isset($method)){
    echo json_encode(['success' => false, 'error' => 'Metodo non specificato!']);
    exit;
}


switch($method){
    case 'GET':
        if(!isset($_GET['id'])){
            switch ($role){
                case 'client':

                    $query = "SELECT p.*, r.nome AS nome_ristorante , t.numero_tavolo AS numero_tavolo, c.nome AS nome_cliente, c.cognome AS cognome_cliente, sp.nome_stato AS nome_stato
                    FROM `$table` p 
                    LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                    LEFT JOIN tavoli t ON p.ID_tavolo = t.ID_tavolo  
                    LEFT JOIN stati_prenotazioni sp ON p.ID_stato_prenotazione = sp.ID_stato_prenotazione
                    LEFT JOIN clienti c ON p.ID_cliente = c.ID_cliente
                    WHERE p.ID_cliente = ?
                    ORDER BY p.data_prenotazione DESC"; 

                    $stmt = mysqli_prepare($link, $query);
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    // $result = mysqli_query($link, "SELECT * FROM $table WHERE ID_Cliente = ?");
                    $prenotazioni = [];
                    while($row = mysqli_fetch_assoc($result)){
                        $prenotazione = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
                        $prenotazioni[] = new PrenotazioniTable($prenotazione, $row['nome_ristorante'], $row['cognome_cliente']. " ".$row['nome_cliente'], $row['numero_tavolo'], $row['nome_stato']);
                    }
                    echo json_encode(['success' => true, 'data' => $prenotazioni]);
                    exit;
                case 'restaurant':
                    $query = "SELECT p.*, r.nome AS nome_ristorante , t.numero_tavolo AS numero_tavolo, c.nome AS nome_cliente, c.cognome AS cognome_cliente, sp.nome_stato AS nome_stato
                    FROM `$table` p 
                    LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                    LEFT JOIN tavoli t ON p.ID_tavolo = t.ID_tavolo  
                    LEFT JOIN stati_prenotazioni sp ON p.ID_stato_prenotazione = sp.ID_stato_prenotazione
                    LEFT JOIN clienti c ON p.ID_cliente = c.ID_cliente
                    WHERE r.ID_ristoratore = ?
                    ORDER BY p.data_prenotazione DESC
                    "; 
                    // $stmt = mysqli_prepare($link, "SELECT p.* FROM `$table` p LEFT JOIN ristoranti r ON p.ID_ristorante = r.ID_ristorante WHERE r.ID_ristoratore = ?");
                    $stmt = mysqli_prepare($link, $query);
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $prenotazioni = [];
                    while($row = mysqli_fetch_assoc($result)){
                        $prenotazione = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
                        $prenotazioni[] = new PrenotazioniTable($prenotazione, $row['nome_ristorante'], $row['cognome_cliente']. " ".$row['nome_cliente'], $row['numero_tavolo'], $row['nome_stato']);
                    }
                     echo json_encode(['success' => true, 'data' => $prenotazioni]);
                    exit;
                case 'admin':
                    $query = "SELECT p.*, r.nome AS nome_ristorante , t.numero_tavolo AS numero_tavolo, c.nome AS nome_cliente, c.cognome AS cognome_cliente, sp.nome_stato AS nome_stato
                    FROM `$table` p 
                    LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                    LEFT JOIN tavoli t ON p.ID_tavolo = t.ID_tavolo  
                    LEFT JOIN stati_prenotazioni sp ON p.ID_stato_prenotazione = sp.ID_stato_prenotazione
                    LEFT JOIN clienti c ON p.ID_cliente = c.ID_cliente
                    "; 
                    $result = mysqli_query($link, $query);
                    $prenotazioni = [];
                    while($row = mysqli_fetch_assoc($result)){
                        $prenotazione = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
                        $prenotazioni[] = new PrenotazioniTable($prenotazione, $row['nome_ristorante'], $row['cognome_cliente']. " ".$row['nome_cliente'], $row['numero_tavolo'], $row['nome_stato']);
                    }
                     echo json_encode(['success' => true, 'data' => $prenotazioni]);
                    exit;
            }
            echo json_encode(['success' => false, 'error' => 'Utente non autorizzato']);
        }

        $id = $_GET['id'];
        $query = "SELECT p.*, r.nome AS nome_ristorante , t.numero_tavolo AS numero_tavolo, c.nome AS nome_cliente, c.cognome AS cognome_cliente, sp.nome_stato AS nome_stato
                    FROM `$table` p 
                    LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                    LEFT JOIN tavoli t ON p.ID_tavolo = t.ID_tavolo  
                    LEFT JOIN stati_prenotazioni sp ON p.ID_stato_prenotazione = sp.ID_stato_prenotazione
                    LEFT JOIN clienti c ON p.ID_cliente = c.ID_cliente
                    WHERE p.ID_prenotazione = ?"; 
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) !== 1){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Prenotazione non trovata!']);
            exit;
        }

        $row = mysqli_fetch_assoc($result);
        $prenotazione = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
        echo json_encode($prenotazione);
        break;
    case 'POST':
        if(!isset($_POST['request_type'])){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Tipo di richiesta mancante!']);
            exit;
        }

        $tipo_richiesta = $_POST['request_type'];

        switch ($tipo_richiesta){
            case 'prenota':
                $id_ristorante = $_POST['ID_ristorante'];
                $data = $_POST['data'];
                $ora = $_POST['ora'];
                $persone = $_POST['persone'];
                
               if(!$id_ristorante || !$data || !$ora || !$persone){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Dati Mancanti!']);
                    exit;
                }

                if(!verificaData($data)){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'La data inserita deve essere futura!']);
                    exit;
                }

                nuovaPrenotazione($link, $id_ristorante, $data, $ora, $persone, $user_id);
                exit;
            case 'modifica':
                $id_prenotazione = $_POST['ID_prenotazione'];
                $data = $_POST['data'];
                $ora = $_POST['ora'];
                $persone = $_POST['persone'];

                // 1. Verifico che i dati non siano vuoti

                if(!$id_prenotazione || !$data || !$ora || !$persone){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Dati Mancanti!']);
                    exit;
                }

                aggiornaPrenotazione($link, $id_prenotazione, $data, $ora, $persone);
                break;
            case 'cancella':
                $id_prenotazione = $_POST['ID_prenotazione'];

                if(!$id_prenotazione){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Dati Mancanti!']);
                    exit;
                }

                cancellaPrenotazione($link, $id_prenotazione);
                break;
            case 'accetta':
                if($role !== 'restaurant'){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Accesso Negato!']);
                    exit;
                }

                $id_prenotazione = $_POST['ID_prenotazione'];

                if(!$id_prenotazione){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Dati Mancanti!']);
                    exit;
                }
                accettaPrenotazione($link, $id_prenotazione);
                break;
            case 'declina':
                if($role !== 'restaurant'){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Accesso Negato!']);
                    exit;
                }

                $id_prenotazione = $_POST['ID_prenotazione'];

                if(!$id_prenotazione){
                    http_response_code(405);
                    echo json_encode(['success' => false, 'error' => 'Dati Mancanti!']);
                    exit;
                }
                declinaPrenotazione($link, $id_prenotazione);
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
                break;
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
        break;
}

// Funzione che crea una nuova prenotazione

function nuovaPrenotazione($link, $id_ristorante, $data, $ora, $persone, $user_id){
    $stmt = mysqli_prepare(
        $link,
        "INSERT INTO prenotazioni 
        (data_prenotazione, ora_prenotazione, persone, ID_ristorante, ID_cliente, ID_stato_prenotazione) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    if(!$stmt){
        echo json_encode([
            'success'=>false,
            'error'=>'Errore nella preparazione della query: '.mysqli_error($link)
        ]);
        exit;
    }
    $stato = 1;
    mysqli_stmt_bind_param($stmt, "ssiiii", $data, $ora, $persone, $id_ristorante, $user_id, $stato);
    
    if(!mysqli_stmt_execute($stmt)){
        echo json_encode([
            'success'=>false,
            'error'=>'Errore nell\'esecuzione della query: '.mysqli_stmt_error($stmt)
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Prenotazione inserita correttamente',
        'id_prenotazione' => mysqli_insert_id($link)
    ]);

    mysqli_stmt_close($stmt);
}

// Funzioni che aggiornano lo stato di una prenotazione

function aggiornaPrenotazione($link, $id_prenotazione, $data, $ora, $persone){
    $stmt = mysqli_prepare($link, "SELECT data_prenotazione, ora_prenotazione, persone FROM prenotazioni WHERE ID_prenotazione = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_prenotazione);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) !== 1){
        echo json_encode([
            'success'=>false,
            'error'=>'Prenotazione non trovata'
        ]);
        exit;
    }

    $row = mysqli_fetch_assoc($result);

    if($data != $row['data_prenotazione'] || $ora != $row['ora_prenotazione'] || intval($persone) != intval($row['persone'])){
        $nuovo_stato = 5; // Modificata
        $stmt2 = mysqli_prepare($link, "
            UPDATE prenotazioni
            SET data_prenotazione = ?, ora_prenotazione = ?, persone = ?, ID_stato_prenotazione = ?
            WHERE ID_prenotazione = ?
        ");
        mysqli_stmt_bind_param($stmt2, "sssii", $data, $ora, $persone, $nuovo_stato, $id_prenotazione);

        if(!mysqli_stmt_execute($stmt2)){
            echo json_encode([
                'success'=>false,
                'error'=>'Errore nell\'esecuzione della query: '.mysqli_stmt_error($stmt2)
            ]);
            exit;
        }

        mysqli_stmt_close($stmt2);
    }

    echo json_encode([
        'success'=>true,
        'message'=>'Prenotazione aggiornata!'
    ]);

    mysqli_stmt_close($stmt);
}


function cancellaPrenotazione($link, $id_prenotazione){
    $stato = 3; // Stato Annulato
    $stmt = mysqli_prepare($link,"UPDATE prenotazioni SET ID_stato_prenotazione = ? WHERE ID_prenotazione = ?");
    mysqli_stmt_bind_param($stmt, "ii", $stato, $id_prenotazione);
    
    if(!mysqli_stmt_execute($stmt)){
        echo json_encode([
            'success'=>false,
            'error'=>'Errore nell\'esecuzione della query: '.mysqli_stmt_error($stmt)
        ]);
        exit;
    }

    echo json_encode([
        'success'=>true,
        'message'=>'Prenotazione cancellata con successo!'
    ]);

    mysqli_stmt_close($stmt);
}

function accettaPrenotazione($link, $id_prenotazione){
    $stato = 2; // Stato confermato
    $stmt = mysqli_prepare($link,"UPDATE prenotazioni SET ID_stato_prenotazione = ? WHERE ID_prenotazione = ?");
    mysqli_stmt_bind_param($stmt, "ii", $stato, $id_prenotazione);
    
    if(!mysqli_stmt_execute($stmt)){
        echo json_encode([
            'success'=>false,
            'error'=>'Errore nell\'esecuzione della query: '.mysqli_stmt_error($stmt)
        ]);
        exit;
    }

    echo json_encode([
        'success'=>true,
        'message'=>'Prenotazione confermata con successo!'
    ]);

    mysqli_stmt_close($stmt);
}

function declinaPrenotazione($link, $id_prenotazione){
    $stato = 7; // Stato Rifiutata
    $stmt = mysqli_prepare($link,"UPDATE prenotazioni SET ID_stato_prenotazione = ? WHERE ID_prenotazione = ?");
    mysqli_stmt_bind_param($stmt, "ii", $stato, $id_prenotazione);
    
    if(!mysqli_stmt_execute($stmt)){
        echo json_encode([
            'success'=>false,
            'error'=>'Errore nell\'esecuzione della query: '.mysqli_stmt_error($stmt)
        ]);
        exit;
    }

    echo json_encode([
        'success'=>true,
        'message'=>'Prenotazione rifiutata con successo!'
    ]);

    mysqli_stmt_close($stmt);
}

mysqli_close($link);

?>