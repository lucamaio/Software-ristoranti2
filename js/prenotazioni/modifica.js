document.addEventListener('DOMContentLoaded', () => {
    // 1. Mi ricavo il form
    const form = document.getElementById("form-edit-prenotazione");
    if (!form) return;

    // 2. carico i dettagli nel form
    ricavaDettagliPrenotazione();

    // 3. crea un Listener 
    form.addEventListener("submit", aggiorna);
});

function getIdPrenotazione() {
    return document.getElementById('id_prenotazione').value;
}

async function ricavaDettagliPrenotazione() {
    const id = getIdPrenotazione();
    const url = "../../../api/prenotazioni.php";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "dettagli_prenotazione",
                ID_prenotazione: id

            })
        });
        if (!response.ok) throw new Error(`Response status: ${response.status}`);

        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        const result = await response.json();
        // console.log(result);

        if (result.success && result.data) {
            document.getElementById('data_prenotazione').value = result.data.data_prenotazione;
            document.getElementById('ora_prenotazione').value = result.data.ora_prenotazione;
            document.getElementById('persone').value = result.data.numero_persone;
        } else {
            mostraMessaggio(result.error || "Errore nel recupero dati prenotazione", "error");
        }

    } catch (error) {
        console.error(error.message);
        mostraMessaggio("Errore nel API " + error.message);
    }
}

async function aggiorna(event) {
    event.preventDefault(); //  blocca refresh automatico

    const id = getIdPrenotazione();
    const data = document.getElementById('data_prenotazione').value;
    const ora = document.getElementById('ora_prenotazione').value;
    const persone = document.getElementById('persone').value;

    if (!id || !data || !ora || !persone) {
        mostraMessaggio("Devi compilare tutti i campi!", "error");
        return;
    }

    const url = '../../../api/prenotazioni.php';

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                azione: "aggiorna_prenotazione",
                ID_prenotazione: id,
                data_prenotazione: data,
                ora_prenotazione: ora,
                persone: parseInt(persone, 10)
            })
        });

        const result = await response.json();
        // console.log(result);

        if (result.success) {
            mostraMessaggio(result.message);
            // reindirizza dopo 2 secondi
            setTimeout(() => {
                window.location.href = "../index.php";
            }, 2000);
        } else {
            // console.error(result.error);
            mostraMessaggio(result.error || "Errore nell'aggiornamento!", "error");
        }

    } catch (error) {
        console.error(error.message);
        mostraMessaggio("Errore di rete: " + error.message, "error");
    }
}