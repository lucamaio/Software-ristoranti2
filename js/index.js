  document.addEventListener('DOMContentLoaded', function() {
    getRistoranti(); 

    const more = document.getElementById('btn-more');
    if (more) {
        more.addEventListener('click', function () {
            window.location.href = "ristoranti.php";
        })
    }
});

async function getRistoranti() {
    const url = '../api/ristoranti.php';
    var container = document.getElementById("show-ristoranti");
    container.innerHTML = '';
    
    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ 
                request_type: "get"
            })
        });

        const result = await response.json();

        if(result){
           console.log('success');
           result.data.forEach(r => {
                var div = document.createElement("div");
                 div.className = "restaurant-card";
                div.innerHTML = `
                    <a href="ristoranti/mostra.php?id=${r.ristorante.ID_ristorante}" style="text-decoration: none; color: inherit;">
                        <img src="${r.url_immagine}" alt="Immagine del ristorante" class="restaurant-image">
                        <div class="card-body">
                            <h5 class="card-title">${r.ristorante.nome}</h5>
                            <p class="card-text">
                                <strong>ID:</strong> ${r.ristorante.ID_ristorante}<br>
                                <strong>Indirizzo:</strong> ${r.ristorante.indirizzo} ${r.ristorante.numero_civico}<br>
                                <strong>Telefono:</strong> ${r.ristorante.telefono}<br>
                                <strong>Email:</strong> ${r.ristorante.email}
                            </p>
                        </div>
                    </a>
                `;
                container.appendChild(div);
            });
        } else {
            alert("Errore nel recuperare i ristoranti!");
        }
    } catch(error) {
        console.log("Errore: ", error.message);
    }
}