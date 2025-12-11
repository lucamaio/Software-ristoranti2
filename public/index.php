<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Software Ristoranti</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="../css/bootstrap.min.css" rel="stylesheet"> -->

    <script>
        window.onload = function() {
            ristorantiGet(); // richiamo automatico al caricamento
            document.getElementById("get").addEventListener("click", ristorantiGet);
        };

        function ristorantiGet() {
            var oReq = new XMLHttpRequest();
            oReq.onload = function() {
                var dati = JSON.parse(oReq.responseText);
                var container = document.getElementById("ajaxres");
                container.innerHTML = ""; // pulisco contenuto precedente

                dati.forEach(function(r) {
                    var div = document.createElement("div");
                    div.className = "card mb-3"; // stile Bootstrap
                    div.style.width = "18rem";
                    
                    div.innerHTML = `
                    <a href="ristorante.php?id=${r.ID_ristorante}" style="text-decoration: none; color: inherit;">
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
                document.getElementById("ajaxres").innerHTML = "Errore nella richiesta";
            };

            oReq.open("GET", "http://localhost:8080/prenota2/api/ristoranti.php", true);
            oReq.send();
        }
    </script>

</head>
<body class="p-4">
    <?php session_start(); ?>
    <h1 class="mb-4">Software Ristoranti</h1>
    <p class="mb-4" style="font-align: justify">Benvenuto nella piattaforma gestionale per i ristoranti. Qui potrari effetuare delle prenotazioni, ordinazioni direattamente senza dover chiamare. I gestori potranno accettare e rifiutare la tua prenotazioni.</p>
    <button id="get" class="btn btn-primary mb-4">Aggiorna Ristoranti</button>
    <?php if(isset($_SESSION['user_id'])){?>
        <button class="btn btn-secondary mb-4" onclick="window.location.href='prenotazioni.php'">Prenotazioni</button>
        <button class="btn btn-secondary mb-4" onclick="window.location.href='ordini.php'">Ordini</button>
        <button class="btn btn-danger mb-4" onclick="window.location.href='logout.php'">Logout</button>
    <?php }else { ?>
        <button class="btn btn-danger mb-4" onclick="window.location.href='login.php'">Accedi</button>
    <?php }?>
    
    
    <div id="ajaxres" class="d-flex flex-wrap gap-3"></div> <!-- contenitore per i div -->

</body>
</html>
