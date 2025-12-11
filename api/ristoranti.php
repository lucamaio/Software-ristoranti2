<?php
require_once '../includes/db.php';
require_once '../model/Ristorante.php';


define('DEBUG',value: true);
$method = $_SERVER['REQUEST_METHOD'];
$table = 'ristoranti';

switch($method){
    case 'GET':
        $result = mysqli_query($link, "SELECT * FROM $table");
        $ristoranti = [];
        while($row = mysqli_fetch_assoc($result)){
            $ristoranti[] = new Ristorante($row['ID_ristorante'], $row['nome'],$row['indirizzo'],$row['telefono'],$row['email']);
        }
        echo json_encode($ristoranti);
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
        break;
}
mysqli_close($link);
?>