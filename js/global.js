document.addEventListener('DOMContentLoaded',function (){
    const getHome = document.getElementById('btn-home');
    if(getHome){
        getHome.addEventListener("click", function(){
            window.location.href = "index.php";
        })
    }
});

// Funzione 1: Mostra un messaggio

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

// Funzione 2: Formattazione della data e ora
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