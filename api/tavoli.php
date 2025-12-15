<?php
require_once '../includes/db.php';
require_once '../model/Tavolo.php';
session_start();

if(!isset($_SESSION['user_id'])){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Devi accedere per potere eseguire le operazioni']);
    exit;
}
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$method = $_SERVER['REQUEST_METHOD'];

// Verifico che l'utete ha i permessi neccessari
if($role === 'client' || $role === 'chef' ){
    echo json_encode(['success' => false, 'error' => 'Accesso Negato!']);
    exit;
}

if(!isset($method) && $method != 'POST'){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => '$method inesistente']);
    exit;
}

$azione = $_POST['request_type'];

switch($azione){
    case 'get':
        // Mi salvo l'id del ristorante
        $ID_ristorante = $_POST['ID_ristorante'];
        
        if(!$ID_ristorante){
            echo json_encode(['success' => false, 'error' => 'Dati mancanti!']);
            exit;
        }

        
        $stmt = mysqli_prepare($link, "SELECT * FROM tavoli WHERE ID_ristorante = ?");
        mysqli_stmt_bind_param($stmt, "i", $ID_ristorante);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $tavoli = [];
        while($row = mysqli_fetch_assoc($result)){
            $tavoli[] = new Tavolo($row['ID_tavolo'], $row['numero_tavolo'], $row['posti'], $row['ID_ristorante'], $row['ID_ristoratore']);
        }

        echo json_encode([
            'success' => true,
            'data' => $tavoli
        ]);
        exit;
    case 'set':

        $ID_prenotazione = $_POST['ID_prenotazione'];
        $ID_tavolo = $_POST['ID_tavolo'];

        if(!$ID_prenotazione || !$ID_tavolo){
            echo json_encode(['success' => false, 'error' => 'Dati mancanti!']);
            exit;
        }

        // Imposto il tavolo alla prenotazione
        $stmt = mysqli_prepare($link,"UPDATE prenotazioni SET ID_tavolo = ? WHERE ID_prenotazione = ?");
        mysqli_stmt_bind_param($stmt, "ii", $ID_tavolo, $ID_prenotazione);
        
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


        exit;
    default: 
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Devi accedere per poter eseguire la query']);
        break;
}

mysqli_close($link);
?>