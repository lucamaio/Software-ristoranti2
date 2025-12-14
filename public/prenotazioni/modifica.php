<form id="form-edit-prenotazione" method="POST" >
    <h2>Modifica una prenotazione</h2>

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
        <button type="submit">Aggiorna</button>
    </div>
</form>

<script>
    const id = <?= $_GET['id'] ?? 0; ?>;
    window.onload = function (){
        getPrenotazione();

        const form = document.getElementById("form-edit-prenotazione");
        if(form){
            form.addEventListener("submit",aggiorna);
        }

    }

    async function getPrenotazione(){
        const url = "../../../api/prenotazioni.php/?id="+id;

        try{
            const response = await fetch(url);
            if(!response.ok) throw new Error(`Response status: ${response.status}`);

            const result = await response.json();
            // console.log(result);

            if(result.error){
                window.location.href = "../../index.php";
                return
            }

            // Modifico il contenuto dei form
            const dataPrenotazione = document.getElementById('data_prenotazione');
            const ora_prenotazione = document.getElementById('ora_prenotazione');
            const persone = document.getElementById('persone');

            dataPrenotazione.value = result.data_prenotazione;
            ora_prenotazione.value = result.ora_prenotazione;
            persone.value = result.numero_persone;
        }catch(error) {
            console.error(error.message);
        }

    }

    async function aggiorna(){
        // Verifico se i campi sono vuoti
        event.preventDefault();
        const data = document.getElementById('data_prenotazione').value;
        const ora = document.getElementById('ora_prenotazione').value;
        const persone = document.getElementById('persone').value;

        if(!data || !ora || !persone){
            alert("Devi inserire tutti i dati!");
            return;
        }

        // console.log(data);
        // console.log(ora);
        // console.log(persone);

        const url = '../../../api/prenotazioni.php';
        try {
            const response = await fetch(url, {  
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ 
                    request_type: "modifica",
                    ID_prenotazione: id,
                    data: data,
                    ora: ora,
                    persone: persone
                })
            });

            const result = await response.json();
            console.log(result);

            if(result.success){
                window.location.href= "../index.php";
            }
        } catch(error) {
            console.error(error.message);
        }

    }
</script>