<?php
require_once '../includes/db.php';
require_once '../model/Prenotazione.php';

$method = $_SERVER['REQUEST_METHOD'];
$table = 'prenotazioni';
session_start();

switch($method){
    case 'GET':
        // Verifico l'esistenza della sessione
        if(!isset($_SESSION['role']) || !isset($_SESSION['user_id'])){
            echo json_encode(['success' => false, 'error' => 'Devi accedere per poter eseguire la query']);
            exit;
        }

        $role = $_SESSION['role'];
        $user_id = $_SESSION['user_id'];

        switch ($role){
            case 'client':
                $stmt = mysqli_prepare($link, "SELECT * FROM `$table` WHERE ID_Cliente = ?");
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                // $result = mysqli_query($link, "SELECT * FROM $table WHERE ID_Cliente = ?");
                $prenotazioni = [];
                while($row = mysqli_fetch_assoc($result)){
                    $prenotazioni[] = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
                }
                echo json_encode($prenotazioni);
                exit;
            case 'restaurant':
                $stmt = mysqli_prepare($link, "SELECT p.* FROM `$table` p LEFT JOIN ristoranti r ON p.ID_ristorante = r.ID_ristorante WHERE r.ID_ristoratore = ?");
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $prenotazioni = [];
                while($row = mysqli_fetch_assoc($result)){
                    $prenotazioni[] = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
                }
                echo json_encode($prenotazioni);
                exit;
            case 'admin':
                $result = mysqli_query($link, "SELECT * FROM $table WHERE");
                $prenotazioni = [];
                while($row = mysqli_fetch_assoc($result)){
                    $prenotazioni[] = new Prenotazione($row['ID_prenotazione'], $row['data_prenotazione'],$row['ora_prenotazione'],$row['persone'],$row['ID_ristorante'],$row['ID_cliente']);
                }
                echo json_encode($prenotazioni);
                exit;
        }
               

        echo json_encode(['success' => false, 'error' => 'Utente non autorizzato']);
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
        break;
}
mysqli_close($link);
?>