<?php
require_once '../includes/db.php';
require_once '../model/Ordine.php';
session_start();

// Controlli pre-liminari per evitare accessi indesiderati

// 1. Verifico l'essistenza di una sessione valida 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
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
$role = $_SESSION['role'];
$azione = $_POST['azione'] ?? 'null';

if (!$azione) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Azione non valida']);
    exit;
}

switch ($azione) {
    case 'get':
       ricava_ordini($link, $_SESSION['user_id'], $_SESSION['role']);
        break;
    case 'ordina':

        if(!isset($_POST['ID_carrello'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID_carrello mancante']);
            exit;
        }

        if($role !== 'client') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Solo i clienti possono effettuare ordini']);
            exit;
        }
        
        genera_ordine($link, $_SESSION['user_id']);
        break;
    case 'get-dettagli':
        if(!isset($_POST['ID_ordine'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID_ordine mancante']);
            exit;
        }

        if($role !== 'client') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Solo i clienti possono effettuare ordini']);
            exit;
        }
        $ID_ordine = $_POST['ID_ordine'];

        ricavaDettagliOrdine($link, $ID_ordine, $user_id, $role);
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Azione non riconosciuta']);
        exit;
}


function ricava_ordini($link, $user_id, $role) {

    if ($role === 'client') {

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


    } elseif ($role === 'ristoratore') {

        $query = "
            SELECT o.*, r.nome AS nome_ristorante, SUM(SELECT dc.quantita * p.prezzo FROM dettagli_carrelli dc
                LEFT JOIN piatti p ON p.ID_piatto = dc.ID_piatto
                WHERE dc.ID_carrello = o.ID_carrello) AS totale
            FROM ordini o
            LEFT JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
            LEFT JOIN dettagli_carrelli dc ON o.ID_carrello = dc.ID_carrello
            WHERE r.ID_ristoratore = ?
        ";

        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);

    } elseif ($role === 'admin') {

        $query = "
            SELECT o.*, r.nome AS nome_ristorante
            FROM ordini o
            LEFT JOIN ristoranti r ON r.ID_ristorante = o.ID_ristorante
        ";

        $stmt = mysqli_prepare($link, $query);

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
            'totale' => $row['totale'] ?? null,
            'stato_ordine' => $row['stato_ordine'] ?? null
        ];
    }

    echo json_encode(['success' => true, 'data' => array_values($data)]);
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

function ricavaDettagliOrdine($link, $ID_ordine, $user_id, $role) {
    // Preparo la query in base al ruolo
    if($role === 'client'){
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
    }else if($role === 'ristorator'){
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
    } else if($role === 'chef'){
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Funzionalità non ancora implementata']);
        exit;
    }else if($role === 'admin'){
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
        if ($role === 'client' && $row['ID_cliente'] != $user_id) {
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