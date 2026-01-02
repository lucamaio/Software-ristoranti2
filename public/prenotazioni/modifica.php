<?php
require_once '../../includes/functions.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['ruolo'] === 'cuoco')) {
    message("Accesso negato!");
}

$id_prenotazione = $_GET['id'] ?? null;
if (!$id_prenotazione) {
    message("ID prenotazione mancante!");
}

?>
<html>

<head>
    <meta charset="utf-8">
    <title>Modifica prenotazione n°<?= $id_prenotazione; ?></title>
    <!-- Icone -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Stile CSS -->
    <link href="../../../css/global.css" rel="stylesheet">
    <link href="../../../css/prenotazioni.css" rel="stylesheet">
    <link href="../../../css/index.css" rel="stylesheet">

    <!-- Configurazione JavaScript -->
    <script src="../../../js/global.js"></script>
    <script src="../../../js/prenotazioni/modifica.js"></script>
    <script src="../../../js/navbar.js"></script>

</head>

<body>
    <div class="main-content">
        <!-- Hero section -->
        <div class="hero-section">
            <div class="container">
                <h1 class="hero-title">Modifica prenotazione n°<?= $id_prenotazione; ?></h1>
                <p class="hero-description">Qui puoi modificare la tua prenotazione. Cambiando la data e l'ora ed infine
                    il numero di persone.</p>
            </div>
        </div>

        <div class="bg-light mb-0 py-5">
            <div class="container mt-4">
                <div class="d-flex flex-column gap-3">
                    <div id="message" class="mb-4"></div>
                    <form id="form-edit-prenotazione" class="prenotazione-form" method="POST">
                        <input type="hidden" name="id_prenotazione" id="id_prenotazione" value="<?= $id_prenotazione; ?>">
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
                            <button type="submit" class="prenotazione-submit">Aggiorna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php get_template_part('navbar'); ?>
    <?php get_template_part('footer'); ?>
</body>

</html>