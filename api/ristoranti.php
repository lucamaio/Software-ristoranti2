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

if(!isset($method)){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non specificato o non valido!']);
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
                // $ristoranti[] = new RistoranteCard($ristorante, $row["nome_comune"], $row['url_immagine']);
                $ristoranti [] = [
                    'ristorante' => $ristorante,
                    'nome_comune' => $row['nome_comune'],
                    'url_immagine' => "http://localhost:8080/prenota/view/".$row['url_immagine']
                ];

            }
            echo json_encode(['success' => true, 'data' => $ristoranti]);
            exit;
        }else {
            echo json_encode(['success' => false, 'data' => []]);
            exit;
        }
    case 'get-info':
        // Verifico l'esistenza del id
        if(!isset($_POST['id'])){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Dati Mancanti!']);
            exit;
        }
        $id = $_POST['id'];
        $stmt = mysqli_prepare($link,"SELECT * FROM $table WHERE ID_ristorante = ? ");
        mysqli_stmt_bind_param($stmt,"i",$id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
         if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                $ristorante = new Ristorante(
                    $row['ID_ristorante'], $row['nome'], $row['indirizzo'],
                    $row['num_civ'],  $row['telefono'], $row['email'],
                    $row['descrizione_breve'], $row['descrizione_estesa'],
                    $row['cod_fisc'], $row['partita_IVA'], $row['ragione_soc'],
                    $row['capienza'], $row['ID_ristoratore']
                );
                // $ristoranti[] = new RistoranteCard($ristorante, $row["nome_comune"], $row['url_immagine']);
                $retur = [
                    'ristorante' => $ristorante,
                    'nome_comune' => $row['nome_comune'],
                    'url_immagine' => "http://localhost:8080/prenota/view/".$row['url_immagine']
                ];
            }
            echo json_encode(['success' => true, 'data' => $retur]);
            exit;
        }else {
            echo json_encode(['success' => false, 'data' => []]);
            exit;
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
        break;
}
mysqli_close($link);
?>