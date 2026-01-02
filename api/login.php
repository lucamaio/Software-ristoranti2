<?php
require_once '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$richiesta =$_POST['request_type'];
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if($method != 'POST'){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
    exit;
}

// verifico che la richiesta non sia vuota
if(!$richiesta || empty($richiesta)){
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tipo di richiesta mancante']);
    exit;
}

switch($richiesta){
    case 'Login':

        // Inanzitutto verifico che email e password siano stati forniti

        if(!$email || !$password){
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Email e password sono obbligatorie']);
            exit;
        }

        // Verifico se l'utente esiste ed è un cliente
        
        $tables = ['clienti', 'ristoratori', 'cuochi', 'admin'];
        $rules = ['cliente', 'ristoratore', 'cuoco', 'admin'];
        $ids = ['ID_cliente', 'ID_ristoratore', 'ID_cuoco', 'ID_admin'];
        $i = 0;

        foreach($tables as $table){
            $stmt = mysqli_prepare($link, "SELECT * FROM `$table` WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 1) {

                $row = mysqli_fetch_assoc($result);

                if(password_verify($password, $row['password'])){
                    $durata_sessione = 15 * 60; // 15 minuti

                    session_set_cookie_params([
                        'lifetime' => $durata_sessione,
                        'path'     => '/',
                        'secure'   => false, // true se HTTPS
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);

                    // Avvio la sessione e salvo l'ID dell'utente
                    session_start();
                    $_SESSION['user_id'] = $row[$ids[$i]];
                    $_SESSION['ruolo'] = $rules[$i];
                    $_SESSION['email'] = $row['email'];

                    if($rules[$i] === 'cuoco') {
                        $_SESSION['ID_ristorante'] = $row['ID_ristorante'];
                    }
                    
                    // Password corretta, login riuscito
                    echo json_encode(['success' => true, 'message' => 'Login riuscito']);
                    exit;
                } else {
                    // Password errata
                    http_response_code(401);
                    echo json_encode(['success' => false, 'error' => 'Password errata']);
                    exit;
                }
            }else if(mysqli_num_rows($result) > 1){
                // Più utenti trovati con la stessa email
                http_response_code(409);
                echo json_encode(['success' => false, 'error' => 'Conflitto: più utenti trovati con la stessa email']);
                exit;
            }
            $i++;
        }

        // utente non trovato
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Utente non trovato']);
        exit;
        
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
        exit;
}

mysqli_close($link);
?>