  window.onload = function() {
            getRistoranti(); // richiamo automatico al caricamento
            // document.getElementById("get").addEventListener("click", ristorantiGet); // Pulsante aggiorna risultati

            const  loginButton = document.getElementById("btn-login");
            if(loginButton){
                loginButton.addEventListener('click', function(){
                    window.location.href = "login";
                });
            }

            const logoutButton = document.getElementById("btn-logout");
            if(logoutButton){
                logoutButton.addEventListener('click', function(){
                    window.location.href = "logout.php";
                });
            }

            const bookingButton = document.getElementById("btn-prenotazioni");
            if(bookingButton){
                bookingButton.addEventListener('click',function() {
                    window.location.href = "prenotazioni/index.php";
                });
            } 
            
            const ordiniButton = document.getElementById('btn-ordini');
            if(ordiniButton){
                ordiniButton.addEventListener('click', function(){
                    window.location.href = "ordini.php";
                })
            }

            const cartButton = document.getElementById('btn-carrello');
            if(cartButton){
                cartButton.addEventListener('click', function(){
                    window.location.href = "carrello/index.php"; 
                })
            }

            const more = document.getElementById('btn-more');
            if(more){
                more.addEventListener('click',function(){
                    window.location.href = "ristoranti.php";
                })
            }
        };

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
        //console.log(result);

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
            alert("Errore nella prenotazione!");
        }
    } catch(error) {
        console.log("Errore: ", error.message);
    }
}