<?php 
require_once __DIR__ . '/../includes/functions.php';
session_start(); 
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Ristoranti</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS personalizzato -->
    <link href="../css/index.css" rel="stylesheet">
    <link href="../css/global.css" rel="stylesheet">
    
    <!-- JS -->
    <script src="../js/index.js"></script>
    <script src="../js/navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="main-content d-flex flex-column min-vh-100">
        <!-- Hero section -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="hero-title">Software Ristoranti</h1>
                <p class="hero-description">Benvenuto nella piattaforma gestionale per i ristoranti. Qui potrai effettuare prenotazioni e ordinazioni direttamente senza dover chiamare. I gestori potranno accettare o rifiutare le tue prenotazioni.</p>
            </div>
        </div>

        <!-- Sezione ristoranti -->
        <div class="container mt-4 flex-grow-1">
            <div class="d-flex flex-column gap-3">
                <h3>Esplora i ristoranti</h3>
                <div id="show-ristoranti" class="d-flex flex-wrap gap-3 justify-content-center"></div>
            </div>
        </div>
    </div>

    <!-- Navbar e footer -->
    <?php get_template_part('navbar'); ?>
    <?php get_template_part('footer'); ?>
</body>
</html>
