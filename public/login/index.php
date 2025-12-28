<?php
require_once '../../includes/functions.php';
session_start();
if (isset($_SESSION['user_id'])) { 
    message("Sei già Logato!","../index.php");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../css/global.css" rel="stylesheet">
    <link href="../../css/login.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid login-container">
    <div class="row min-vh-100">
        <!-- Immagine a sinistra (solo desktop) -->
        <div class="col-lg-6 d-none d-lg-block login-image"></div>
        <!-- Form di login -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="login-form w-100">
                <h3 class="mb-4 text-center fw-bold" style="color: var(--main-blue);">Accedi a <span style="color: #222;">Software Ristoranti</span></h3>
                <form method="POST" action="#">
                    <span id="massage"></span>
                    <div class="mb-3">
                        <label for="email" class="form-label visually-hidden">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                            <input class="form-control" type="email" name="email" id="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label visually-hidden">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                            <input class="form-control" type="password" name="password" id="password" placeholder="Password" required>
                            <span class="input-group-text bg-white" style="cursor:pointer;" onclick="togglePassword()"><i class="bi bi-eye-slash" id="togglePasswordIcon"></i></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div> -->
                        <a href="reset_password.php" class="small" style="color: var(--main-blue);">Password dimenticata?</a>
                    </div>
                    <button class="btn btn-main w-100 mb-2 py-2" type="submit">Sign in</button>
                </form>
                <div class="text-center mt-3">
                    <span>Non hai un account? <a href="sing_up.php" style="color: var(--main-blue); font-weight: 600;">Registati ora</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password toggle
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordInput.type = "password";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }

    // Gestione login AJAX come nel tuo script
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.querySelector("form");
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
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
                if (!response.ok) {
                    showError(data.error || "Errore durante il login.");
                    return;
                }
                showSuccess("Accesso effettuato. Reindirizzamento in corso...");
                setTimeout(() => {
                    window.location.href = "../index.php";
                }, 1500);
            } catch (error) {
                showError("Errore di comunicazione con il server.");
            }
        });
    });

    function showError(msg) {
        let messageSpan = document.getElementById('massage');
        messageSpan.innerHTML = msg + '<br>';
        messageSpan.classList.add('error-text');
        messageSpan.classList.remove('succes-text');
    }
    function showSuccess(msg) {
        let messageSpan = document.getElementById('massage');
        messageSpan.innerHTML = msg + '<br>';
        messageSpan.classList.add('succes-text');
        messageSpan.classList.remove('error-text');
    }
</script>
</body>
</html>
