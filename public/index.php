<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Software Ristoranti</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
        }
        .hero-section {
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
            padding: 50px 0;
            text-align: center;
        }
        .hero-title {
            font-size: 3rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }
        .hero-description {
            font-size: 1.2rem;
            line-height: 1.6;
            color: #555;
            max-width: 800px;
            margin: 0 auto;
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
        .restaurant-card {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            width: 18rem;
        }
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .restaurant-card a {
            text-decoration: none;
            color: inherit;
        }
        .restaurant-card .card-title {
            color: #007bff;
            font-weight: bold;
        }
        .restaurant-card .card-text {
            line-height: 1.5;
        }
        .restaurant-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .hero-description {
                font-size: 1rem;
            }
            .restaurant-card {
                width: 100%;
            }
        }
    </style>

    <script>
        window.onload = function() {
            ristorantiGet(); // richiamo automatico al caricamento
            document.getElementById("get").addEventListener("click", ristorantiGet);
        };

        function ristorantiGet() {
            var oReq = new XMLHttpRequest();
            oReq.onload = function() {
                var dati = JSON.parse(oReq.responseText);
                var container = document.getElementById("ajaxres");
                container.innerHTML = ""; // pulisco contenuto precedente

                dati.forEach(function(r) {
                    var div = document.createElement("div");
                    div.className = "restaurant-card"; // stile personalizzato
                    
                    div.innerHTML = `
                    <a href="ristorante.php?id=${r.ID_ristorante}" style="text-decoration: none; color: inherit;">
                        <img src="../ristorante.png" alt="Immagine del ristorante" class="restaurant-image">
                        <div class="card-body">
                            <h5 class="card-title">${r.nome}</h5>
                            <p class="card-text">
                                <strong>ID:</strong> ${r.ID_ristorante}<br>
                                <strong>Indirizzo:</strong> ${r.indirizzo}<br>
                                <strong>Telefono:</strong> ${r.telefono}<br>
                                <strong>Email:</strong> ${r.email}
                            </p>
                        </div>
                    </a>
                    `;
                    container.appendChild(div);
                });
            };

            oReq.onerror = function() {
                document.getElementById("ajaxres").innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> Errore nella richiesta dei ristoranti.
                    </div>
                `;
            };

            oReq.open("GET", "../api/ristoranti.php", true); // Cambiato URL relativo
            oReq.send();
        }
    </script>

</head>
<body class="p-4">
    <?php session_start(); ?>
    
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">Software Ristoranti</h1>
            <p class="hero-description">Benvenuto nella piattaforma gestionale per i ristoranti. Qui potrai effettuare prenotazioni e ordinazioni direttamente senza dover chiamare. I gestori potranno accettare o rifiutare le tue prenotazioni.</p>
        </div>
    </div>

    <div class="container mt-4">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button id="get" class="btn btn-primary btn-modern">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna Ristoranti
            </button>
            <?php if(isset($_SESSION['user_id'])){?>
                <button class="btn btn-secondary btn-modern" onclick="window.location.href='prenotazioni.php'">
                    <i class="bi bi-calendar-check"></i> Prenotazioni
                </button>
                <button class="btn btn-secondary btn-modern" onclick="window.location.href='ordini.php'">
                    <i class="bi bi-receipt"></i> Ordini
                </button>
                <button class="btn btn-danger btn-modern" onclick="window.location.href='logout.php'">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            <?php }else { ?>
                <button class="btn btn-danger btn-modern" onclick="window.location.href='login.php'">
                    <i class="bi bi-box-arrow-in-right"></i> Accedi
                </button>
            <?php }?>
        </div>
        
        <div id="ajaxres" class="d-flex flex-wrap gap-3"></div> <!-- contenitore per i div -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
