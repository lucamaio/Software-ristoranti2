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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menù Ristorante</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../css/menu.css" rel ="stylesheet" />
</head>

<body>

<div class="container-fluid">
    <div id="menu-data"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.onload = () => caricaMenu();

    async function caricaMenu() {
        const id = <?php echo json_encode($_GET['id']); ?>;
        const url = "../api/menu.php?id=" + encodeURIComponent(id);

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error("Errore HTTP " + response.status);

            const result = await response.json();

            let html = `
                <div class="hero-section">
                    <a href="ristorante.php?id=${id}" class="btn btn-dark back-button">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h1 class="restaurant-name">${result.nomeRistorante}</h1>
                </div>

                <div class="container section">
                    <div class="text-center mb-4">
                        <button class="btn btn-success">
                            <i class="bi bi-calendar-check"></i> Prenota
                        </button>
                    </div>
            `;

            result.categorie.forEach(categoria => {
                html += `
                    <div class="category-box">
                        <div class="category-header">
                            <h2 class="category-title">
                                <i class="bi bi-bookmark-fill"></i>
                                ${categoria.nome}
                            </h2>
                        </div>

                        <div class="category-content">
                `;

                if (categoria.piatti.length > 0) {
                    categoria.piatti.forEach(piatto => {
                        html += `
                            <div class="menu-item">
                                <div class="d-flex justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="menu-item-name">${piatto.nome}</div>
                                        <div class="menu-item-description">${piatto.descrizione ?? ''}</div>
                                    </div>

                                    <div class="text-end">
                                        <div class="menu-item-price">€${piatto.prezzo}</div>
                                        <div class="btn-add-to-cart-wrapper">
                                            <button class="btn-add-to-cart" onclick="addToCart('${piatto.ID_piatto}')">
                                                <i class="bi bi-cart-plus"></i> Aggiungi
                                            </button>
                                            <button class="btn-remove-from-cart" onclick="removeFromCart('${piatto.id}')">
                                                <i class="bi bi-trash"></i> Rimuovi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `<p class="text-muted">Nessun piatto disponibile.</p>`;
                }

                html += `
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
            document.getElementById("menu-data").innerHTML = html;

        } catch (error) {
            document.getElementById("menu-data").innerHTML = `
                <div class="container section">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Errore nel caricamento del menù: ${error.message}
                    </div>
                </div>
            `;
        }
    }

    async function addToCart(id) {
        
        const url = "../api/cart.php?id=" + encodeURIComponent(id);
        try{
            const response = await fetch(url, {
                method: "POST",
                   headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    id: id,
                    azione: 'add'
                })
            });

            if(!response.ok){
                throw new Error("Errore HTTP" + response.status);
            }
            alert("Piatto " + id + " aggiunto al carrello");            
        }catch(error){
            alert("Errore nel inserimento del piatto "+ id +" al carrello");
        }
    }

    function removeFromCart(id) {
        alert("Piatto " + id + " rimosso dal carrello");
    }
</script>

</body>
</html>
