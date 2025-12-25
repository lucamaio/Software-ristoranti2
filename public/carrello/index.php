<?php
    require_once '../../includes/functions.php'; 
    session_start(); 

    if(!isset($_SESSION['user_id'])){
        message("Devi accedere per poter visualizzare questa pagina!");
    }
    if(isset($_SESSION['role']) && $_SESSION['role'] !== 'client'){
        message("Non sei autorizzato ad accedere a questa pagina");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carrello</title>
    <!-- Icone -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="../../css/prenotazioni.css" rel="stylesheet">
    <link href="../../css/global.css"rel="stylesheet">
    <script>
        window.APP_CONFIG = {
            userId: <?= (int) $_SESSION['user_id'] ?>,
            role : "<?= (string) $_SESSION['role'] ?>"
        };
    </script>  

    <script src = "../../js/global.js"></script>
    <script src="../../js/cart.js"></script>
    <script src="../../js/navbar.js" ></script>

</head>
<body class="p-4">
    <div class="hero-section mb-4 mx-3">
        <div class="container">
            <h1 class="hero-title">I miei carrelli</h1>
            <p class="hero-description">Qui puoi visualizzare e gestire i tuoi carrelli.</p>
        </div>
    </div>
    <div id="message" class="mb-4"></div>

    <div id="table-carts" class="table-responsive mb-3"></div> 
    <?php get_template_part('navbar'); ?>
    <?php get_template_part('footer'); ?>
</body>

</body>