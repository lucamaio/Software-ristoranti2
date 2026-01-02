document.addEventListener('DOMContentLoaded', function() {
    const path = window.location.pathname;
    const num_cart = contaCarattere(path, '/');

    function vaiAllaPagina(stringa) {
        if(num_cart <= 3){
            window.location.href = stringa;
            return;
        }
        let percorso = "";
        const slash = "../";
        for (let i = 0; i < num_cart - 3; i++){
            percorso += slash;
        }
        window.location.href = percorso + stringa;
    }

    const btns = ["btn-homepage","btn-login","btn-logout","btn-register",
                  "btn-prenotazioni","btn-ordini","btn-carrello", "btn-ristorante",
                  "btn-profile","btn-pagamenti"];

    btns.forEach(id => {
        const btn = document.getElementById(id);
        if(btn){
            btn.addEventListener('click', function(e){
                switch(id){
                    case "btn-homepage": vaiAllaPagina("index.php"); break;
                    case "btn-login": vaiAllaPagina("login"); break;
                    case "btn-logout":
                        vaiAllaPagina("logout.php"); break;
                        break;
                    case "btn-register":
                        if(num_cart > 3) window.location.href = "../sign_up.php";
                        else window.location.href = "sign_up.php";
                        break;
                    case "btn-prenotazioni":
                        if(num_cart === 4) window.location.href = "../prenotazioni/index.php";
                        else if(num_cart === 5) window.location.href = "../../prenotazioni/index.php";
                        else window.location.href = "prenotazioni/index.php";
                        break;
                    case "btn-ordini":
                        if(num_cart > 3) window.location.href = "../ordini/index.php";
                        else window.location.href = "ordini/index.php";
                        break;
                    case "btn-carrello":
                        if(num_cart > 3) window.location.href = "../carrello/index.php";
                        else window.location.href = "carrello/index.php";
                        break;
                    case "btn-profile":
                        if(num_cart > 3) window.location.href = "../profilo/index.php";
                        else window.location.href = "profilo/index.php";
                        break;
                    case "btn-pagamenti":
                        if(num_cart > 3) window.location.href = "../pagamenti.php";
                        else window.location.href = "pagamenti.php";
                        break;
                    case "btn-ristorante":
                        const idRistorante = btn.dataset.id;
                        vaiAllaPagina('ristoranti/mostra.php?id='+idRistorante);
                        break;
                }
            });
        }
    });
});

function contaCarattere(stringa, carattere) {
  return stringa.split(carattere).length - 1;
}
