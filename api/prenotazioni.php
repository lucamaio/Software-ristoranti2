<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../model/Prenotazione.php';
require_once '../model/table/PrenotazioniTable.php';
session_start();
// Controlli pre-liminari per evitare accessi indesiderati

// 1. Verifico l'essistenza di una sessione valida 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['ruolo'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Utente non autenticato']);
    exit;
}

// 2. Verifico se la richiesta è valida

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
    exit;
}

// 3. Verifico se l'azione è diversa da null
$user_id = $_SESSION['user_id'];
$ruolo = $_SESSION['ruolo'];
$azione = $_POST['azione'] ?? 'null';

if (!$azione) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Azione non valida']);
    exit;
}

// 4. Eseguo l'azione richiesta
switch($azione){
    case 'ricava_prenotazioni':
        ricavaPrenotazioni($link, $user_id, $ruolo);
        break;
    case 'dettagli_prenotazione':
            // echo json_encode([
            //     'success'=>false,
            //     'error'=>'Funzione non ancora implementata!'
            // ]);
            if(!isset($_POST['ID_prenotazione'])){
                echo json_encode([
                    'success'=>false,
                    'error'=>'Parametri mancanti per il dettaglio della prenotazione'
                ]);
                exit;
            }

            ricavaDettagliPrenotazione($link,$_POST['ID_prenotazione'], $user_id, $ruolo);
        break;
    case 'crea_prenotazione':

        if(!isset($_POST['ID_ristorante']) || !isset($_POST['data_prenotazione']) || !isset($_POST['ora_prenotazione']) || !isset($_POST['persone'])){
            echo json_encode([
                'success'=>false,
                'error'=>'Parametri mancanti per la creazione della prenotazione'
            ]);
            exit;
        }

        nuovaPrenotazione($link,$_POST['ID_ristorante'], $_POST['data_prenotazione'],$_POST['ora_prenotazione'],$_POST['persone'], $user_id);
        break;
    case 'aggiorna_prenotazione':
        if(!isset($_POST['ID_prenotazione']) || !isset($_POST['data_prenotazione']) || !isset($_POST['ora_prenotazione']) || !isset($_POST['persone'])){
            echo json_encode([
                'success'=>false,
                'error'=>'Parametri mancanti per l\'aggiornamento della prenotazione'
            ]);
            exit;
        }

        aggiornaPrenotazione($link,$_POST['ID_prenotazione'], $_POST['data_prenotazione'],$_POST['ora_prenotazione'],$_POST['persone']); 
        break;

    case 'cancella_prenotazione':
        if(!isset($_POST['ID_prenotazione'])){
            echo json_encode([
                'success'=>false,
                'error'=>'Parametri mancanti per la cancellazione della prenotazione'
            ]);
            exit;
        }

        cancellaPrenotazione($link,$_POST['ID_prenotazione']);
        break;
    case 'accetta_prenotazione':
        if(!isset($_POST['ID_prenotazione'])){
            echo json_encode([
                'success'=>false,
                'error'=>'Parametri mancanti per l\'accettazione della prenotazione'
            ]);
            exit;
        }

        accettaPrenotazione($link,$_POST['ID_prenotazione']);
        break;
    case 'declina_prenotazione':
        if(!isset($_POST['ID_prenotazione'])){
            echo json_encode([
                'success'=>false,
                'error'=>'Parametri mancanti per il rifiuto della prenotazione'
            ]);
            exit;
        }

        declinaPrenotazione($link,$_POST['ID_prenotazione']);
        break;
    case 'imposta_tavolo':
        if(!isset($_POST['ID_prenotazione']) || !isset($_POST['ID_tavolo'])){
            echo json_encode([
                'success'=>false,
                'error'=>'Parametri mancanti per l\'impostazione del tavolo'
            ]);
            exit;
        }
        impostaTavolo($link,$_POST['ID_prenotazione'], $_POST['ID_tavolo']);
        break;

        break;
    default:
        echo json_encode([
            'success'=>false,
            'error'=>'Azione non riconosciuta'
        ]);
}   

// Funzione che restituisce tutte le prenotazioni di un utente a seconda del suo ruolo

function ricavaPrenotazioni($link, $user_id, $ruolo){
    switch($ruolo){
        case 'cliente':
            // Ricavo le prenotazioni del cliente
            $query = "
                SELECT p.*, r.nome AS nome_ristorante , t.numero_tavolo AS numero_tavolo, c.nome AS nome_cliente, c.cognome AS cognome_cliente, sp.nome_stato AS nome_stato
                FROM prenotazioni p 
                LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                LEFT JOIN tavoli t ON p.ID_tavolo = t.ID_tavolo  
                LEFT JOIN stati_prenotazioni sp ON p.ID_stato_prenotazione = sp.ID_stato_prenotazione
                LEFT JOIN clienti c ON p.ID_cliente = c.ID_cliente
                WHERE p.ID_cliente = ?
                ORDER BY p.data_prenotazione DESC
            "; 

            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            break;
        case 'ristoratore':
            // Ricavo le prenotazioni dei ristoranti gestiti dal ristoratore
            $query = "
                SELECT p.*, r.nome AS nome_ristorante , t.numero_tavolo AS numero_tavolo, c.nome AS nome_cliente, c.cognome AS cognome_cliente, sp.nome_stato AS nome_stato
                FROM prenotazioni p 
                LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                LEFT JOIN tavoli t ON p.ID_tavolo = t.ID_tavolo  
                LEFT JOIN stati_prenotazioni sp ON p.ID_stato_prenotazione = sp.ID_stato_prenotazione
                LEFT JOIN clienti c ON p.ID_cliente = c.ID_cliente
                WHERE r.ID_ristoratore = ?
                ORDER BY p.data_prenotazione DESC
            "; 

            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            break;
        case 'admin':
            // Ricavo tutte le prenotazioni
            $query = "
                SELECT p.*, r.nome AS nome_ristorante , t.numero_tavolo AS numero_tavolo, c.nome AS nome_cliente, c.cognome AS cognome_cliente, sp.nome_stato AS nome_stato
                FROM prenotazioni p 
                LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                LEFT JOIN tavoli t ON p.ID_tavolo = t.ID_tavolo  
                LEFT JOIN stati_prenotazioni sp ON p.ID_stato_prenotazione = sp.ID_stato_prenotazione
                LEFT JOIN clienti c ON p.ID_cliente = c.ID_cliente
            "; 
            break;
        default:
            echo json_encode(['success'=>false, 'error'=>'Ruolo utente non riconosciuto']);
            exit;
    }

    if($ruolo !== 'admin'){
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($link, $query);
    }

    // Controllo errori nella query
    if(!$result){
        echo json_encode(['success'=>false, 'error'=>'Errore nella query: '.mysqli_error($link)]);
        exit;
    }

    // verifico se ci sono prenotazioni
    if(mysqli_num_rows($result) === 0){
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    // Altrimenti, ciclo sui risultati e li inserisco nell'array delle prenotazioni
    $prenotazioni = [];
    while($row = mysqli_fetch_assoc($result)){
        $prenotazione = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
        $prenotazioni[] = new PrenotazioniTable($prenotazione, $row['nome_ristorante'], $row['cognome_cliente']. " ".$row['nome_cliente'], $row['numero_tavolo'], $row['nome_stato']);
    }
    
    // Chiudo lo statement se è stato utilizzato
    if(isset($stmt)){
        mysqli_stmt_close($stmt);
    }
    echo json_encode(['success' => true, 'data' => $prenotazioni]);
    exit;
}   


// Funzione 2: che restituisce i dettagli di una prenotazione

function ricavaDettagliPrenotazione($link, $id_prenotazione, $user_id, $ruolo){
    switch ($ruolo){
        case 'cliente':
            $query = "
                SELECT p.*
                FROM prenotazioni p 
                WHERE p.ID_prenotazione = ? AND p.ID_cliente = ?
            "; 
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "ii", $id_prenotazione, $user_id);
            break;
        case 'ristoratore':
            $query = "
                SELECT p.*
                FROM prenotazioni p 
                LEFT JOIN ristoranti r ON  p.ID_ristorante = r.ID_ristorante
                WHERE p.ID_prenotazione = ? AND r.ID_ristoratore = ?
            "; 
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "ii", $id_prenotazione, $user_id);
            break;
        case 'admin':
            $query = "
                SELECT p.*
                FROM prenotazioni p
                WHERE p.ID_prenotazione = ?
            ";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_prenotazione);
            break;
        default:
            echo json_encode(['success'=>false, 'error'=>'Ruolo utente non riconosciuto']);
            exit;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Controllo errori nella query
    if(!$result){
        echo json_encode(['success'=>false, 'error'=>'Errore nella query: '.mysqli_error($link)]);
        exit;
    }

    // verifico se la prenotazione esiste
    if(mysqli_num_rows($result) !== 1){
        echo json_encode(['success'=>false, 'error'=>'Prenotazione non trovata']);
        exit;
    }
    $row = mysqli_fetch_assoc($result);
    $prenotazione = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
    
    echo json_encode(['success'=>true, 'data'=>$prenotazione]);
    exit;
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

function impostaTavolo($link, $id_prenotazione, $id_tavolo){
    $stmt = mysqli_prepare($link,"UPDATE prenotazioni SET ID_tavolo = ? WHERE ID_prenotazione = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id_tavolo, $id_prenotazione);
    
    if(!mysqli_stmt_execute($stmt)){
        echo json_encode([
            'success'=>false,
            'error'=>'Errore nell\'esecuzione della query: '.mysqli_stmt_error($stmt)
        ]);
        exit;
    }

    echo json_encode([
        'success'=>true,
        'message'=>'Tavolo impostato con successo!'
    ]);

    mysqli_stmt_close($stmt);
}

mysqli_close($link);

?>