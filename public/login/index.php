<?php require_once '../../includes/functions.php';
session_start();
if (!isset($_SESSION['user_id'])) { ?>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="../../css/login.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
    <body class="login-image">
        <main class="main-content" id="main-content">
            <section class="login-section">
                <h2>Login</h2>
                <form method="POST" action="#">
                    <span id="massage"></span>
                    <input class="login-input" type="email" name="email" id="email" placeholder="email" required><br>
                    <input class="login-input" type="password" name="password" id="password" placeholder="password" required><br>
                    <a class="login-link" href="reset_password.php">Password dimenticata</a>
                    
                    <div class="buttons">
                        <button class="sign-up" type="submit">Accedi</button>
                        <button class="sign-in" type="button" onclick="window.location.href='sing_up.php'">Registrati</button>
                    </div>
                </form>
            </section>
        </main>
    </body>
<?php } else { 
    message("Sei già Logato!","../index.php");
} ?>
<script>
    document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    form.addEventListener("submit", async (e) => {
        e.preventDefault(); // Evita il refresh della pagina

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        // Controllo basilare client-side
        if (!email || !password) {
            showError("Inserire email e password.");
            return;
        }

        try {
            const response = await fetch("../../api/login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    request_type: "Login",
                    email: email,
                    password: password
                })
            });

            const data = await response.json();
            console.log(data);

            if (!response.ok) {
                showError(data.error || "Errore durante il login.");
                return;
            }

            // Successo
            showSuccess("Accesso effettuato. Reindirizzamento in corso...");

            // Attendere un istante prima del redirect
            setTimeout(() => {
                window.location.href = "../index.php";
            }, 1500);

        } catch (error) {
            showError("Errore di comunicazione con il server.");
            console.error(error);
        }
    });
});


function showError(msg) {
    // alert(msg);
    let messageSpan = document.getElementById('massage');
    messageSpan.innerHTML = msg + '<br>';
    messageSpan.classList.add('error-text');
}

function showSuccess(msg) {
    let messageSpan = document.getElementById('massage');
    messageSpan.innerHTML = msg + '<br>';
    messageSpan.classList.add('succes-text');
    // alert(msg);

}

</script>
