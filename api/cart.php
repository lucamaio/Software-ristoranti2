<?php
require_once '../includes/db.php';
require_once '../model/Cart.php';
require_once '../model/DettailsCart.php';
session_start();

if(!isset($_SESSION['user_id'])){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Devi accedere per potere eseguire le operazioni']);
    exit;
}
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if(!isset($method) && $method != 'POST'){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => '$method inesistente']);
    exit;
}

$azione = $_POST['azione'];

if(!isset($azione) || empty($azione)){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => '$azione inesistente o vuota']);
    exit;
}

switch ($azione){
    case 'add':
        // Verifico se l'utente è un cliente
        
        if($_SESSION['role'] !== 'client'){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Utente non autorizzato ad effetuare questa operazione!']);
            exit;
        }
        // Salvo l'id del piatto d'aggiungere
        $ID_piatto = $_POST['id'];

        // Verifico l'esistenza del piatto e mi ricavo l'id del ristorante neccessario per le operazioni successive
        $stmt = mysqli_prepare($link, "SELECT p.ID_ristorante FROM piatti p WHERE p.ID_piatto = ?");
        mysqli_stmt_bind_param($stmt,"i", $ID_piatto);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) != 1){
            echo json_encode(['success' => false, 'error' => 'Piatto non trovato!']);
            exit;
        }

        $row = mysqli_fetch_assoc($result);
        $ID_ristorante = $row['ID_ristorante'];

        // Adesso, verifico se il cliente ha un carrello attivo
        $stmt2 = mysqli_prepare($link, "SELECT * FROM carrelli WHERE ID_cliente = ? AND ID_ristorante = ? AND ID_stato_carrello = 1");
        mysqli_stmt_bind_param($stmt2,"ii",$user_id,$ID_ristorante);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);

        if(mysqli_num_rows($result2) == 0){
            $stato_cart = 1;
            // 1. Creo un carrello
            $stmt3 = mysqli_prepare($link, "INSERT INTO carrelli (ID_cliente, ID_stato_carrello, ID_ristorante)  VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt3, "iii", $user_id, $stato_cart, $ID_ristorante);
            mysqli_stmt_execute($stmt3);
            

            // 2. Aggiungo i dettagli al carrello

            exit;

        }else if(mysqli_num_rows($result2) == 1){
              
        }else{
            echo json_encode(['success' => false, 'error' => 'Sono presenti più carrelli attivi!']);
            exit;
        }
    case 'remove':
        break;
    default:
        break;
}