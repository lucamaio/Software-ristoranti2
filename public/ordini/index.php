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
    <!-- Custom CSS -->
    <link href="../../css/global.css" rel="stylesheet">
    <link href="../../css/ordini.css" rel="stylesheet">
    <!-- Custom JS -->
    <script src="../../js/navbar.js"></script>
    <script src="../../js/global.js"></script>
    <script src="../../js/orders.js"></script>
</head>

<body>
    <?php get_template_part('navbar'); ?>
    <main class="main-content">
         <!-- Hero section -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="hero-title">Ordini</h1>
                <p class="hero-description">In questa pagina puoi consultare e modificare i tuoi ordini. Inoltre, puoi procedere ad effettuare i pagamenti di essi.</p>
            </div>
        </div>

            <div class="container py-4">
                <!-- Loader -->
                <div id="orders-loader" class="text-center my-5">
                    <div class="spinner-border text-primary"></div>
                </div>

                <!-- ORDINI -->
                <div class="container my-4">
                    <div class="row g-3" id="orders-container"></div>
                </div>

                <!-- Messaggio -->
                <div id="message" class="mb-4"></div>

            </div>
    </main> 
    <?php get_template_part('footer'); ?>
</body>

</html>