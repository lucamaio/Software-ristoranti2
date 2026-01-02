document.addEventListener('DOMContentLoaded', () => {
    showLoader();
    ricavaOrdini();
});

/*
----------------------- FUNZIONI -----------------------
*/

/*
    Funzione 1: ricava ordini dal api,tramite fetch
*/

async function ricavaOrdini() {
    const url = "../../api/orders.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ azione: "get" })
        });

        const result = await response.json();
        // console.log(result);

        hideLoader();

        if (result.success && Array.isArray(result.data) && result.data.length) {
            creaOrdiniCards(result.data, result.ruolo);
        } else {
            mostraMessaggio("Nessun ordine disponibile", "info");
        }

    } catch (error) {
        console.error(error);
        hideLoader();
        mostraMessaggio("Errore nel caricamento degli ordini", "danger");
    }
}


/*
    Funzione 2: Caricamento delle card 
*/

function creaOrdiniCards(ordini,ruolo) {
    const container = document.getElementById("orders-container");
    container.innerHTML = "";

    ordini.forEach(o => {
        const card = document.createElement("div");
        card.className = "order-card";

        card.innerHTML = `
            <div class="order-top">
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    <strong>${formatDataOra(o.ordine?.data_ordine)}</strong>
                </span>

                <span class="order-badge ${badgeStato(o.stato_ordine)}">
                    ${o.stato_ordine}
                </span>
            </div>

            <div class="order-title">
                ${o.nome_ristorante}
            </div>

            <div class="order-total">
                ${o.totale} €
            </div>

            <div class="order-actions">
                ${generaAzioniOrdine(o.ordine.ID_ordine, o.stato_ordine,ruolo)}
            </div>
        `;

        container.appendChild(card);
    });
}


/*
    Funzione 3: Inserisci i pulsanti azioni
*/

function generaAzioniOrdine(ID_ordine, stato_ordine, ruolo) {
    let html = `
        <button class="btn btn-sm btn-outline-primary btn-details"
                data-ordine="${ID_ordine}">
            <i class="fa-solid fa-eye"></i> Dettagli
        </button>
    `;

    switch(ruolo){
        case 'cliente':
            if(stato_ordine === 'In attesa') {
                html += `
                    <button class="btn btn-sm btn-outline-danger btn-delete"
                            data-ordine="${ID_ordine}">
                        <i class="fa-solid fa-trash"></i> Annulla
                    </button>
                `;
            }else if(stato_ordine === 'Completato'){
                html += `
                    <button class="btn btn-sm btn-success btn-pay"
                            data-ordine="${ID_ordine}">
                        <i class="fa-solid fa-credit-card"></i> Paga
                    </button>
                `;
            }
            break;
        case 'ristoratore':
            if(stato_ordine === 'Completato') {
                html += `
                    <button class="btn btn-sm btn-outline-success btn-paid"
                            data-ordine="${ID_ordine}">
                        <i class="fa-solid fa-check-circle"></i> Pagato
                    </button>
                `;
            }
        case 'cuoco':
            if(stato_ordine === 'In attesa'){
                 html += `
                    <button class="btn btn-sm btn-outline-waring btn-take"
                            data-ordine="${ID_ordine}">
                        <i class="fa-solid fa-check-circle"></i> Prendi a carico
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-rifiuta"
                            data-ordine="${ID_ordine}">
                        <i class="fa-solid fa-times-circle"></i> Rifiuta
                    </button>
                `;
            }else if(stato_ordine === 'In elaborazione'){
                 html += `
                    <button class="btn btn-sm btn-outline-success btn-completato"
                            data-ordine="${ID_ordine}">
                        <i class="fa-solid fa-check-circle"></i> Completato
                    </button>
                `;
            }
            break;
        
    }

    return html;
}

/*
    Funzione 4: mostra il loader
*/
function showLoader() {
    document.getElementById("orders-loader").classList.remove("d-none");
    document.getElementById("orders-container").classList.add("d-none");
}

/*
    Funzione 5: nascondi il loader
*/

function hideLoader() {
    document.getElementById("orders-loader").classList.add("d-none");
    document.getElementById("orders-container").classList.remove("d-none");
}

/*
    Funzione 6: visualizza il messaggio ricevuto come risultato
*/

/*
function mostraMessaggio(testo, tipo = "info") {
    document.getElementById("orders-container").innerHTML = `
        <div class="col-12">
            <div class="alert alert-${tipo} text-center shadow-sm">
                <i class="fa-solid fa-circle-info me-2"></i>${testo}
            </div>
        </div>
    `;
}*/

/*
----------------------- AZIONI -----------------------
*/

/* 
    Azione 1: Gestione della azione da eseguire a seconda del pulsante
*/

document.addEventListener("click", e => {
    const btn = e.target.closest("button");
    if (!btn?.dataset.ordine) return;

    const ID_ordine = btn.dataset.ordine;

    if (btn.classList.contains("btn-details")) {
        window.location.href = `dettagli.php?id=${ID_ordine}`;
    }

    if (btn.classList.contains("btn-take")) {
        cambiaStatoOrdine(ID_ordine,7);
    }

     if (btn.classList.contains("btn-completato")) {
        cambiaStatoOrdine(ID_ordine,3);
    }

    if (btn.classList.contains("btn-delete")) {
        cambiaStatoOrdine(ID_ordine,4);
    }

    if (btn.classList.contains("btn-pay")) {
        // cambiaStatoOrdine(ID_ordine,8);
        console.log("Funzionalità da implementare!");
    }

    if (btn.classList.contains("btn-paid")) {
        cambiaStatoOrdine(ID_ordine,8);
        // console.log("Funzionalità da implementare!");
    }
    ricavaOrdini();
});


function badgeStato(stato) {
    switch (stato) {
        case 'In attesa': return 'bg-warning text-dark';
        case 'Completato': return 'bg-success';
        case 'Annullato': return 'bg-danger';
        case 'Pagato': return 'bg-primary text-white';
        default: return 'bg-secondary';
    }
}

// Azione 2: Cambia lo stato del ordine

async function cambiaStatoOrdine(ID_ordine, nuovo_stato) {
    //console.log("Funzione da implementare elabora ordine:", ID_ordine);
    const url = "../../api/orders.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ azione: "cambia-stato",ID_ordine: ID_ordine, nuovo_stato: nuovo_stato })
        });

        const result = await response.json();
        console.log(result);

        if (result.success && Array.isArray(result.data) && result.data.length) {
            creaOrdiniCards(result.data, result.ruolo);
        } else {
            mostraMessaggio("Nessun ordine disponibile", "info");
        }

    } catch (error) {
        console.error(error);
        mostraMessaggio("Errore nel caricamento degli ordini", "danger");
    }
}



