 const cartQuantities = {};

    function updateBadge(id) {
        const badge = document.getElementById(`badge-${id}`);
        const qty = cartQuantities[id] ?? 0;

        if (!badge) return;

        if (qty > 0) {
            badge.textContent = qty;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    window.onload = () => caricaMenu();

    async function caricaMenu() {
        const id = window.APP_CONFIG.ID_ristorante;
        const url = "../../api/menu.php?id=" + encodeURIComponent(id);

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error("Errore HTTP " + response.status);

            const result = await response.json();

            let html = `
                <div class="hero-section">
                    <a href="mostra.php?id=${id}" class="btn btn-dark back-button">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h1 class="restaurant-name">${result.nomeRistorante}</h1>
                </div>

                <div class="container section">
                    <div class="text-center mb-4">
                        <button class="btn btn-success">
                            <i class="bi bi-calendar-check"></i> Prenota
                        </button>
                        <button class="btn btn-secondary" onclik ="window.location.href="../carrello/index.php">
                            <i class="bi bi-calendar-check"></i> Carrelli
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
                                            <div class="cart-badge" id="badge-${piatto.ID_piatto}"></div>
                                            <button class="btn-add-to-cart" onclick="addToCart('${piatto.ID_piatto}')">
                                                <i class="bi bi-cart-plus"></i> Aggiungi
                                            </button>
                                            <button class="btn-remove-from-cart" onclick="removeFromCart('${piatto.ID_piatto}')">
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
    const url = "../../api/cart.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                ID_piatto: id,
                azione: 'aggiungi'
            })
        });

        if (!response.ok) {
            throw new Error("Errore HTTP " + response.status);
        }

        cartQuantities[id] = (cartQuantities[id] ?? 0) + 1;
        updateBadge(id);

    } catch (error) {
        alert("Errore nell'inserimento del piatto nel carrello");
    }
}


    async function removeFromCart(id) {
    const url = "../../api/cart.php";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                azione: 'rimuovi',
                ID_piatto: id,
            })
        });

        if (!response.ok) {
            throw new Error("Errore HTTP " + response.status);
        }

        cartQuantities[id] = Math.max((cartQuantities[id] ?? 0) - 1, 0);
        updateBadge(id);

    } catch (error) {
        alert("Errore nella rimozione del piatto dal carrello");
    }
}