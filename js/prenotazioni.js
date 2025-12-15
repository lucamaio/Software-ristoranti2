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
    // console.log(idPrenotazione);
    if(role === 'client'){
         div.innerHTML = `
            <button class="btn btn-sm btn-outline-primary me-2 mb-2 btn-edit" data-id="${idPrenotazione}" title="Modifica prenotazione">
                <i class="fa-solid fa-gear"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger me-2 mb-2 btn-cancel" data-id="${idPrenotazione}" title="Cancella prenotazione">
                <i class="fa-solid fa-x"></i>
            </button>
        `;
    }else if(role === "restaurant"){
        div.innerHTML = `
            <button class="btn btn-sm btn-outline-primary me-2 mb-2 btn-accept" data-id="${idPrenotazione}" title="Accetta prenotazione">
                <i class="fa-solid fa-check"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger me-2 mb-2 btn-decline" data-id="${idPrenotazione}" title="Rifiuta Prenotazione">
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

async function accettaPrenotazione(id){
    const url = "../../api/prenotazioni.php/?id="+id;
    try {
        const response = await fetch(url, {  
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ 
                request_type: "accetta",
                ID_prenotazione: id
            })
        });

        const result = await response.json();
        console.log(result);
        const messageDiv = document.getElementById('message');

       if(result.success){
            mostraMessaggio("Prenotazione accettata!", 'success');
            getPrenotazioni();
        } else {
            mostraMessaggio("Accettazione non riuscita!", 'error');
        }

    } catch(error) {
        console.error(error.message);
    }
}

async function declinaPrenotazione(id){
    const url = "../../api/prenotazioni.php/?id="+id;
    try {
        const response = await fetch(url, {  
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ 
                request_type: "declina",
                ID_prenotazione: id
            })
        });

        const result = await response.json();
        console.log(result);
        const messageDiv = document.getElementById('message');

       if(result.success){
            mostraMessaggio("Prenotazione rifiutata!", 'success');
            getPrenotazioni();
        } else {
            mostraMessaggio("Operazione non riuscita!", 'error');
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
        // console.log(role);

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
        var riga = 1;

        dati.forEach(function (r) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${r.prenotazione.data_prenotazione} ${r.prenotazione.ora_prenotazione}</td>
                <td>${r.nomeRistorante}</td>
                <td>${r.nominativo}</td>
                <td>${r.prenotazione.numero_persone}</td>
                <td id="tavolo-${r.prenotazione.ID_prenotazione}" class="col-mobile-hide">${r.tavolo ?? '-'}</td>  <!-- Nascosto su mobile -->
                <td class="col-mobile-hide">${r.statoPrenotazione ?? '-' }</td>  <!-- Nascosto su mobile -->
            `;
            const tdAzioni = document.createElement('td');
            tdAzioni.classList = 'text-center';
            if(r.statoPrenotazione === 'In Attesa' || r.statoPrenotazione === 'Modificata'){
                tdAzioni.appendChild(creaPulsantiAzioni(role, r.prenotazione.ID_prenotazione));
            }else{
                tdAzioni.innerHTML = `<td> </td>`;
            }
            tr.appendChild(tdAzioni);
            tbody.appendChild(tr);
            
            console.log("riga: "+riga+" | ID_stato_prenotazione: "+r.statoPrenotazione +" | Tavolo: "+  r.tavolo +" | ID_prenotazione: "+ r.prenotazione.ID_prenotazione + " | ID_ristorante: "+ r.prenotazione.ID_ristorante+"\n");
            console.log(r);
           
            
           
            
            // Carico selcet tavolo se serve
            if(role === 'restaurant' && r.statoPrenotazione === "Confermata" && r.tavolo === null){
                caricaSelectTavolo(r.prenotazione.ID_prenotazione, r.prenotazione.ID_ristorante);
            }
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

            if(e.target.closest('.btn-accept')){
                const id = e.target.closest('.btn-accept').dataset.id;
                accettaPrenotazione(id);
            }

            if(e.target.closest('.btn-decline')){
                const id = e.target.closest('.btn-decline').dataset.id;
                declinaPrenotazione(id);
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
// Funzione che gestisce il tavolo

async function caricaSelectTavolo(ID_prenotazione, ID_ristorante){
    // Mi ricavo la colonna da modificare
    const td_tavolo = document.getElementById("tavolo-"+ID_prenotazione);
    if(!td_tavolo){
        return;
    }  

    // Creo una sezione che consente di selezionare il tavolo
    const selectTavolo = document.createElement('select');
    selectTavolo.className = 'form-select form-select-sm';
    selectTavolo.innerHTML = '<option value="">Seleziona un tavolo</option>';

    // Mi ricavo dall'API i tavoli
    const url = "../../api/tavoli.php";
    try {
        const response = await fetch(url, {  
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ 
                request_type: "get",
                ID_ristorante: ID_ristorante
            })
        });

        const result = await response.json();
        console.log(result.success);
        const messageDiv = document.getElementById('message');

       if(result.success){
           result.data.forEach(t => {
                const option = document.createElement('option');
                option.value = t.ID_tavolo;
                option.text = t.numero_tavolo;
                selectTavolo.appendChild(option);
            });
            td_tavolo.innerHTML = '';
            td_tavolo.appendChild(selectTavolo);
        } else {
            console.log('Tavoli mancanti!');
        }

        selectTavolo.addEventListener('change', function () {
            setTavolo(ID_prenotazione, selectTavolo.value);
        });

    } catch(error) {
        console.error(error.message);
    }

}

async function setTavolo(ID_prenotazione, ID_tavolo) {

    if (!ID_tavolo) {
        return;
    }

    const url = "../../api/tavoli.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                request_type: "set",
                ID_prenotazione: ID_prenotazione,
                ID_tavolo: ID_tavolo
            })
        });

        const result = await response.json();
        console.log(result);

        if (result.success) {
            mostraMessaggio("Tavolo assegnato correttamente", "success");
            getPrenotazioni(); // ricarico la tabella
        } else {
            mostraMessaggio(result.error ?? "Errore durante l'assegnazione", "error");
        }

    } catch (error) {
        console.error(error);
        mostraMessaggio("Errore di comunicazione con il server", "error");
    }
}


// Funzione che aggiorna il messaggio

function mostraMessaggio(testo, tipo='success') {
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
