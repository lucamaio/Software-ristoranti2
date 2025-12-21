<?php session_start(); ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Software Ristoranti</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../css/index.css"rel="stylesheet">

    <script src="../js/index.js" ></script>

    <script>
       
    </script>

</head>
<body class="p-4">
    
    <div class="hero-section">
        <div class="container">
            <h1 class="hero-title">Software Ristoranti</h1>
            <p class="hero-description">Benvenuto nella piattaforma gestionale per i ristoranti. Qui potrai effettuare prenotazioni e ordinazioni direttamente senza dover chiamare. I gestori potranno accettare o rifiutare le tue prenotazioni.</p>
        </div>
    </div>

    <div class="container mt-4">
        <div class="d-flex flex-wrap gap-2 mb-4">
            <!-- <button id="get" class="btn btn-primary btn-modern">
                <i class="bi bi-arrow-clockwise"></i> Aggiorna Ristoranti
            </button> -->
            <?php if(isset($_SESSION['user_id'])){
                $role = $_SESSION['role'] ?? null;
                if($role === 'client' ){ ?>
                    <button id="btn-prenotazioni" class="btn btn-secondary btn-modern">
                        <i class="bi bi-calendar-check"></i> Prenotazioni
                    </button>
                    <button id="btn-ordini" class="btn btn-secondary btn-modern">
                        <i class="bi bi-receipt"></i> Ordini
                    </button>
                    <button id="btn-carrello" class="btn btn-secondary btn-modern">
                        <i class="bi bi-receipt"></i> Carello
                    </button>
                <?php } else if($role === 'restaurant'){?>
                        <button id="btn-addRistorante" class="btn btn-secondary btn-modern">
                            <i class="bi bi-receipt"></i> Aggiungi Ristoranti
                        </button>
                        <button id="btn-prenotazioni" class="btn btn-secondary btn-modern">
                            <i class="bi bi-calendar-check"></i> Prenotazioni
                        </button>
                        <button id="btn-ordini" class="btn btn-secondary btn-modern">
                            <i class="bi bi-receipt"></i> Ordini
                        </button>
                        <button id="btn-pagamenti" class="btn btn-secondary btn-modern">
                            <i class="bi bi-receipt"></i> Pagamenti
                        </button>
                <?php } //else if($role === 'chef'){?>

                <?php // } else if($role === 'admin'){ ?>

                <?php //} ?>
                <button id="btn-profilo" class="btn btn-secondary btn-modern">
                    <i class="bi bi-box-arrow-right"></i> Profilo
                </button>
                <button id="btn-logout" class="btn btn-danger btn-modern">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            <?php }else { ?>
                <button id="btn-login" class="btn btn-danger btn-modern">
                    <i class="bi bi-box-arrow-in-right"></i> Accedi
                </button>
            <?php }?>
        </div>
        
        <div id="show-ristoranti" class="d-flex flex-wrap gap-3"></div> <!-- contenitore per i div -->
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
