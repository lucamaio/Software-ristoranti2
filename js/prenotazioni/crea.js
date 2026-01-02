document.addEventListener('DOMContentLoaded', () => {
    getRistoranti();

    const form = document.getElementById("form-prenotazione");
    if (form) {
        form.addEventListener("submit", prenota);
    }
});

// Funzione 1: Verifico la data se è valida. ovvero se è maggioriore di oggi

function verificaData(dateString) {
    // Converto la stringa in oggetto Date
    const inputDate = new Date(dateString);

    // Creo un oggetto Date per la data odierna (senza orario)
    const today = new Date();
    today.setHours(0, 0, 0, 0); // azzera ore, minuti, secondi e millisecondi

    // Confronto
    return inputDate > today;
}

// Funzione 2: Mi ricavo i ristoranti

async function getRistoranti() {
    const url = "../../api/ristoranti.php";

    try {
        const response = await fetch(url,{
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                request_type: "get",
            })
        });
        if (!response.ok) throw new Error(`Response status: ${response.status}`);

        const result = await response.json();
        console.log(result);

        const selectRistorante = document.getElementById('ristorante');
        if (selectRistorante) {
            selectRistorante.innerHTML = '<option value="">-- Seleziona un ristorante --</option>';

            result.data.forEach(r => {
                const option = document.createElement('option');
                option.value = r.ristorante.ID_ristorante;
                option.text = r.ristorante.nome;
                selectRistorante.appendChild(option);
            });
        }
    } catch (error) {
        console.error(error.message);
    }
}

// Funzione 3: effetua la prenotazione

async function prenota(event) {
    event.preventDefault();

    const ristorante = document.getElementById('ristorante').value;
    const data = document.getElementById('data_prenotazione').value;
    const ora = document.getElementById('ora_prenotazione').value;
    const persone = document.getElementById('persone').value;

    if (!ristorante || !data || !ora || !persone) {
        mostraMessaggio("Devi inserire tutti i dati!", 'error');
        return;
    }
    if (!verificaData(data)) {
        mostraMessaggio("Devi inserire una data futura!", 'error');
        return;
    }

    if (!verificaData(data)) {
        alert("Devi inserire una data futura!");
        return;
    }

    const url = '../../api/prenotazioni.php';

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                request_type: "prenota",
                ID_ristorante: ristorante,
                data: data,
                ora: ora,
                persone: persone
            })
        });

        const result = await response.json();

        if (result.success) {
            mostraMessaggio(result.message || "Prenotazione effettuata con successo!", 'success');

            setTimeout(() => {
                window.location.href = "../index.php";
            }, 2000);
        } else {
            mostraMessaggio("Errore nella prenotazione!", 'error');
        }
    } catch (error) {
        console.error(error.message);
    }
}