document.addEventListener('DOMContentLoaded', () => {
    getOrders();
});

/* ===========================
   FETCH ORDINI
   =========================== */

async function getOrders() {
    const url = "../../api/orders.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ azione: "get" })
        });

        const result = await response.json();

        if (result.success && Array.isArray(result.data) && result.data.length) {
            renderOrdiniCards(result.data);
        } else {
            mostraMessaggio("Nessun ordine disponibile", "info");
        }

    } catch (error) {
        console.error(error);
        mostraMessaggio("Errore nel caricamento degli ordini", "danger");
    }
}

/* ===========================
   RENDER CARD ORDINI
   =========================== */

function renderOrdiniCards(ordini) {
    const container = document.getElementById("orders-container");
    container.innerHTML = "";

    ordini.forEach(o => {
        const col = document.createElement("div");
        col.className = "col-12 col-sm-12 col-md-6 col-lg-4";

        col.innerHTML = `
            <div class="card h-100 shadow-sm order-card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">
                        <i class="fa-solid fa-calendar-days me-2"></i>
                        ${formatDataOra(o.ordine?.data_ordine)}
                    </span>
                    <span class="badge ${badgeStato(o.stato_ordine)}">
                        ${o.stato_ordine ?? 'N/D'}
                    </span>
                </div>

                <div class="card-body d-flex flex-column">
                    <p class="mb-2">
                        <i class="fa-solid fa-utensils text-primary me-2"></i>
                        <strong>${o.nome_ristorante ?? 'N/D'}</strong>
                    </p>

                    <p class="mt-auto mb-0 text-muted">
                        Totale:
                        <span class="fw-bold text-success fs-5">
                            ${o.totale ?? 'N/D'} €
                        </span>
                    </p>
                </div>

                <div class="card-footer bg-light d-flex justify-content-end gap-2 flex-wrap">
                    ${generaAzioniOrdine(o.ID_ordine, o.stato_ordine, 'client')}
                </div>
            </div>
        `;

        container.appendChild(col);
    });
}

/* ===========================
   AZIONI ORDINE
   =========================== */

function generaAzioniOrdine(ID_ordine, stato_ordine, role) {
    let html = `
        <button class="btn btn-sm btn-outline-primary btn-details"
                data-ordine="${ID_ordine}">
            <i class="fa-solid fa-eye"></i> Dettagli
        </button>
    `;

    if (role === 'client' && stato_ordine === 'In attesa') {
        html += `
            <button class="btn btn-sm btn-outline-danger btn-delete"
                    data-ordine="${ID_ordine}">
                <i class="fa-solid fa-trash"></i> Annulla
            </button>
        `;
    }

    if (role === 'client' && stato_ordine === 'Completato') {
        html += `
            <button class="btn btn-sm btn-success btn-pay"
                    data-ordine="${ID_ordine}">
                <i class="fa-solid fa-credit-card"></i> Paga
            </button>
        `;
    }

    return html;
}

/* ===========================
   EVENT DELEGATION
   =========================== */

document.addEventListener("click", e => {
    const btn = e.target.closest("button");
    if (!btn?.dataset.ordine) return;

    const ID_ordine = btn.dataset.ordine;

    if (btn.classList.contains("btn-details")) {
        window.location.href = `dettagli.php?id=${ID_ordine}`;
    }

    if (btn.classList.contains("btn-delete")) {
        annullaOrdine(ID_ordine);
    }

    if (btn.classList.contains("btn-pay")) {
        pagaOrdine(ID_ordine);
    }
});

/* ===========================
   SUPPORTO
   =========================== */

function badgeStato(stato) {
    switch (stato) {
        case 'In attesa': return 'bg-warning text-dark';
        case 'Completato': return 'bg-success';
        case 'Annullato': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function formatDataOra(dataString) {
    if (!dataString) return "N/D";
    return new Date(dataString).toLocaleString("it-IT", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
}

function mostraMessaggio(testo, tipo = "info") {
    document.getElementById("orders-container").innerHTML = `
        <div class="col-12">
            <div class="alert alert-${tipo} text-center shadow-sm">
                <i class="fa-solid fa-circle-info me-2"></i>${testo}
            </div>
        </div>
    `;
}

function annullaOrdine(ID_ordine) {
    console.log("Funzione da implementare Annulla ordine:", ID_ordine);
}

function pagaOrdine(ID_ordine) {
    console.log("Funzione da implementare Paga ordine:", ID_ordine);
}
