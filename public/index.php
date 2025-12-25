<?php 
require_once __DIR__ . '/../includes/functions.php';
session_start(); ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Software Ristoranti</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../css/index.css"rel="stylesheet">
    <link href="../css/global.css"rel="stylesheet">
        
    <script src="../js/index.js" ></script>
    <script src="../js/navbar.js" ></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   
</head>
<body class="p-4">
    <!-- Titolo e descrizione homepage -->
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">Software Ristoranti</h1>
            <p class="hero-description">Benvenuto nella piattaforma gestionale per i ristoranti. Qui potrai effettuare prenotazioni e ordinazioni direttamente senza dover chiamare. I gestori potranno accettare o rifiutare le tue prenotazioni.</p>
        </div>
    </div>

    <div class="container mt-4">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <h3 class="h3 mb-3">Esplora i ristoranti</h3>
            <div id="show-ristoranti" class="d-flex flex-wrap gap-3"></div>
        </div>
    </div>

     <?php get_template_part('navbar'); ?>

    <?php get_template_part('footer'); ?>
</body>
</html>
