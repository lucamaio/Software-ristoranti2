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

   <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/login.js" defer></script>
    <scrip src="../../js/global.js" defer></script>
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


</body>
</html>
