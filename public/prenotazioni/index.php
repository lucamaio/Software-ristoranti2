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
    
    <link href="../../css/global.css"rel="stylesheet">
    <link href="../../css/prenotazioni.css" rel="stylesheet">
    <script>
        window.APP_CONFIG = {
            userId: <?= (int) $_SESSION['user_id'] ?>,
            role : "<?= (string) $_SESSION['role'] ?>"
        };
    </script>  

    <script src = "../../js/global.js"></script>
    <script src="../../js/prenotazioni.js"></script>
    <script src="../../js/navbar.js" ></script>

</head>
<body>
    <?php 
    if(!isset($_SESSION['user_id'])){
        message("Devi accedere per poter visualizzare questa pagina");
    }
    if($_SESSION['role'] === 'chef'){
        message("Accesso negato!");
    }    
    ?>
    
    <div class="hero-section mb-4 mx-3">
        <div class="container">
            <h1 class="hero-title">Elenco prenotazioni</h1>
            <p class="hero-description">Qui puoi visualizzare e gestire le tue prenotazioni. Inoltri, puoi effetuare una nuova Prenotazione.</p>
        </div>
    </div>
    <div class="bg-light mb-0 py-5">
         <div class="container mt-4">
            <div id="message" class="mb-4"></div>
            <div id="table-responsive" class="table-responsive"></div>    
        </div>
    </div>
   
    

    <?php 
    $btns []=  [
        'icon' => 'bi bi-plus-lg',
        'id' => 'btn-prenota',
        'label' => 'Prenota',
        'link' => 'nuova.php'
    ];
    get_template_part('navbar', ['btns' => $btns]); ?>
    <?php get_template_part('footer'); ?>
</>
</html>
