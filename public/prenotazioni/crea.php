<?php
require_once '../../includes/functions.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] === 'chef')) {
    message("Accesso negato!");
}
?>
<html>

<head>
    <meta charset="utf-8">
    <title>Effetua una prenotazione</title>
    <!-- Icone -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Stile CSS -->
    <link href="../../css/index.css" rel="stylesheet">
    <link href="../../css/prenotazioni.css" rel="stylesheet">
    <link href="../../css/global.css" rel="stylesheet">

    <!-- Configurazione JavaScript -->
    <script src="../../js/global.js"></script>
    <script src="../../js/prenotazioni/crea.js"></script>
    <script src="../../js/navbar.js"></script>

</head>

<body>
    <?php get_template_part('navbar'); ?>
    <div class="hero-section mb-0 mx-3">
        <div class="container">
            <h1 class="hero-title">Prenota</h1>
            <p class="hero-description">Qui puoi effeture una nuova prenotazione, indicando le informazioni basi. Quali
                ristorante, data e ora, e numero di persone.</p>
        </div>
    </div>
    <div class="bg-light mb-0 py-5">
        <div class="container mt-0">
            <form id="form-prenotazione" class="prenotazione-form" method="POST">


                <div class="prenotazione-group">
                    <label>Seleziona Ristorante:</label>
                    <select name="ristorante" id="ristorante" required></select>
                </div>

                <div class="prenotazione-group">
                    <label for="data_prenotazione">Data prenotazione:</label>
                    <input type="date" name="data_prenotazione" id="data_prenotazione" required>
                </div>

                <div class="prenotazione-group">
                    <label for="ora_prenotazione">Ora prenotazione:</label>
                    <input type="time" name="ora_prenotazione" id="ora_prenotazione" required>
                </div>

                <div class="prenotazione-group">
                    <label for="persone">Numero di persone:</label>
                    <input type="number" name="persone" id="persone" required min="1">
                </div>

                <div>
                    <button type="submit" class="prenotazione-submit">Prenota</button>
                </div>
            </form>
        </div>
    </div>
    <?php get_template_part('footer'); ?>
</body>
</html>