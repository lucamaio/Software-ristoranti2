<?php
    require_once '../../includes/functions.php'; 
    session_start(); 

    if(!isset($_SESSION['user_id'])){
        message("Devi accedere per poter visualizzare questa pagina!");
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Elenco Prenotazioni</title>
    <!-- Icone -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/index.css" rel="stylesheet">
    <link href="../../css/prenotazioni.css" rel="stylesheet">
    <script>
        window.APP_CONFIG = {
            userId: <?= (int) $_SESSION['user_id'] ?>,
            role : "<?= (string) $_SESSION['role'] ?>"
        };
    </script>  

    <script src = "../../js/global.js"></script>
    <script src="../../js/prenotazioni.js"></script>

</head>
<body class="p-4">
    <?php 
    
    if(!isset($_SESSION['user_id'])){
        message("Devi accedere per poter visualizzare questa pagina");
    }?>
    <div id="btn-home"></div>
    
    <h1 class="mb-4">Elenco Prenotazioni</h1>
    <button id="btn-prenota" class="btn btn-secondary btn-modern mb-4">Nuova Prenotazione</button>
    <div id="message" class="mb-2"></div>
    <div id="table-responsive" class="table-responsive"></div>    
</body>
</html>
