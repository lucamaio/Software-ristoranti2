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