<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Elenco Prenotazioni</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        window.onload = function() {
            prenotazioniGet(); // richiamo automatico al caricamento
            document.getElementById("get").addEventListener("click", prenotazioniGet);
        };

        function prenotazioniGet() {
            var user_id = <?php echo $_SESSION['user_id']; ?>;
            // console.log(user_id);
            var oReq = new XMLHttpRequest();
            oReq.onload = function() {
                var dati = JSON.parse(oReq.responseText);
                var container = document.getElementById("table-responsive");
                container.innerHTML = ""; // pulisco contenuto precedente

                dati.forEach(function(r) {
                    var table = document.createElement("table");
                    table.className = "table table-striped table-bordered"; // stile Bootstrap
                    
                    table.innerHTML=`
                            <thead class="table-primary">
                                <tr>
                                    <th>ID Ristorante</th>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Ora</th>
                                    <th>Persone</th>
                                    <th>ID Cliente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${r.ID_ristorante}</td>
                                    <td>${r.ID_prenotazione}</td>
                                    <td>${r.data_prenotazione}</td>
                                    <td>${r.ora_prenotazione}</td>
                                    <td>${r.numero_persone}</td>
                                    <td>${r.ID_cliente}</td>
                                </tr>
                            </tbody>
                    `;
                    container.appendChild(table);
                });
            };

            oReq.onerror = function() {
                document.getElementById("ajaxres").innerHTML = "Errore nella richiesta";
            };

            oReq.open("GET", "http://localhost:8080/prenota2/api/prenotazioni.php/", true);
            oReq.send();
        }
    </script>
    <?php include '../includes/functions.php'; ?>
</head>
<body class="p-4">
    <?php 
    
    if(!isset($_SESSION['user_id'])){
        message("Devi accedere per poter visualizzare questa pagina");
    }?>
    <h1 class="mb-4">Elenco Prenotazioni</h1>
    <button id="get" class="btn btn-primary mb-4">Aggiorna Prenotazioni</button>
    <button class="btn btn-secondary mb-4" onclick="window.location.href='index.php'">Torna ai Ristoranti</button>

    <div id="table-responsive" class="table-responsive"></div>    

</body>
</html>
