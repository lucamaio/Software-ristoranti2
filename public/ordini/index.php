<?php 
require_once __DIR__ . '/../../includes/functions.php';
session_start(); 
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <title>Ordini</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

        <link href="../../css/global.css"rel="stylesheet">
        <link href="../../css/ordini.css" rel="stylesheet">
        
        <script src="../../js/navbar.js" ></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/orders.js"></script>
    </head>
    <body>
    <?php get_template_part('navbar'); ?>

    <div class="main-content">
        <div class="hero-section d-flex flex-column">
            <div class="container">
                <h1 class="hero-title">Ordini</h1>
                <p class="hero-description">In questa pagina puoi consultare e modificare i tuoi ordini. Inoltre, puoi procedere ad effettuare i pagamenti di essi.</p>
            </div>
        </div>

        <div class="bg-light py-3">
            <div class="container">
                <div id="message" class="mb-4"></div>
                <div id="orders-container" class="row g-3"></div>
            </div>
        </div> 
    </div>

    <?php get_template_part('footer'); ?>
</body>

</html>
