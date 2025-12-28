document.addEventListener('DOMContentLoaded', function () {
    getCart();
});

async function getCart() {
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
            // console.log("Sono dentro if");
            creaTabella(result.data);
        }

    } catch (error) {
        console.log("Errore: ", error.message);
    }
}

function creaTabella(risultato) {
    // console.log('Sono dentro la funzione! ', risultato);
    const container = document.getElementById("table-carts");
    container.innerHTML = "";

    risultato.forEach(r => {
        const card = document.createElement("div");
        card.className = "card shadow-sm";

        // Formattiamo la data in dd/mm/yyyy
        let dataCreazione = r.cart.data_creazione ? new Date(r.cart.data_creazione).toLocaleDateString() : 'N/D';

        card.innerHTML = `
            <div class="card-header bg-dark fw-semibold d-flex justify-content-between">
                <span>Ristorante ${r.nome_ristorante ?? 'N/D'}</span>
                <span class="text-muted" style="color: #fff !important">Creato il: ${dataCreazione}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Piatto</th>
                                <th>Quantità</th>
                                <th>Prezzo singolo</th>
                                <th>Prezzo totale</th>
                                <th class="text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="prenotazioni-body-${r.cart.ID_carrello}"></tbody>
                    </table>
                    <p id="totale-${r.cart.ID_carrello}" class="mt-2 mx-5"></p>
                    <div class="d-flex justify-content-center mt-3 mb-2">
                        <button class="btn btn-success btn-sm me-2" id="genera-ordine-${r.cart.ID_carrello}">
                            <i class="fa-solid fa-cart-plus me-2"></i>Crea ordine
                        </button>
                        <button class="btn btn-danger btn-sm me-2" id="elimina-cart-${r.cart.ID_carrello}">
                            <i class="fa-solid fa-cart-plus me-2"></i>Elimina carrello
                        </button>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(card);

        // Inserisco il t-body per questo carrello
        const tbody = document.getElementById(`prenotazioni-body-${r.cart.ID_carrello}`);
        let totale = 0;

        r.dettagli.forEach(d => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${d.nome_piatto}</td>
                <td>${d.quantita}</td>
                <td>${d.prezzo ?? 'N/D'} €</td>
                <td>${(d.prezzo * d.quantita) ?? 'N/D'} €</td>
            `;

            const tdAzioni = document.createElement('td');
            tdAzioni.classList = 'text-center';
            tdAzioni.appendChild(creaPulsantiAzioni(d.ID_piatto, r.cart.ID_carrello, d.quantita));
            tr.appendChild(tdAzioni);
            tbody.appendChild(tr);
            totale += (d.prezzo * d.quantita) ?? 0;
        });

        const pTotale = document.getElementById(`totale-${r.cart.ID_carrello}`);
        pTotale.innerHTML = `<strong>Totale carrello</strong>: ${totale.toFixed(2)} €`;

        tbody.addEventListener("click", function (e) {
            if (e.target.closest('.btn-remove')) {
                const btn = e.target.closest(".btn-remove");
                const idPiatto = btn.dataset.piatto;
                const idCarrello = btn.dataset.carrello;

                rimuoviPiatto(idPiatto, idCarrello);
            }

            if (e.target.closest('.btn-diminuisci')) {
                const btn = e.target.closest(".btn-diminuisci");
                const idPiatto = btn.dataset.piatto;
                const idCarrello = btn.dataset.carrello;
                // console.log(idPiatto + "\n" + idCarrello);
                // alert('quantita diminuita rimmosso!');
                diminuisciQuantita(idPiatto, idCarrello);
            }

            if (e.target.closest('.btn-add')) {
                const btn = e.target.closest(".btn-add");
                const idPiatto = btn.dataset.piatto;
                const idCarrello = btn.dataset.carrello;
                // console.log(idPiatto + "\n" + idCarrello);
                // alert('quantita diminuita rimmosso!');
                incrementaQuantita(idPiatto, idCarrello);
            }
        });

        const btnOrder = document.getElementById('genera-ordine-'+r.cart.ID_carrello); 
        if(btnOrder){
            btnOrder.addEventListener('click', function() {
                generaOrdine(r.cart.ID_carrello);
            });
        }

        const btnEliminaCart = document.getElementById('elimina-cart-'+r.cart.ID_carrello);
        if(btnEliminaCart){
            btnEliminaCart.addEventListener('click', function(){
                eliminaCarrello(r.cart.ID_carrello);
            });
        }
    });
}

function creaPulsantiAzioni(idPiatto, idCarrello, quantita) {
    const div = document.createElement('div');
    // console.log('Quantita: ', quantita);
    if (quantita === 1) {

        div.innerHTML = `
        <button class="btn btn-sm btn-outline-success me-2 mb-2 btn-add" data-piatto="${idPiatto}" data-carrello="${idCarrello}" title="Incrementa quantita">
            <i class="fa-solid fa-plus"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger me-2 mb-2 btn-remove" data-piatto="${idPiatto}" data-carrello="${idCarrello}" title="Elimina piatto">
            <i class="fa-solid fa-trash"></i>
        </button> `;
    } else {
        div.innerHTML = `
        <button class="btn btn-sm btn-outline-success me-2 mb-2 btn-add" data-piatto="${idPiatto}" data-carrello="${idCarrello}" title="Incrementa quantita">
            <i class="fa-solid fa-plus"></i>
        </button>
        <button class="btn btn-sm btn-outline-secondary me-2 mb-2 btn-diminuisci" data-piatto="${idPiatto}" data-carrello="${idCarrello}" title="Diminuisci quantita">
            <i class="fa-solid fa-minus"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger me-2 mb-2 btn-remove" data-piatto="${idPiatto}" data-carrello="${idCarrello}" title="Elimina piatto">
            <i class="fa-solid fa-trash"></i>
        </button>        
    `;
    }

    return div;
}

// Funzione diminuisci quantità di un

async function diminuisciQuantita(idPiatto, idCarrello) {
    const url = "../../api/cart.php";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "diminuisci",
                ID_carrello: idCarrello,
                ID_piatto: idPiatto
            })
        });

        const result = await response.json();
        // console.log(result);

        if (result.success) {
            mostraMessaggio(result.message);
            getCart();
        } else {
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        console.error(error.message);
    }
}

async function incrementaQuantita(idPiatto, idCarrello) {
    const url = "../../api/cart.php";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "incrementa",
                ID_carrello: idCarrello,
                ID_piatto: idPiatto
            })
        });

        const result = await response.json();
        // console.log(result);

        if (result.success) {
            mostraMessaggio(result.message);
            getCart();
        } else {
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        console.error(error.message);
    }
}

// Funzione che riduce la quantita di un piatto

async function rimuoviPiatto(idPiatto, idCarrello) {
    const url = "../../api/cart.php";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "rimuovi",
                ID_carrello: idCarrello,
                ID_piatto: idPiatto
            })
        });

        // Debug avanzato: status HTTP e body
        if (!response.ok) {
            const text = await response.text();
            console.error(`Errore HTTP ${response.status}: ${text}`);
            mostraMessaggio(`Errore server: ${response.status}`, 'error');
            return;
        }

        const result = await response.json();

        // Debug dettagliato
        // console.log("Risposta API:", result);

        if (result.success) {
            mostraMessaggio(result.message);
            getCart();
        } else {
            // Mostra messaggio di errore con info dettagliate
            console.error("Errore API:", result.error);
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        // Debug completo: stack trace
        console.error("Errore fetch:", error);
        mostraMessaggio(`Errore imprevisto: ${error.message}`, 'error');
    }
}



// Funzione che aggiorna il messaggio

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

async function generaOrdine(ID_cart){
    const url = "../../api/orders.php";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "ordina",
                ID_carrello: ID_cart,
            })
        });

        // Debug avanzato: status HTTP e body
        // if (!response.ok) {
        //     const text = await response.text();
        //     console.error(`Errore HTTP ${response.status}: ${text}`);
        //     mostraMessaggio(`Errore server: ${response.status}`, 'error');
        //     return;
        // }

        const result = await response.json();

        // Debug dettagliato
        // console.log("Risposta API:", result);

        if (result.success) {
            mostraMessaggio(result.message);
            getCart();
        } else {
            // Mostra messaggio di errore con info dettagliate
            // console.error("Errore API:", result.error);
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        // Debug completo: stack trace
        console.error("Errore fetch:", error);
        mostraMessaggio(`Errore imprevisto: ${error.message}`, 'error');
    }
}

async function eliminaCarrello(ID_cart){
    const url = "../../api/cart.php";
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "elimina",
                ID_carrello: ID_cart,
            })
        });

        const result = await response.json();

        // Debug dettagliato
        // console.log("Risposta API:", result);

        if (result.success) {
            mostraMessaggio(result.message);
            getCart();
        } else {
            mostraMessaggio(result.error, 'error');
        }

    } catch (error) {
        // Debug completo: stack trace
        console.error("Errore fetch:", error);
        mostraMessaggio(`Errore imprevisto: ${error.message}`, 'error');
    }
}