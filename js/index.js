  window.onload = function() {
            ristorantiGet(); // richiamo automatico al caricamento
            // document.getElementById("get").addEventListener("click", ristorantiGet); // Pulsante aggiorna risultati

            const  loginButton = document.getElementById("btn-login");
            if(loginButton){
                loginButton.addEventListener('click', function(){
                    window.location.href = "login.php";
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
                    window.location.href = "carrello.php"; 
                })
            }

            const more = document.getElementById('btn-more');
            if(more){
                more.addEventListener('click',function(){
                    window.location.href = "ristoranti.php";
                })
            }
        };
 function ristorantiGet() {
            var oReq = new XMLHttpRequest();
            oReq.onload = function() {
                var dati = JSON.parse(oReq.responseText);
                var container = document.getElementById("ajaxres");
                container.innerHTML = ""; // pulisco contenuto precedente

                dati.forEach(function(r) {
                    var div = document.createElement("div");
                    div.className = "restaurant-card"; // stile personalizzato
                    
                    div.innerHTML = `
                    <a href="ristorante.php?id=${r.ID_ristorante}" style="text-decoration: none; color: inherit;">
                        <img src="../ristorante.png" alt="Immagine del ristorante" class="restaurant-image">
                        <div class="card-body">
                            <h5 class="card-title">${r.nome}</h5>
                            <p class="card-text">
                                <strong>ID:</strong> ${r.ID_ristorante}<br>
                                <strong>Indirizzo:</strong> ${r.indirizzo}<br>
                                <strong>Telefono:</strong> ${r.telefono}<br>
                                <strong>Email:</strong> ${r.email}
                            </p>
                        </div>
                    </a>
                    `;
                    container.appendChild(div);
                });
            };

            oReq.onerror = function() {
                document.getElementById("ajaxres").innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> Errore nella richiesta dei ristoranti.
                    </div>
                `;
            };

            oReq.open("GET", "../api/ristoranti.php", true); // Cambiato URL relativo
            oReq.send();
        }

       