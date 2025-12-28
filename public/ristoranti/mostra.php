<?php
include_once '../../includes/functions.php';

session_start();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
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

    <!-- Stili custom -->
    <link href="../../css/ristorante.css" rel="stylesheet">
    <link href="../../css/global.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    />

    <!-- Leaflet JS -->
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        crossorigin=""
    ></script>

    <script src="../../js/navbar.js"></script>
</head>

<body>

<div class="container-fluid">
    <div id="ristoranti-data"></div>
</div>

<?php
$btns[] = [
    'icon' => 'bi bi-menu-button-wide',
    'id'   => 'btn-menu',
    'label'=> 'Menù',
    'link' => 'menu.php?id=' . urlencode($id)
];
get_template_part('navbar', ['btns' => $btns]);
get_template_part('footer');
?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
window.onload = function () {
    stampaInfoRistorante();
};

async function stampaInfoRistorante() {

    const id = <?php echo $id; ?>;
    const url = "../../api/ristoranti.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                request_type: 'get-info',
                id: id
            })
        });

        if (!response.ok) {
            throw new Error(`Errore HTTP ${response.status}`);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error("Dati non disponibili");
        }

        const data = result.data;
        const container = document.getElementById("ristoranti-data");
        // console.log(data);
        container.innerHTML = `
            <div class="hero-section" style="background-image: url('${data.url_immagine ?? '../../img/hero-default.jpg'}');">
                <h1 class="restaurant-name">${data.ristorante.nome}</h1>
            </div>

            <div class="container section">

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h2 class="info-title">
                                <i class="bi bi-info-circle-fill"></i> Informazioni Generali
                            </h2>

                            <div class="info-item">
                                <i class="bi bi-geo-alt-fill info-icon"></i>
                                <strong>Indirizzo:</strong>
                                ${data.ristorante.indirizzo}
                                ${data.ristorante.numero_civico},
                                ${data.citta.nome ?? 'N/D'},
                                ${data.citta.cap ?? ''},
                                ${data.citta.provincia ?? ''},
                                ${data.citta.regione ?? ''}
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

                    <div class="col-md-6">
                        <div class="info-card">
                            <h2 class="info-title">
                                <i class="bi bi-card-text"></i> Dettagli Fiscali
                            </h2>

                            <div class="info-item">
                                <strong>Codice Fiscale:</strong> ${data.ristorante.codice_fiscale}
                            </div>

                            <div class="info-item">
                                <strong>Partita IVA:</strong> ${data.ristorante.partita_iva}
                            </div>

                            <div class="info-item">
                                <strong>Ragione Sociale:</strong> ${data.ristorante.ragione_sociale}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-card mt-4">
                    <h2 class="info-title">
                        <i class="bi bi-file-text-fill"></i> Descrizioni
                    </h2>
                    <p><strong>Breve:</strong> ${data.ristorante.descrizione_breve}</p>
                    <p><strong>Estesa:</strong> ${data.ristorante.descrizione_estesa}</p>
                </div>

                <div class="info-card mt-4">
                    <h2 class="info-title">
                        <i class="bi bi-star-fill"></i> Recensioni
                    </h2>
                    <button class="btn btn-outline-primary mb-3" onclick="openReviewModal()"><i class="bi bi-plus-circle"></i> Inserisci Recensione</button>
                    <div id="reviews-list"></div>
                </div>

                <div class="info-card mt-4">
                    <h2 class="info-title">
                        <i class="bi bi-map-fill"></i> Mappa
                    </h2>
                    <div id="map" style="height: 400px;"></div>
                </div>

            </div>
        `;

        caricaRecensioni(data.recensioni);

        if (data.citta.latitudine && data.citta.longitudine) {
            caricaMappa(data.citta.latitudine, data.citta.longitudine);
        } else {
            document.getElementById("map").innerHTML =
                "<p class='text-muted'>Coordinate non disponibili.</p>";
        }

    } catch (error) {
        document.getElementById("ristoranti-data").innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                ${error.message}
            </div>
        `;
    }
}

function caricaRecensioni(recensioni) {
    const container = document.getElementById("reviews-list");

    if (!recensioni || recensioni.length === 0) {
        container.innerHTML = "<p>Nessuna recensione disponibile.</p>";
        return;
    }

    container.innerHTML = recensioni.map(r => `
        <div class="review-card">
            <h5>${r.titolo}</h5>
            <p>${r.commento}</p>
            <strong>Voto:</strong> ${r.voto} ⭐
        </div>
    `).join('');
}

function caricaMappa(lat, lon) {

    const map = L.map('map').setView([lat, lon], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lon]).addTo(map)
        .bindPopup('Ristorante')
        .openPopup();
}
</script>

</body>
</html>
