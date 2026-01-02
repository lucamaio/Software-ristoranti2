const cartQuantities = {};
let ID_cart = null;

document.addEventListener('DOMContentLoaded', function() {
    caricaMenu();
});

// Funzione che carica il menù del ristorante
async function caricaMenu() {
    // 1. Mi ricavo i carrelli del utente se l'utente è client
    const ruolo = window.APP_CONFIG.ruolo;
    if(ruolo && ruolo === 'cliente'){
       await  caricaQuantitàCarrello();
    }
    const id = window.APP_CONFIG.ID_ristorante;
    const url = "../../api/menu.php?id=" + encodeURIComponent(id);

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error("Errore HTTP " + response.status);

        const result = await response.json();
        let html = `
            <div class="hero-section">
                <h1 class="restaurant-name">${result.nomeRistorante}</h1>
            </div>
            <div class="container section">
                <div id="message" class="mb-4"></div>
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

            categoria.piatti.forEach(piatto => {
                html += `
                    <div class="menu-item">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1">
                                <div class="menu-item-name">${piatto.nome}</div>
                                <div class="menu-item-description">${piatto.descrizione ?? ""}</div>
                            </div>

                            <div class="text-end" id="pulsanti-azione-${piatto.ID_piatto}">
                                <div class="menu-item-price">€${piatto.prezzo}</div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `</div></div>`;
        });

        html += `</div>`;
        document.getElementById("menu-data").innerHTML = html;

        inizializzaPulsanti(result);

    } catch (error) {
        document.getElementById("menu-data").innerHTML = `
            <div class="alert alert-danger">
                Errore nel caricamento del menù
            </div>
        `;
    }
}

// Funzione che carica quantità piatti carrello

async function caricaQuantitàCarrello() {
    const url = "../../api/cart.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({ azione: "get-info" })
        });

        const result = await response.json();
        // console.log(result);

        if (result.success) {
            // resetta cartQuantities
            for (let key in cartQuantities) delete cartQuantities[key];

            // assume che ci sia un solo carrello per il cliente
            if (result.data.length > 0) {
                const ID_carrello = result.data[0].cart.ID_carrello ?? 'N/D';
                const dettagli = result.data[0].dettagli;

                dettagli.forEach(item => {
                    cartQuantities[item.ID_piatto] = item.quantita;
                });
                ID_cart = ID_carrello;
            }
            // console.log(ID_cart);
            // console.log('Cart quantities:', cartQuantities);
        }

    } catch (error) {
        document.getElementById("menu-data").innerHTML = `
            <div class="alert alert-danger">
                Errore nel caricamento del menù ${error.message}
            </div>
        `;
    }
}

// Funzione che inizializza i pulsanti 

function inizializzaPulsanti(result) {
    result.categorie.forEach(categoria => {
        categoria.piatti.forEach(piatto => {

            const qty = cartQuantities[piatto.ID_piatto] ?? 0;
            const container = document.getElementById(`pulsanti-azione-${piatto.ID_piatto}`);

            if (container) {
                container.innerHTML = `
                    <div class="menu-item-price">€${piatto.prezzo}</div>
                    ${creaPulsantiAzione(piatto.ID_piatto, qty)}
                `;
            }
        });
    });
}

// Funzione che genera i pulsanti azione menù 

function creaPulsantiAzione(ID_piatto, quantità) {
    let html = `
        <div class="cart-controls">
            <button class="btn-cart btn-add"
                    onclick="addToCart('${ID_piatto}')">
                <i class="bi bi-cart-plus"></i>
            </button>
    `;

    if (quantità == 1) {
        html += `
            <span class="qty-value" id="qty-${ID_piatto}">
                ${quantità}
            </span>

            <button class="btn-cart btn-qty"
                    onclick="incrementaQuantità('${ID_piatto}')">
                <i class="bi bi-plus"></i>
            </button>

            <button class="btn-cart btn-remove"
                    onclick="removeFromCart('${ID_piatto}')">
                <i class="bi bi-trash"></i>
            </button>
        `;
    } else if(quantità > 1) {
        html += `
            <button class="btn-cart btn-qty"
                    onclick="diminuisciQuantità('${ID_piatto}')">
                <i class="bi bi-dash"></i>
            </button>

            <span class="qty-value" id="qty-${ID_piatto}">
                ${quantità}
            </span>

            <button class="btn-cart btn-qty"
                    onclick="incrementaQuantità('${ID_piatto}')">
                <i class="bi bi-plus"></i>
            </button>

            <button class="btn-cart btn-remove"
                    onclick="removeFromCart('${ID_piatto}')">
                <i class="bi bi-trash"></i>
            </button>
        `;
    }

    html += `</div>`; // chiudi cart-controls

    return html;
}


// Funzione che aggiorna il badge della quantità

function updateBadge(id) {
    const badge = document.getElementById(`badge-${id}`);
    const qtySpan = document.getElementById(`qty-${id}`);
    const qty = cartQuantities[id] ?? 0;

    if (badge) {
        badge.style.display = qty > 0 ? 'flex' : 'none';
        badge.textContent = qty;
    }

    if (qtySpan) {
        qtySpan.textContent = qty;
    }
}



// Funzioni che interogano l'API

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

        // Aggiorna quantità locale
        // cartQuantities[ID_piatto] = (cartQuantities[ID_piatto] ?? 0) + 1;

        // Aggiorna badge e controlli
        // updateBadge(ID_piatto);

        const result = await response.json();

        if (result.success) {
            // cartQuantities[ID_piatto] = (cartQuantities[ID_piatto] ?? 0) + 1;
            // updateBadge(ID_piatto);
            mostraMessaggio(result.message);
            await sleep(250);
            caricaMenu();
        } else {
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        alert("Errore nell'inserimento del piatto nel carrello",error.message);
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


        const result = await response.json();

        if (result.success) {
            // cartQuantities[ID_piatto] = (cartQuantities[ID_piatto] ?? 0) + 1;
            // updateBadge(ID_piatto);
            mostraMessaggio(result.message);
            await sleep(250);
            caricaMenu();
        } else {
            mostraMessaggio(result.error, 'error');
        }

        // cartQuantities[id] = Math.max((cartQuantities[id] ?? 0) - 1, 0);
        // updateBadge(id);

    } catch (error) {
        alert("Errore nella rimozione del piatto dal carrello", error.message);
    }
}

async function incrementaQuantità(ID_piatto){
    if(ID_cart == null || ID_cart <= 0){
        console.log('ID carrello non valido! ID_cart: '+ID_cart);
        return;
    }
    
    const url = "../../api/cart.php";
    
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "incrementa",
                ID_carrello: ID_cart,
                ID_piatto: ID_piatto
            })
        });

        const result = await response.json();

        if (result.success) {
            // cartQuantities[ID_piatto] = (cartQuantities[ID_piatto] ?? 0) + 1;
            // updateBadge(ID_piatto);
            mostraMessaggio(result.message);
            await sleep(250);
            caricaMenu();
        } else {
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        console.error(error.message);
    }
}

async function diminuisciQuantità(ID_piatto) {
    if(ID_cart == null || ID_cart <= 0){
        console.log('ID carrello non valido! ID_cart: '+ID_cart);
        return;
    }
    
    const url = "../../api/cart.php";
    
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "diminuisci",
                ID_carrello: ID_cart,
                ID_piatto: ID_piatto
            })
        });

        const result = await response.json();

        if (result.success) {
            // cartQuantities[ID_piatto] = Math.max((cartQuantities[ID_piatto] ?? 1) - 1, 0);
            // updateBadge(ID_piatto);
            mostraMessaggio(result.message);
            await sleep(250);
            caricaMenu();
        } else {
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        console.error(error.message);
    }
}

// Funzione che stampa il messagio

function mostraMessaggio(testo, tipo = 'success') {
    const messageDiv = document.getElementById('message');
    messageDiv.style.display = 'block';
    messageDiv.textContent = testo;

    // Rimuovo eventuali classi precedenti
    messageDiv.className = '';

    // Aggiungo tipo e animazione
    messageDiv.classList.add(tipo, 'show');

    // Nascondo automaticamente dopo 3 secondi
    setTimeout(() => {
        messageDiv.classList.add('hide');
        // Dopo la transizione, rimuovo tutto
        setTimeout(() => {
            messageDiv.className = '';
            messageDiv.style.display = 'none';
        }, 400);
    }, 3000);
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}