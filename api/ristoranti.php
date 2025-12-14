<?php
require_once '../includes/db.php';
require_once '../model/Ristorante.php';


define('DEBUG',value: true);
$method = $_SERVER['REQUEST_METHOD'];
$table = 'ristoranti';

switch($method){
    case 'GET':
        
        if(!isset($_GET['id'])){
            $result = mysqli_query($link, "SELECT * FROM $table");
        }else{
            $id = $_GET['id'];
            $stmt = mysqli_prepare($link,"SELECT * FROM $table WHERE ID_ristorante = ? ");
            mysqli_stmt_bind_param($stmt,"i",$id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        }

        if(!isset($result)){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => '$result non trovato']);
            exit;
        }

        if(mysqli_num_rows($result) > 1){
            $ristoranti = [];
            while($row = mysqli_fetch_assoc($result)){
                $ristoranti[] = new Ristorante($row['ID_ristorante'], $row['nome'],$row['indirizzo'],$row['num_civ'],$row['telefono'],$row['email'],$row['descrizione_breve'], $row['descrizione_estesa'],$row['cod_fisc'],$row['partita_IVA'],$row['ragione_soc'],$row['capienza'],$row['ID_ristoratore']);
            }
            // console.log($ristoranti);
            echo json_encode($ristoranti);
        }else if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);
            $ristorante = new Ristorante($row['ID_ristorante'], $row['nome'],$row['indirizzo'],$row['num_civ'],$row['telefono'],$row['email'],$row['descrizione_breve'], $row['descrizione_estesa'],$row['cod_fisc'],$row['partita_IVA'],$row['ragione_soc'],$row['capienza'],$row['ID_ristoratore']);
            echo json_encode($ristorante);
        }
        
        
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
        break;
}
mysqli_close($link);
?>