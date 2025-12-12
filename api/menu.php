<?php
/*require_once '../includes/db.php';
require_once '../model/Piatto.php';
require_once '../model/TipologiaPiatto.php';
// require_once '../includes/function.php';


define('DEBUG',value: true);
$method = $_SERVER['REQUEST_METHOD'];
$table = 'menu';

if(!isset($method) || empty($method)){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => '$method inesistente']);
    exit;
}

switch($method){
    case 'GET':

        if(isset($_GET['id'])){
            // Mi ricavo il menù del ristorante con l'id fornito
            $id_ristorante = $_GET['id'];

            $stmt = mysqli_prepare($link,"SELECT * FROM piatti WHERE ID_ristorante = ?");
            mysqli_stmt_bind_param($stmt,"i",$id_ristorante);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) > 0){
                // Mi ricavo le categorie dei piatti del ristorante
                $stmt_cat = mysqli_prepare($link, "SELECT tp.* FROM tipologie_piatti tp LEFT JOIN ristoranti_tipologie_piatti rtp ON tp.ID_tipologia_piatto  = rtp.ID_tipologia_piatto WHERE rtp.ID_ristorante = ? ");
                mysqli_stmt_bind_param($stmt_cat,"i",$id_ristorante);
                mysqli_stmt_execute($stmt_cat);
                $result_cat = mysqli_stmt_get_result($stmt_cat);

                if(mysqli_num_rows($result_cat) > 0){
                    $categorie = [];
                    while($row = mysqli_fetch_assoc($result_cat)){
                        $categorie[] = new TipologiaPiatto($row['ID_tipologia_piatto'], $row['nome'], $row['descrizione'], $row['ID_admin'], $row['data_inserimento'], $row['data_ultima_modifica']);
                    }
                }
                // Mi ricavo il nome del ristorante
                $stmt_resturant = mysqli_prepare($link,"SELECT r.nome FROM ristoranti r WHERE r.ID_ristorante = ?");
                mysqli_stmt_bind_param($stmt_resturant,"i",$id_ristorante);
                mysqli_stmt_execute($stmt_resturant);
                $result_restaurant = mysqli_stmt_get_result($stmt_resturant);

                if (mysqli_num_rows($result_restaurant) == 1) {
                    // Preleva la riga come array associativo
                    $row = mysqli_fetch_assoc($result_restaurant);
                    $nomeRistorante = $row['nome'];
                }


            }     

        }else if(isset($_GET['id_piatto'])){

        }else{

        }

        if(!isset($result)){
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => '$result non trovato']);
            exit;
        }

        if(mysqli_num_rows($result) > 0){
                $menu = [];
                while($row = mysqli_fetch_assoc($result)){
                    $menu[] = new Piatto($row['ID_piatto'], $row['nome'], $row['descrizione'], $row['prezzo'], $row['ID_ristorante'], $row['ID_stato_piatto'], $row['ID_tipologia_piatto'], $row['ID_ristoratore'],$row['ID_cuoco']);
                }

                if(isset($categorie) && isset($nomeRistorante)){
                    $result_finaly = [
                        'menu' => $menu,
                        'categorie' => $categorie,
                        'nomeRistorante' =>$nomeRistorante
                    ];
                    // var_dump($result_finaly);
                    echo json_encode($result_finaly,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    
                }else{
                    echo json_encode($menu,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                exit;
            }
    default:
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
    break;
}
mysqli_close($link);*/
?>
<?php
require_once '../includes/db.php';

// Modelli
require_once '../model/Piatto.php';
require_once '../model/TipologiaPiatto.php';

$method = $_SERVER['REQUEST_METHOD'];

if($method !== 'GET'){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
    exit;
}

if(!isset($_GET['id'])){
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID ristorante mancante']);
    exit;
}

$id_ristorante = intval($_GET['id']);

// 1. Prendo tutti i piatti del ristorante
$stmt = mysqli_prepare($link, "SELECT * FROM piatti WHERE ID_ristorante = ?");
mysqli_stmt_bind_param($stmt, "i", $id_ristorante);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$menu = [];
while($row = mysqli_fetch_assoc($result)){
    $menu[] = [
        'ID_piatto' => $row['ID_piatto'],
        'nome' => $row['nome'],
        'descrizione' => $row['descrizione'],
        'prezzo' => $row['prezzo'],
        'ID_tipologia_piatto' => $row['ID_tipologia_piatto']
    ];
}

// 2. Prendo categorie
$stmt_cat = mysqli_prepare($link, "
    SELECT tp.ID_tipologia_piatto, tp.nome
    FROM tipologie_piatti tp
    INNER JOIN ristoranti_tipologie_piatti rtp 
        ON tp.ID_tipologia_piatto = rtp.ID_tipologia_piatto
    WHERE rtp.ID_ristorante = ?
");
mysqli_stmt_bind_param($stmt_cat, "i", $id_ristorante);
mysqli_stmt_execute($stmt_cat);
$result_cat = mysqli_stmt_get_result($stmt_cat);

$categories = [];
while($row = mysqli_fetch_assoc($result_cat)){
    $catId = $row['ID_tipologia_piatto'];
    $categories[] = [
        'ID_tipologia_piatto' => $catId,
        'nome' => $row['nome'],
        'piatti' => array_values(array_filter($menu, fn($p) => $p['ID_tipologia_piatto'] == $catId))
    ];
}

// 3. Prendo nome ristorante
$stmt_rest = mysqli_prepare($link, "SELECT nome FROM ristoranti WHERE ID_ristorante = ?");
mysqli_stmt_bind_param($stmt_rest, "i", $id_ristorante);
mysqli_stmt_execute($stmt_rest);
$result_rest = mysqli_stmt_get_result($stmt_rest);
$row = mysqli_fetch_assoc($result_rest);
$nomeRistorante = $row['nome'] ?? '';

// 4. Risultato finale
$result_final = [
    'nomeRistorante' => $nomeRistorante,
    'categorie' => $categories
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result_final, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>
