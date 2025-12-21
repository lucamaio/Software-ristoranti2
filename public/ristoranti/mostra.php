<?php
include_once '../../includes/functions.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    message("Devi accedere per poter visualizzare questa pagina");
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Ristorante</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/ristorante.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="" id="ristoranti-data"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.onload = function () {
            stampaInfoRistorante();
        }

        async function stampaInfoRistorante() {
            const id = <?php echo $_GET['id']; ?>;
            const url = "../../api/ristoranti.php";
            console.log(id);

            try {
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        request_type: 'get-info',
                        id: id,
                    })
                });

                if (!response.ok) {
                    throw new Error(`Response status: ${response.status}`);
                }

                const result = await response.json();
                console.log(result);
                var container = document.getElementById("ristoranti-data");
                var data = result.data;

                container.innerHTML = `
                    <div class="hero-section">
                        <a href="../index.php" class="btn btn-dark btn-modern back-button"> <!-- Cambiato a btn-dark per colore scuro -->
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h1 class="restaurant-name">${data.ristorante.nome}</h1>
                    </div>
                    <div class="container section">
                        <!-- Pulsanti Menù e Prenota spostati qui per una posizione più ottimale -->
                        <div class="d-flex justify-content-center mb-4">
                            <button class="btn btn-primary btn-modern me-2" onclick="window.location.href='menu.php?id=' + ${id};">
                                <i class="bi bi-menu-button-wide"></i> Menù
                            </button>
                            <button class="btn btn-success btn-modern"><i class="bi bi-calendar-check"></i> Prenota</button>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="info-card">
                                    <h2 class="info-title"><i class="bi bi-info-circle-fill"></i> Informazioni Generali</h2>
                                    <div class="info-item">
                                        <i class="bi bi-geo-alt-fill info-icon"></i>
                                        <strong>Indirizzo:</strong> ${data.ristorante.indirizzo} ${result.numero_civico}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-telephone-fill info-icon"></i>
                                        <strong>Telefono:</strong> ${data.ristorante.telefono}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-envelope-fill info-icon"></i>
                                        <strong>Email:</strong> ${data.ristorante.email}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-people-fill info-icon"></i>
                                        <strong>Capienza:</strong> ${data.ristorante.capienza}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="info-card">
                                    <h2 class="info-title"><i class="bi bi-card-text"></i> Dettagli Fiscali</h2>
                                    <div class="info-item">
                                        <i class="bi bi-card-text info-icon"></i>
                                        <strong>Codice Fiscale:</strong> ${data.ristorante.codice_fiscale}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-receipt info-icon"></i>
                                        <strong>Partita IVA:</strong> ${data.ristorante.partita_iva}
                                    </div>
                                     <div class="info-item">
                                        <i class="bi bi-building info-icon"></i> <!-- Cambiata icona da bi-receipt a bi-building per la ragione sociale -->
                                        <strong>Ragione Sociale:</strong> ${data.ristorante.ragione_sociale}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="info-card">
                                    <h2 class="info-title"><i class="bi bi-file-text-fill"></i> Descrizioni</h2>
                                    <p class="description"><strong>Breve:</strong> ${data.ristorante.descrizione_breve}</p>
                                    <p class="description"><strong>Estesa:</strong> ${data.ristorante.descrizione_estesa}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row reviews-section">
                            <div class="col-12">
                                <div class="info-card">
                                    <h2 class="info-title"><i class="bi bi-star-fill"></i> Recensioni</h2>
                                    <button class="btn btn-outline-primary btn-modern mb-3" onclick="openReviewModal()"><i class="bi bi-plus-circle"></i> Inserisci Recensione</button>
                                    <div id="reviews-list">
                                        <!-- Qui puoi aggiungere recensioni dinamiche se hai un'API per loro -->
                                        <div class="review-card">
                                            <strong>Utente1:</strong> Ottimo ristorante! Servizio eccellente.
                                        </div>
                                        <div class="review-card">
                                            <strong>Utente2:</strong> Cibo delizioso, tornerò sicuramente.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

            } catch (error) {
                console.error(error.message);
                var container = document.getElementById("ristoranti-data");
                container.innerHTML = `
                    <div class="container section">
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i> Errore nel caricamento dei dati: ${error.message}
                        </div>
                    </div>
                `;
            }
        }

        function openReviewModal() {
            // Placeholder per aprire un modal di inserimento recensione
            alert("Funzionalità di inserimento recensione da implementare (es. aprire un modal con form).");
        }


    </script>
</body>

</html>