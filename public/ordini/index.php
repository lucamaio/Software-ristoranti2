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
        <link href="../../css/global.css"rel="stylesheet">
        <link href="../../css/ordini.css" rel="stylesheet">
        <!-- Custom JS -->
        <script src="../../js/navbar.js" ></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/orders.js"></script>
    </head>
    <body>
        <?php get_template_part('navbar'); ?>
        <main class="main-content">
            <section class="hero-section">
                <div class="container">
                    <h1 class="hero-title">Ordini</h1>
                    <p class="hero-description">
                        In questa pagina puoi consultare e modificare i tuoi ordini.
                        Inoltre, puoi procedere ad effettuare i pagamenti.
                    </p>
                </div>
            </section>

            <section class="d-flex flex-column gap-3 bg-light">
                <div class="container py-3">
                    <div id="message" class="mb-4"></div>
                    <div id="orders-container" class="d-flex flex-wrap gap-3 justify-content-center"></div>
                </div>
            </section>

        </main>
        <?php get_template_part('footer'); ?>
    </body>
</html>
