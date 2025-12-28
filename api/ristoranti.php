<?php
require_once '../includes/db.php';
require_once '../model/Ristorante.php';
require_once '../model/Immagine.php';
require_once '../model/Comune.php';
require_once '../model/card/RistoranteCard.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

define('DEBUG', true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
    exit;
}

$table = 'ristoranti';
$azione = $_POST['request_type'];

switch($azione){
    case 'get':
       
        $query = "SELECT r.*, c.comune AS nome_comune, i.percorso AS url_immagine 
            FROM ristoranti r 
            LEFT JOIN comuni c ON r.ID_citta = c.ID_citta 
            LEFT JOIN immagini i ON r.ID_ristorante = i.ID_ristorante";
        $result = mysqli_query($link, $query);

        if(!isset($result)){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => '$result non trovato']);
            exit;
        }
        $ristoranti = [];
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                $ristorante = new Ristorante(
                    $row['ID_ristorante'], $row['nome'], $row['indirizzo'],
                    $row['num_civ'],  $row['telefono'], $row['email'],
                    $row['descrizione_breve'], $row['descrizione_estesa'],
                    $row['cod_fisc'], $row['partita_IVA'], $row['ragione_soc'],
                    $row['capienza'], $row['ID_ristoratore']
                );
                $percorsoImmagine = "../" . $row['url_immagine'];
                $ristoranti [] = [
                    'ristorante' => $ristorante,
                    'nome_comune' => $row['nome_comune'],
                    'url_immagine' => $percorsoImmagine
                ];

            }
            echo json_encode(['success' => true, 'data' => $ristoranti]);
            exit;
        }else {
            echo json_encode(['success' => false, 'data' => []]);
            exit;
        }
   case 'get-info':

    if (!isset($_POST['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID ristorante mancante']);
        exit;
    }

    $id = (int) $_POST['id'];

    $stmt = mysqli_prepare($link, "
        SELECT 
            r.*,

            -- dati città
            c.comune   AS nome_comune,
            c.provincia,
            c.regione,
            c.cap,

            -- immagine
            i.percorso AS url_immagine,

            -- recensioni
            re.ID_recensione,
            re.titolo,
            re.commento,
            re.voto

        FROM ristoranti r
        LEFT JOIN comuni c 
            ON r.ID_citta = c.ID_citta
        LEFT JOIN immagini i 
            ON r.ID_ristorante = i.ID_ristorante
        LEFT JOIN recensioni re 
            ON r.ID_ristorante = re.ID_ristorante
        WHERE r.ID_ristorante = ?
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(['success' => false, 'data' => null]);
        exit;
    }

    $ristorante = null;
    $recensioni = [];

    while ($row = mysqli_fetch_assoc($result)) {

        // Inizializzo UNA SOLA VOLTA il ristorante
        if ($ristorante === null) {
            $ristorante = new Ristorante(
                $row['ID_ristorante'],
                $row['nome'],
                $row['indirizzo'],
                $row['num_civ'],
                $row['telefono'],
                $row['email'],
                $row['descrizione_breve'],
                $row['descrizione_estesa'],
                $row['cod_fisc'],
                $row['partita_IVA'],
                $row['ragione_soc'],
                $row['capienza'],
                $row['ID_ristoratore']
            );

            $citta = [
                'nome'      => $row['nome_comune'],
                'provincia' => $row['provincia'],
                'regione'   => $row['regione'],
                'cap'       => $row['cap'],
                'latitudine' => $row['latitudine'],
                'longitudine' => $row['longitudine']
            ];

            $url_immagine = "../../" . $row['url_immagine'];
        }

        // Aggiungo tutte le recensioni
        if ($row['ID_recensione'] !== null) {
            $recensioni[] = [
                'id'       => $row['ID_recensione'],
                'titolo'   => $row['titolo'],
                'commento' => $row['commento'],
                'voto'     => (int) $row['voto']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'ristorante' => $ristorante,
            'citta'      => $citta,
            'url_immagine' => $url_immagine,
            'recensioni' => $recensioni
        ]
    ]);

    exit;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
        break;
}
mysqli_close($link);
?>