<?php 
require_once __DIR__ . '/../includes/functions.php';
session_start(); ?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8">
        <title>Pagamenti</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

        <link href="../css/global.css"rel="stylesheet">

        <script src="../js/navbar.js" ></script>
    </head>
    <body>
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title">Pagamenti</h1>
                <p class="hero-description">In questa pagina puoi consultare i tuoi pagamenti effettuati.</p>
            </div>
        </div>

        <div class="container mt-4">
            <div class="d-flex flex-wrap gap-2 mb-4">
                
            </div>
        </div>
         <?php get_template_part('navbar'); ?>

    <?php get_template_part('footer'); ?>
    </body>
</html>
