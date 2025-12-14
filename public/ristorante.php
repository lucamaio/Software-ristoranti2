<?php 
    include_once '../includes/functions.php';

    session_start(); 
    if(!isset($_SESSION['user_id'])){
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
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
        }
        .hero-section {
            position: relative;
            height: 250px; /* Ridotta da 300px per renderla meno grande */
            background: url('../ristorante.png') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .restaurant-name {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5); /* Per migliorare la leggibilità */
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }
        .section {
            padding: 40px 0;
        }
        .info-card {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .info-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #007bff; /* Cambiato in blu */
            margin-bottom: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-icon {
            margin-right: 10px;
            color: #007bff; /* Cambiato in blu */
        }
        .description {
            line-height: 1.6;
        }
        .btn-modern {
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .reviews-section {
            margin-top: 40px;
        }
        .review-card {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        @media (max-width: 768px) {
            .hero-section {
                height: 150px; /* Ridotta da 200px per mobile */
            }
            .restaurant-name {
                font-size: 2rem;
            }
        }
    </style>
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

        async function stampaInfoRistorante(){
            const id = <?php echo $_GET['id']; ?>;
            const url = "../api/ristoranti.php?id=" + id;

            try {
                const response = await fetch(url, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    }
                });

                if(!response.ok){
                    throw new Error(`Response status: ${response.status}`);
                }

                const result = await response.json();
                console.log(result);
                var container = document.getElementById("ristoranti-data");

                container.innerHTML = `
                    <div class="hero-section">
                        <a href="index.php" class="btn btn-dark btn-modern back-button"> <!-- Cambiato a btn-dark per colore scuro -->
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h1 class="restaurant-name">${result.nome}</h1>
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
                                        <strong>Indirizzo:</strong> ${result.indirizzo} ${result.numero_civico}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-telephone-fill info-icon"></i>
                                        <strong>Telefono:</strong> ${result.telefono}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-envelope-fill info-icon"></i>
                                        <strong>Email:</strong> ${result.email}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-people-fill info-icon"></i>
                                        <strong>Capienza:</strong> ${result.capienza}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="info-card">
                                    <h2 class="info-title"><i class="bi bi-card-text"></i> Dettagli Fiscali</h2>
                                    <div class="info-item">
                                        <i class="bi bi-card-text info-icon"></i>
                                        <strong>Codice Fiscale:</strong> ${result.codice_fiscale}
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-receipt info-icon"></i>
                                        <strong>Partita IVA:</strong> ${result.partita_iva}
                                    </div>
                                     <div class="info-item">
                                        <i class="bi bi-building info-icon"></i> <!-- Cambiata icona da bi-receipt a bi-building per la ragione sociale -->
                                        <strong>Ragione Sociale:</strong> ${result.ragione_sociale}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="info-card">
                                    <h2 class="info-title"><i class="bi bi-file-text-fill"></i> Descrizioni</h2>
                                    <p class="description"><strong>Breve:</strong> ${result.descrizione_breve}</p>
                                    <p class="description"><strong>Estesa:</strong> ${result.descrizione_estesa}</p>
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
