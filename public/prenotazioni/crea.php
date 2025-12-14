<html>
    <head>
        <meta charset="utf-8">
        <title>Effetua una prenotazione</title>
        <!-- Icone -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
        <link href="../../css/index.css" rel="stylesheet">
        <link href="../../css/prenotazioni.css" rel="stylesheet">

        <script>
            const back_home = document.getElementById('btn-home');
            if(back_home){
                back_home.innerHTML = `
                    <div class="hero-section">
                        <a href="../index.php" class="btn btn-dark btn-modern back-button"> 
                            <i class="bi bi-arrow-left"></i>
                        </a>
                    </div>`;
            }
        </script>
        
    </head>
    <body>
       <div id="btn-home"></div>
        <form id="form-prenotazione" method="POST" >
            <h2>Effettua una prenotazione</h2>

            <div>
                <label >Seleziona Ristorante:</label>
                <select name="ristorante" id="ristorante" required></select>
            </div>

            <div>
                <label for="data_prenotazione">Data prenotazione:</label>
                <input type="date" name="data_prenotazione" id="data_prenotazione" required>
            </div>

            <div>
                <label for="ora_prenotazione">Ora prenotazione:</label>
                <input type="time" name="ora_prenotazione" id="ora_prenotazione" required>
            </div>

            <div>
                <label for="persone">Numero di persone:</label>
                <input type="number" name="persone" id="persone" required min="1">
            </div>

            <div>
                <button type="submit">Prenota</button>
            </div>
        </form>
    </body>
</html>

<script>
window.onload = function () {
    getRistoranti();

    const form = document.getElementById("form-prenotazione");
    if(form){
        form.addEventListener("submit", prenota);
    }
};

function verificaData(dateString) {
    // Converto la stringa in oggetto Date
    const inputDate = new Date(dateString);
    
    // Creo un oggetto Date per la data odierna (senza orario)
    const today = new Date();
    today.setHours(0, 0, 0, 0); // azzera ore, minuti, secondi e millisecondi
    
    // Confronto
    return inputDate > today;
}


async function getRistoranti() {
    const url = "../../api/ristoranti.php";

    try {
        const response = await fetch(url);
        if(!response.ok) throw new Error(`Response status: ${response.status}`);

        const result = await response.json();
        // console.log(result);

        const selectRistorante = document.getElementById('ristorante');
        if(selectRistorante){
            selectRistorante.innerHTML = '<option value="">-- Seleziona un ristorante --</option>';

            result.forEach(r => {
                const option = document.createElement('option');
                option.value = r.ID_ristorante;
                option.text = r.nome;
                selectRistorante.appendChild(option);
            });
        }
    } catch(error) {
        console.error(error.message);
    }
}

async function prenota(event) {
    event.preventDefault();

    const ristorante = document.getElementById('ristorante').value;
    const data = document.getElementById('data_prenotazione').value;
    const ora = document.getElementById('ora_prenotazione').value;
    const persone = document.getElementById('persone').value;

    if(!ristorante || !data || !ora || !persone){
    mostraMessaggio("Devi inserire tutti i dati!", 'error');
        return;
    }
    if(!verificaData(data)){
        mostraMessaggio("Devi inserire una data futura!", 'error');
        return;
    }

    if(!verificaData(data)){
        alert("Devi inserire una data futura!");
        return;
    }

    // console.log(ristorante);
    // console.log(data);
    // console.log(ora);
    // console.log(persone);

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
        // console.log(result);

        if(result.success){
            mostraMessaggio(result.message || "Prenotazione effettuata con successo!", 'success');
            
            setTimeout(() => {
                window.location.href = "index.php";
            }, 2000);
        } else {
            mostraMessaggio("Errore nella prenotazione!", 'error');
        }
    } catch(error) {
        console.error(error.message);
    }
}

</script>
