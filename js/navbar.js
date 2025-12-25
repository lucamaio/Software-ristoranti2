document.addEventListener('DOMContentLoaded', function() {
    // 1. Mi ricavo il percorso relativo e il numero di separatori

    console.log(window.location.pathname); // percorso relativo (es: /prenota2/index.php)
    const path = window.location.pathname;
    const num_cart = contaCarattere(path, '/');
    console.log(num_cart);

    // Controllo se la navbar esiste
    const navbar = document.getElementById('btn-homepage'); // o qualsiasi ID della navbar
    if (navbar) {
        // Inizializzo la navbar
        const homepageButton = document.getElementById("btn-homepage");
        if (homepageButton) {
            homepageButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../index.php";
                }else{
                    window.location.href = "index.php";
                }
            });
        }

        const loginButton = document.getElementById("btn-login");
        if (loginButton) {
            loginButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../login";
                }else{
                    window.location.href = "login";
                }
            });
        }

        const logoutButton = document.getElementById("btn-logout");
        if (logoutButton) {
            logoutButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../logout.php";
                }else{
                    window.location.href = "logout.php";
                }
            });
        }

        const bookingButton = document.getElementById("btn-prenotazioni");
        if (bookingButton) {
            bookingButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../prenotazioni/index.php";

                }else{
                    window.location.href = "prenotazioni/index.php";
                }
            });
        }

        const ordiniButton = document.getElementById('btn-ordini');
        if (ordiniButton) {
            ordiniButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../ordini.php";

                }else{
                    window.location.href = "ordini.php";
                }
            });
        }

        const cartButton = document.getElementById('btn-carrello');
        if (cartButton) {
            cartButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../carrello/index.php";

                }else{
                    window.location.href = "carrello/index.php";
                }
            });
        }

        const profileButton = document.getElementById('btn-profile');
        if (profileButton) {
            profileButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../profilo/index.php";

                }else{
                    window.location.href = "profilo/index.php";
                }
            });
        }

        const payButton = document.getElementById('btn-pagamenti');
        if (payButton) {
            payButton.addEventListener('click', function () {
                if(num_cart > 3 ){
                    window.location.href = "../pagamenti.php";

                }else{
                    window.location.href = "pagamenti.php";
                }
            });
        }
    }
});


function contaCarattere(stringa, carattere) {
  // Dividi la stringa in un array usando il carattere come separatore.
  // Il numero di occorrenze sarà uguale alla lunghezza dell'array - 1.
  return stringa.split(carattere).length - 1;
}
