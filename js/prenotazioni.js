window.onload = function () {
    getPrenotazioni();

    const getBtn = document.getElementById("get");
    if (getBtn) {
        getBtn.addEventListener("click", prenotazioniGet);
    }

    const btn_prenota = document.getElementById('btn-prenota');
    if(btn_prenota){
        btn_prenota.addEventListener('click', function(){
            window.location.href = "crea.php";
        })
    }

    const back_home = document.getElementById('btn-home');
    if(back_home){
        back_home.innerHTML = `
            <div class="hero-section">
                <a href="../index.php" class="btn btn-dark btn-modern back-button"> 
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>`;
    }
};

function creaPulsantiAzioni(role, idPrenotazione){
    const div = document.createElement('div');

    if(role === 'client'){
         div.innerHTML = `
            <button class="btn btn-sm btn-outline-primary me-2 mb-2 btn-edit" data-id="${idPrenotazione}" title="Modifica prenotazione">
                <i class="fa-solid fa-gear"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger me-2 mb-2 btn-cancel" data-id="${idPrenotazione}" title="Cancella prenotazione">
                <i class="fa-solid fa-x"></i>
            </button>
        `;
    }
    return div;
}

async function cancellaPrenotazione(id) {
    const url = "../../api/prenotazioni.php/?id="+id;
    try {
        const response = await fetch(url, {  
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ 
                request_type: "cancella",
                ID_prenotazione: id
            })
        });

        const result = await response.json();
        console.log(result);
        const messageDiv = document.getElementById('message');

       if(result.success){
            mostraMessaggio("Prenotazione aggiornata!", 'success');
            getPrenotazioni();
        } else {
            mostraMessaggio("Aggiornamento non riuscito!", 'error');
        }

    } catch(error) {
        console.error(error.message);
    }
}

function getPrenotazioni() {
    const oReq = new XMLHttpRequest();

    oReq.onload = function () {
        const dati = JSON.parse(oReq.responseText);
        const container = document.getElementById("table-responsive");
        container.innerHTML = "";
        var role = window.APP_CONFIG.role;
        console.log(role);

        // Card wrapper
        const card = document.createElement("div");
        card.className = "card shadow-sm";

        card.innerHTML = `
            <div class="card-header bg-white fw-semibold">
                Prenotazioni effettuate
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Ristorante</th>
                                <th>Nominativo</th>
                                <th>Persone</th>
                                <th class="col-mobile-hide">Tavolo</th>  <!-- Nascosto su mobile -->
                                <th class="col-mobile-hide">Stato Prenotazione</th>  <!-- Nascosto su mobile -->
                                <th class="text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="prenotazioni-body"></tbody>
                    </table>
                </div>
            </div>
        `;

        container.appendChild(card);

        const tbody = document.getElementById("prenotazioni-body");

        dati.forEach(function (r) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${r.prenotazione.data_prenotazione} ${r.prenotazione.ora_prenotazione}</td>
                <td>${r.nomeRistorante}</td>
                <td>${r.nominativo}</td>
                <td>${r.prenotazione.numero_persone}</td>
                <td class="col-mobile-hide">${r.tavolo === null ? 'N/D' : r.tavolo}</td>  <!-- Nascosto su mobile -->
                <td class="col-mobile-hide">${r.statoPrenotazione === null ? 'N/D' : r.statoPrenotazione}</td>  <!-- Nascosto su mobile -->
            `;
            const tdAzioni = document.createElement('td');
            tdAzioni.classList = 'text-center';
            if(r.statoPrenotazione == 'In Attesa' || r.statoPrenotazione === 'Modificata'){
                tdAzioni.appendChild(creaPulsantiAzioni(role, r.prenotazione.ID_prenotazione));
            }else{
                tdAzioni.innerHTML = `<td> </td>`;
            }
            tr.appendChild(tdAzioni);
            tbody.appendChild(tr);
        });

        tbody.addEventListener("click", function(e){
            if (e.target.closest('.btn-edit')) {
                const id = e.target.closest('.btn-edit').dataset.id;
                window.location.href="modifica.php/?id="+ id;
            }

            if (e.target.closest('.btn-cancel')) {
                const id = e.target.closest('.btn-cancel').dataset.id;
                cancellaPrenotazione(id);
            }
        });
    };

    oReq.onerror = function () {
        document.getElementById("table-responsive").innerHTML = `
            <div class="alert alert-danger m-3">
                Errore nella richiesta delle prenotazioni
            </div>
        `;
    };

    oReq.open("GET", "http://localhost:8080/prenota2/api/prenotazioni.php", true);
    oReq.send();
}

function mostraMessaggio(testo, tipo='success') {
    const messageDiv = document.getElementById('message');
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
