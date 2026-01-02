<?php
require_once '../includes/db.php';
require_once '../model/Ordine.php';
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

switch ($azione) {
    case 'get':
       ricava_ordini($link, $_SESSION['user_id'], $_SESSION['ruolo']);
        break;
    case 'ordina':

        if(!isset($_POST['ID_carrello'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID_carrello mancante']);
            exit;
        }

        if($ruolo !== 'cliente') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Solo i clienti possono effettuare ordini']);
            exit;
        }
        
        genera_ordine($link, $_SESSION['user_id']);
        break;
    case 'get-dettagli':
        if(!isset($_POST['ID_ordine']) ) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID_ordine mancante']);
            exit;
        }

        if($ruolo !== 'cliente') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Solo i clienti possono effettuare ordini']);
            exit;
        }
        $ID_ordine = $_POST['ID_ordine'];

        ricavaDettagliOrdine($link, $ID_ordine, $user_id, $ruolo);
        break;
    case 'cambia-stato':
        // Verifiche preliminiari
        if(!isset($_POST['ID_ordine']) || !isset($_POST['nuovo_stato']) || (!in_array($_POST['nuovo_stato'], [3,4,6,7,8]))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Parametri mancanti']);
            exit;
        }
        $ID_ordine = $_POST['ID_ordine'];
        $nuovo_stato = $_POST['nuovo_stato'];
        cambiaStatoOrdine($link, $ID_ordine, $nuovo_stato, $user_id, $ruolo);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Azione non riconosciuta']);
        exit;
}

function ricava_ordini($link, $user_id, $ruolo) {

    if ($ruolo === 'cliente') {

        $query = "
            SELECT o.*, r.nome AS nome_ristorante, COALESCE(SUM(dc.quantita * p.prezzo), 0) AS totale, so.nome AS stato_ordine
            FROM ordini o
            LEFT JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            LEFT JOIN dettagli_carrelli dc ON dc.ID_carrello = o.ID_carrello
            LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
            LEFT JOIN stati_ordini so ON o.ID_stato_ordine = so.ID_stato_ordine
            WHERE o.ID_cliente = ?
            GROUP BY o.ID_ordine
            ORDER BY o.data_ordine DESC
        ";

        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);

    } elseif ($ruolo === 'ristoratore') {

        $query = "
            SELECT o.*, r.nome AS nome_ristorante, COALESCE(SUM(dc.quantita * p.prezzo), 0) AS totale, so.nome AS stato_ordine
            FROM ordini o
            INNER JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            LEFT JOIN dettagli_carrelli dc ON dc.ID_carrello = o.ID_carrello
            LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
            LEFT JOIN stati_ordini so ON o.ID_stato_ordine = so.ID_stato_ordine
            WHERE r.ID_ristoratore = ?
            GROUP BY o.ID_ordine
            ORDER BY o.data_ordine DESC
        ";

        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);

    } elseif ($ruolo === 'admin') {

        $query = "
            SELECT o.*, r.nome AS nome_ristorante, COALESCE(SUM(dc.quantita * p.prezzo), 0) AS totale, so.nome AS stato_ordine
            FROM ordini o
            LEFT JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            LEFT JOIN dettagli_carrelli dc ON dc.ID_carrello = o.ID_carrello
            LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
            LEFT JOIN stati_ordini so ON o.ID_stato_ordine = so.ID_stato_ordine
            GROUP BY o.ID_ordine
            ORDER BY o.data_ordine DESC
        ";

        $stmt = mysqli_prepare($link, $query);

    } elseif ($ruolo === 'cuoco') {

        $query = "
            SELECT o.*, r.nome AS nome_ristorante, COALESCE(SUM(dc.quantita * p.prezzo), 0) AS totale, so.nome AS stato_ordine
            FROM ordini o
            INNER JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            INNER JOIN cuochi c ON c.ID_ristorante = r.ID_ristorante
            LEFT JOIN dettagli_carrelli dc ON dc.ID_carrello = o.ID_carrello
            LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
            LEFT JOIN stati_ordini so ON o.ID_stato_ordine = so.ID_stato_ordine
            WHERE c.ID_cuoco = ?
            GROUP BY o.ID_ordine
            ORDER BY o.data_ordine DESC
        ";

        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);

    } else {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Ruolo non autorizzato']);
        exit;
    }

    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Errore preparazione query',
            'details' => mysqli_error($link)
        ]);
        exit;
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ordine = new Ordine(
            $row['ID_ordine'],
            $row['ID_cliente'],
            $row['ID_ristorante'],
            $row['ID_stato_ordine'],
            $row['data_ordine'],
            $row['ID_prenotazione'],
            $row['ID_carrello']
        );

        $data[] = [
            'ordine' => $ordine,
            'nome_ristorante' => $row['nome_ristorante'],
            'totale' => $row['totale'] ?? 0,
            'stato_ordine' => $row['stato_ordine'] ?? null
        ];
    }

    echo json_encode(['success' => true, 'data' => array_values($data), 'ruolo' => $ruolo]);
    exit;
}

function genera_ordine($link, $user_id) {
    $ID_carrello = $_POST['ID_carrello'];

    // Verifico che il carrello appartenga al cliente
    $query = "SELECT ID_cliente, ID_ristorante FROM carrelli WHERE ID_carrello = ? AND ID_stato_carrello = 1 ";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'i', $ID_carrello);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $carrello = mysqli_fetch_assoc($result);

    if (!$carrello || $carrello['ID_cliente'] != $user_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Carrello non valido o non appartiene al cliente']);
        exit;
    }

    // Verifico che il cliente abbia una prenotazione valida (stato 2 o 5) entro +/- 30 minuti per lo stesso ristorante
    $pren_query = "SELECT ID_prenotazione FROM prenotazioni WHERE ID_cliente = ? AND ID_ristorante = ? AND ID_stato_prenotazione IN (2,5) AND TIMESTAMP(data_prenotazione, ora_prenotazione) BETWEEN DATE_SUB(NOW(), INTERVAL 30 MINUTE) AND DATE_ADD(NOW(), INTERVAL 30 MINUTE) LIMIT 1";
    $pren_stmt = mysqli_prepare($link, $pren_query);
    mysqli_stmt_bind_param($pren_stmt, 'ii', $user_id, $carrello['ID_ristorante']);
    mysqli_stmt_execute($pren_stmt);
    $pren_result = mysqli_stmt_get_result($pren_stmt);
    $prenotazione_valida = mysqli_fetch_assoc($pren_result);

    if (!$prenotazione_valida) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Nessuna prenotazione valida entro 30 minuti per questo ristorante']);
        exit;
    }

    // Inizio transazione
    mysqli_begin_transaction($link);

    try {
        // Creo l'ordine
        $query = "INSERT INTO ordini (ID_cliente, ID_ristorante, ID_stato_ordine, ID_carrello, ID_prenotazione) VALUES (?, ?, 1, ?, ?)";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'iiii', $user_id, $carrello['ID_ristorante'], $ID_carrello, $prenotazione_valida['ID_prenotazione']);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Errore durante la generazione dell\'ordine');
        }

        // Aggiorno lo stato del carrello a 2 (In corso)
        $query_carrello = "UPDATE carrelli SET ID_stato_carrello = 2 WHERE ID_carrello = ?";
        $stmt_carrello = mysqli_prepare($link, $query_carrello);
        mysqli_stmt_bind_param($stmt_carrello, 'i', $ID_carrello);
        
        if (!mysqli_stmt_execute($stmt_carrello)) {
            throw new Exception('Errore durante l\'aggiornamento dello stato del carrello');
        }

        // Aggiorno lo stato della prenotazione a 6 (In corso)
        $query_pren = "UPDATE prenotazioni SET ID_stato_prenotazione = 6 WHERE ID_prenotazione = ?";
        $stmt_pren = mysqli_prepare($link, $query_pren);
        mysqli_stmt_bind_param($stmt_pren, 'i', $prenotazione_valida['ID_prenotazione']);
        
        if (!mysqli_stmt_execute($stmt_pren)) {
            throw new Exception('Errore durante l\'aggiornamento dello stato della prenotazione');
        }

        // Commit della transazione
        mysqli_commit($link);
        echo json_encode(['success' => true, 'message' => 'Ordine generato con successo']);

    } catch (Exception $e) {
        // Rollback in caso di errore
        mysqli_rollback($link);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    exit;
}

function ricavaDettagliOrdine($link, $ID_ordine, $user_id, $ruolo) {
    // Preparo la query in base al ruolo
    if($ruolo === 'cliente'){
        $query = "
            SELECT o.*, r.nome AS nome_ristorante, so.nome AS stato_ordine,
                dc.quantita, p.nome AS nome_piatto, p.prezzo
            FROM ordini o
            LEFT JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            LEFT JOIN stati_ordini so ON o.ID_stato_ordine = so.ID_stato_ordine
            LEFT JOIN dettagli_carrelli dc ON dc.ID_carrello = o.ID_carrello
            LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
            WHERE o.ID_ordine = ? AND o.ID_cliente = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $ID_ordine, $user_id);
    }else if($ruolo === 'ristoratore'){
        $query = "
            SELECT o.*, r.nome AS nome_ristorante, so.nome AS stato_ordine,
                dc.quantita, p.nome AS nome_piatto, p.prezzo
            FROM ordini o
            LEFT JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            LEFT JOIN stati_ordini so ON o.ID_stato_ordine = so.ID_stato_ordine
            LEFT JOIN dettagli_carrelli dc ON dc.ID_carrello = o.ID_carrello
            LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
            WHERE o.ID_ordine = ? AND r.ID_ristoratore = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $ID_ordine, $user_id);
    } else if($ruolo === 'cuoco'){
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Funzionalità non ancora implementata']);
        exit;
    }else if($ruolo === 'admin'){
        $query = "
            SELECT o.*, r.nome AS nome_ristorante, so.nome AS stato_ordine,
                dc.quantita, p.nome AS nome_piatto, p.prezzo
            FROM ordini o
            LEFT JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            LEFT JOIN stati_ordini so ON o.ID_stato_ordine = so.ID_stato_ordine
            LEFT JOIN dettagli_carrelli dc ON dc.ID_carrello = o.ID_carrello
            LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
            WHERE o.ID_ordine = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'i', $ID_ordine);
    }else {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Ruolo non autorizzato']);
        exit;
    }
   
    // Eseguo la query e ottengo i risultati
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Controllo se l'ordine esiste
    if(mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Ordine non trovato']);
        exit;
    }
    // Preparo la risposta
    $dettagli = [];
    $ordine_info = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Controllo autorizzazione
        if ($ruolo === 'cliente' && $row['ID_cliente'] != $user_id) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato all\'ordine']);
            exit;
        }

        $dettagli[] = [
            'quantita' => $row['quantita'],
            'nome_piatto' => $row['nome_piatto'],
            'prezzo' => $row['prezzo']
        ];
        if(!isset($ordine_info['ID_ordine']) || empty($ordine_info['ID_ordine'])) {
            $ordine_info = [
            'ID_ordine' => $row['ID_ordine'],
            'ID_cliente' => $row['ID_cliente'],
            'ID_ristorante' => $row['ID_ristorante'],
            'nome_ristorante' => $row['nome_ristorante'],
            'stato_ordine' => $row['stato_ordine'],
            'data_ordine' => $row['data_ordine']
        ];
        }
    }

    echo json_encode(['success' => true, 'ordine' => array_values($ordine_info), 'dettagli' => array_values($dettagli)]);
    exit;
}

function cambiaStatoOrdine($link, $ID_ordine, $nuovo_stato, $user_id, $ruolo) {

    switch($ruolo){
        case 'cliente':
            // I clienti posso solo annullare gli ordini (stato 4)

            if($nuovo_stato != 4){
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'I clienti possono solo annullare gli ordini']);
                exit;
            }
            
            $query = "UPDATE ordini o
                      JOIN carrelli c ON o.ID_carrello = c.ID_carrello
                      SET o.ID_stato_ordine = ?, c.ID_stato_carrello = 4
                      WHERE o.ID_ordine = ? AND o.ID_cliente = ? AND o.ID_stato_ordine NOT IN (3,4)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'iii', $nuovo_stato, $ID_ordine, $user_id);
            break;
        case 'ristoratore':
            // I ristoratori possono cambiare lo stato dell'ordine in annullato (4) o pagato se l'ordine è completato(3)
            if(!in_array($nuovo_stato, [4,8])){
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'I ristoratori possono solo completare o annullare gli ordini']);
                exit;
            }
            $query = "UPDATE ordini o
                      JOIN carrelli c ON o.ID_carrello = c.ID_carrello
                      JOIN ristoranti r ON o.ID_ristorante = r.ID_ristorante
                      SET o.ID_stato_ordine = ?, c.ID_stato_carrello = CASE WHEN ? = 4 THEN 4 ELSE c.ID_stato_carrello END
                      WHERE o.ID_ordine = ? AND r.ID_ristoratore = ? AND o.ID_stato_ordine NOT IN (4,8)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'iiii', $nuovo_stato, $nuovo_stato, $ID_ordine, $user_id);
            break;
        case 'cuoco':
            // I cuochi possono cambiare lo stato dell'ordine in 'rifiutato' (6), 'in elaborazione' (7) o 'completato' (3)
            if(!in_array($nuovo_stato, [3,6,7])){
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'I cuochi possono solo cambiare lo stato in rifiutato, in elaborazione o completato']);
                exit;
            }

            $query = "UPDATE ordini o
                      JOIN carrelli c ON o.ID_carrello = c.ID_carrello
                      JOIN ristoranti r ON o.ID_ristorante = r.ID_ristorante
                      JOIN cuochi cu ON cu.ID_ristorante = r.ID_ristorante
                      SET o.ID_stato_ordine = ?, c.ID_stato_carrello = CASE WHEN ? = 6 THEN 4 ELSE c.ID_stato_carrello END
                      WHERE o.ID_ordine = ? AND cu.ID_cuoco = ? AND o.ID_stato_ordine NOT IN (3,4)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'iiii', $nuovo_stato, $nuovo_stato, $ID_ordine, $user_id);
            break;
        case 'admin':
            // funzionalità non ancora implementata
            http_response_code(501);
            echo json_encode(['success' => false, 'error' => 'Funzionalità non ancora implementata']);
            exit;
    }
    if(mysqli_stmt_execute($stmt)) {
        if(mysqli_stmt_affected_rows($stmt) === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Impossibile cambiare lo stato dell\'ordine. Verifica che l\'ordine esista e che lo stato sia modificabile.']);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Stato dell\'ordine aggiornato con successo']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Errore durante l\'aggiornamento dello stato dell\'ordine']);
    }
    exit;
}