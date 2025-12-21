<?php
require_once '../includes/db.php';
require_once '../model/Cart.php';
require_once '../model/DettailsCart.php';
session_start();

// Controlli pre-liminari per evitare accessi indesiderati

// 1. Verifico l'essistenza di una sessione valida 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Utente non autenticato']);
    exit;
}
// 2. Verifico se l'utente è autorizzato ad accedere a questa pagina

if ($_SESSION['role'] !== 'client') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Accesso non autorizzato']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
    exit;
}

// 4. Verifico se l'azione è diversa da null
$azione = $_POST['azione'] ?? null;
if (!$azione) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Azione non valida']);
    exit;
}

$user_id = $_SESSION['user_id'];

/* ===========================
   AZIONI DA ESEGUIRE
=========================== */

switch ($azione) {

    case 'get-info':
        ricavaCarrelli($link, $user_id);
        break;

    case 'aggiungi':
        if(!isset($_POST['ID_piatto'])){
            http_response_code(500);
            echo json_encode([ 'success' => false, 'error' => "Dati mancanti per poter procedere!"]);
            exit;
        }

        aggiungiPiattoCart($link,$user_id, $_POST['ID_piatto']);
        break;

    case 'rimuovi':
        if(!isset($_POST['ID_piatto'])){
            http_response_code(500);
            echo json_encode([ 'success' => false, 'error' => "Dati mancanti per poter procedere!"]);
            exit;
        }
        if( isset($_POST['ID_carrello'])){
            rimuoviPiattoCart($link, $user_id, $_POST['ID_piatto'], $_POST['ID_carrello']);
        }
        
        rimuoviPiattoCart($link, $user_id, $_POST['ID_piatto']);
        
        break;

    case 'diminuisci':
        // Verifico che esistano i campi neccessari per poter effetuare le operazioni
        if(!isset($_POST['ID_carrello']) || !isset($_POST['ID_piatto'])){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Dati mancanti!']);
            exit;
        }

        $ID_carrello = $_POST['ID_carrello'];
        $ID_piatto = $_POST['ID_piatto'];

        // Verifico l'esistenza di un carrello con quel determinato piatto
        $query = "SELECT dc.quantita FROM carrelli c LEFT JOIN dettagli_carrelli dc ON c.ID_carrello = dc.ID_carrello WHERE c.ID_carrello = ? AND dc.ID_piatto = ? AND c.ID_cliente = ?";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'iii', $ID_carrello, $ID_piatto, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) !== 1){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Carrello non trovato!']);
            exit;
        }

        $row = mysqli_fetch_assoc($result);
        $quantita = $row['quantita'];

        if($quantita > 1){
            $quantita --;
            $stmt2 = mysqli_prepare($link, "UPDATE dettagli_carrelli SET quantita = ? WHERE ID_carrello = ? AND ID_piatto = ?");
            mysqli_stmt_bind_param($stmt2, 'iii', $quantita, $ID_carrello, $ID_piatto);  
        }else{
            $stmt2 = mysqli_prepare($link, "DELETE FROM dettagli_carrelli WHERE ID_carrello = ? AND ID_piatto = ? AND ID_cliente = ? ");
            mysqli_stmt_bind_param($stmt2, 'iii', $ID_carrello, $ID_piatto, $user_id);
        }

        if(!mysqli_stmt_execute($stmt2)){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Errore durante l\'aggiornamento']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Quantità piatto aggiornata!']);
        exit;
    default:
        http_response_code(400);
        break;
}

/**
 *  INIZIO PARTE FUNZIONI
 */

// Funzione 1: Ricava i carrelli di un cliente dal db

function ricavaCarrelli($link, $user_id){
    //1. Mi ricavo il carrello con i suoi dettagli
    $query = "SELECT c.ID_carrello, c.data_creazione, c.ID_ristorante, dc.ID_piatto, dc.quantita, r.nome AS nome_ristorante, p.nome AS nome_piatto, p.prezzo
    FROM carrelli c 
    LEFT JOIN dettagli_carrelli dc ON c.ID_carrello = dc.ID_carrello 
    LEFT JOIN piatti p ON dc.ID_piatto = p.ID_piatto
    LEFT JOIN ristoranti r ON c.ID_ristorante = r.ID_ristorante 
    WHERE c.ID_cliente = ? AND c.ID_stato_carrello = ?";

    $stato = 1;
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $user_id, $stato);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $carts = [];
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $ID_carrello = $row['ID_carrello'];

            $cart = new Cart($ID_carrello, $user_id, $stato, $row['data_creazione'], $row['ID_ristorante']);
            
            // Se il carrello non esiste ancora, lo inizializzo
            if(!isset($carts[$ID_carrello])) {
                $carts[$ID_carrello] = [
                    'cart' => new Cart($ID_carrello, $user_id, $stato, $row['data_creazione'], $row['ID_ristorante']),
                    'dettagli' => [],
                    'nome_ristorante'=> $row['nome_ristorante']
                ];
            }

            // Aggiungo i dettagli del piatto
            if(!empty($row['ID_piatto'])) { // verifica se esiste un piatto
                $carts[$ID_carrello]['dettagli'][] = [
                    'ID_piatto' => $row['ID_piatto'],
                    'nome_piatto' => $row['nome_piatto'],
                    'prezzo' => $row['prezzo'],
                    'quantita' => $row['quantita']
                ];
            }

            
        }        
    }

    echo json_encode(['success' => true, 'data' => array_values($carts)]);
    exit;
}

// Funzione 2: Aggiungi un piatto ad un carrello

function aggiungiPiattoCart($link, $user_id, $ID_piatto, ){
    mysqli_begin_transaction($link);
    try{
        // 1. Verifico l'esistenza del piatto e mi ricavo l'id del ristorante

        $stmt = mysqli_prepare($link, "SELECT ID_ristorante FROM piatti WHERE ID_piatto = ?");
        mysqli_stmt_bind_param($stmt, "i", $ID_piatto);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) !== 1) {
            throw new Exception('Piatto non trovato');
        }

        $row = mysqli_fetch_assoc($result);
        $ID_ristorante = $row['ID_ristorante'];

        // 2. Verifico se esistono dei carrelli attivi
        $stmt2 = mysqli_prepare(
                $link, "SELECT ID_carrello FROM carrelli WHERE ID_cliente = ? AND ID_ristorante = ? AND ID_stato_carrello = 1"
            );
            mysqli_stmt_bind_param($stmt2, "ii", $user_id, $ID_ristorante);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);

            if (mysqli_num_rows($result2) === 0) {

                // Se non esiste lo creo
                $stato_cart = 1;
                $stmt3 = mysqli_prepare(
                    $link,
                    "INSERT INTO carrelli (ID_cliente, ID_stato_carrello, ID_ristorante)
                     VALUES (?, ?, ?)"
                );
                mysqli_stmt_bind_param($stmt3, "iii", $user_id, $stato_cart, $ID_ristorante);

                if (!mysqli_stmt_execute($stmt3)) {
                    throw new Exception('Errore creazione carrello');
                }

                $ID_carrello = mysqli_insert_id($link);

            } elseif (mysqli_num_rows($result2) === 1) {
                // Se esiste lo utilizzo
                $row2 = mysqli_fetch_assoc($result2);
                $ID_carrello = $row2['ID_carrello'];

            } else {
                throw new Exception('Sono presenti più carrelli attivi');
            }

        // 3. 
        //  a. Se il piatto è gia presente incremento la sua quantità (chiama funzione incrementaQuantitaPiatto)
        //  b. Altrimenti lo aggiungo
        
        $stmt4 = mysqli_prepare($link,"SELECT quantita FROM dettagli_carrelli WHERE ID_carrello = ? AND ID_piatto = ?");
        mysqli_stmt_bind_param($stmt4, "ii", $ID_carrello, $ID_piatto);
        mysqli_stmt_execute($stmt4);
        $result3 = mysqli_stmt_get_result($stmt4);

        if (mysqli_num_rows($result3) > 0) {
            incrementaQuantitaPiatto($link,$user_id,$ID_piatto,$ID_carrello);
        }else{
            $quantita = 1;
            $stmt5 = mysqli_prepare($link,
                "INSERT INTO dettagli_carrelli (ID_carrello, ID_piatto, quantita) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt5, "iii", $ID_carrello, $ID_piatto, $quantita);

            if (!mysqli_stmt_execute($stmt5)) {
                throw new Exception('Errore inserimento piatto');
            }
        }
        
        // Commit finale
        mysqli_commit($link);

        echo json_encode(['success' => true, 'message' => 'Piatto inserito nel carrello']);
        exit;


    }catch (Exception $e) {

        // Rollback in caso di errore
        mysqli_rollback($link);

        http_response_code(500);
        echo json_encode([ 'success' => false, 'error' => $e->getMessage()]);
        exit;
    }
    
}

// Funzione 3: Incrementa la quantità di un piatto ad un carrello noto

function incrementaQuantitaPiatto($link, $user_id, $ID_piatto, $ID_carrello){
    $stato = 1;
    $query = "UPDATE dettagli_carrelli dc
              JOIN carrelli c ON c.ID_carrello = dc.ID_carrello
              SET dc.quantita = dc.quantita + 1
              WHERE dc.ID_carrello = ? AND c.ID_cliente = ? AND dc.ID_piatto = ? AND c.ID_stato_carrello = ?";
    
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'iiii', $ID_carrello, $user_id, $ID_piatto, $stato);

    if(!mysqli_stmt_execute($stmt)){
        throw new Exception('Errore durante l\'incremento della quantità');
    }
}


// Funzione 4: Rimuovi un piatto da un carrello
function rimuoviPiattoCart($link, $user_id, $ID_piatto, $ID_carrello = null){
    // Seleziono la query da eseguire in base alla esistenza del ID_carrello

    if($ID_carrello !== null){
        $stmt = mysqli_prepare($link,
            "DELETE dc
            FROM dettagli_carrelli dc
            JOIN carrelli c ON c.ID_carrello = dc.ID_carrello
            WHERE dc.ID_carrello = ? AND dc.ID_piatto = ? AND c.ID_cliente = ? AND c.ID_stato_carrello = 1"
        );
        mysqli_stmt_bind_param($stmt, 'iii', $ID_carrello, $ID_piatto, $user_id);
    }else{
        $stmt = mysqli_prepare($link,
            "DELETE dc
            FROM dettagli_carrelli dc
            JOIN carrelli c ON c.ID_carrello = dc.ID_carrello
            WHERE dc.ID_piatto = ? AND c.ID_cliente = ? AND c.ID_stato_carrello = 1"
        );
        mysqli_stmt_bind_param($stmt, 'ii', $ID_piatto, $user_id);
    }

    // Eseguo la query
    
    if(!mysqli_stmt_execute($stmt)){
        http_response_code( 500);
        echo json_encode(['success' => false,'error' => 'Errore durante l\'esecuzione della query']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Piatto rimosso']);
    exit;
}

// Funzione 5: Riduce la quantità di un piatto

function riduciQuantitaPiatto($link, $user_id, $ID_piatto, $ID_carrello){
    // Logica da implementare
}


/**
 *  FINE PARTE FUNZIONI
 */
