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
    <title>Menù Ristorante</title>
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
            background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
            padding: 50px 0;
            text-align: center;
        }
        .hero-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .restaurant-name {
            font-size: 2.5rem;
            font-weight: bold;
            margin-top: 20px;
            color: #007bff; /* Blu */
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
            color: #007bff; /* Blu */
            margin-bottom: 15px;
        }
        .menu-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .menu-item:last-child {
            border-bottom: none;
        }
        .menu-item-name {
            font-weight: bold;
            color: #007bff; /* Blu */
        }
        .menu-item-price {
            font-weight: bold;
            color: #333;
        }
        .menu-item-description {
            color: #666;
            margin-top: 5px;
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
        @media (max-width: 768px) {
            .hero-image {
                height: 200px;
            }
            .restaurant-name {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="" id="menu-data"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
window.onload = function () {
    caricaMenu();
}

async function caricaMenu() {
    const id = <?php echo $_GET['id']; ?>;
    const url = "../api/menu.php?id=" + id;

    try {
        const response = await fetch(url, {
            method: "GET",
            headers: { "Content-Type": "application/x-www-form-urlencoded" }
        });

        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

       const result = await response.json();
console.log(result); // Debug

let menuHtml = `
    <div class="hero-section">
        <div class="container">
            <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2a/90/de/18/le-sale-del-ristorante.jpg?w=900&h=500&s=1" alt="Immagine del ristorante" class="hero-image">
            <h1 class="restaurant-name">${result.nomeRistorante}</h1>
            <div class="mt-3">
                <button class="btn btn-primary btn-modern me-2" onclick="window.location.href='ristorante.php?id='+${id}"><i class="bi bi-arrow-left"></i> Torna ai Dettagli</button>
                <button class="btn btn-success btn-modern"><i class="bi bi-calendar-check"></i> Prenota</button>
            </div>
        </div>
    </div>
    <div class="container section">
`;

result.categorie.forEach(categoria => {
    menuHtml += `
        <div class="row">
            <div class="col-12">
                <div class="info-card">
                    <h2 class="info-title"><i class="bi bi-list-ul"></i> ${categoria.nome}</h2>
    `;
    if(categoria.piatti.length > 0){
        categoria.piatti.forEach(piatto => {
            menuHtml += `
                <div class="menu-item">
                    <div class="d-flex justify-content-between">
                        <span class="menu-item-name">${piatto.nome}</span>
                        <span class="menu-item-price">€${piatto.prezzo}</span>
                    </div>
                    <div class="menu-item-description">${piatto.descrizione || ''}</div>
                </div>
            `;
        });
    } else {
        menuHtml += `<p>Nessun piatto disponibile in questa categoria.</p>`;
    }
    menuHtml += `</div></div></div>`;
});

menuHtml += `</div>`;
document.getElementById("menu-data").innerHTML = menuHtml;


    } catch (error) {
        console.error(error.message);
        const container = document.getElementById("menu-data");
        container.innerHTML = `
            <div class="container section">
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> Errore nel caricamento del menù: ${error.message}
                </div>
            </div>
        `;
    }
}
</script>

</body>
</html>
