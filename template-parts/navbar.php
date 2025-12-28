<nav class="bottom-nav">
    <button class="btn-modern bottom-nav-item me-2" id="btn-homepage">
        <i class="bi bi-house"></i>
        <span>Home</span>
    </button>

    <?php if(isset($_SESSION['user_id'])){ 
        $role = $_SESSION['role'] ?? null;

        if($role === 'client' ){ ?>
            <button class="btn-modern bottom-nav-item me-2 mx-auto" id="btn-prenotazioni">
                <i class="bi bi-calendar-check"></i> 
                <span class="d-flex">Prenotazioni</span>
            </button>

            <button class="btn-modern bottom-nav-item me-2 mx-auto" id="btn-carrello">
                <i class="bi bi-cart"></i>
                <span class="d-flex">Carrelli</span>
            </button>

            <button class="btn-modern bottom-nav-item me-2 mx-auto" id="btn-ordini">
                <i class="bi bi-basket3"></i> 
                <span class="d-flex">Ordini</span>
            </button>
        <?php } elseif($role === 'restaurant' ){ ?>
            <button class="btn-modern bottom-nav-item me-2 mx-auto" id="btn-prenotazioni">
                <i class="bi bi-calendar-check"></i> 
                <span class="d-flex">Prenotazioni</span>
            </button>
            <button class="btn-modern bottom-nav-item me-2 mx-auto" id="btn-ordini">
                <i class="bi bi-basket3"></i> 
                <span class="d-flex">Ordini</span>
            </button>
            <button class="btn-modern bottom-nav-item me-2 mx-auto" id="btn-pagamenti">
                <i class="bi bi-credit-card-2-front"></i> 
                <span class="d-flex">Pagamenti</span>
            </button>
        <?php } elseif($role === 'chef'){ ?>
            <button class="btn-modern bottom-nav-item me-2 mx-auto" id="btn-ristorante">
                <i class="bi bi-shop"></i> 
                <span class="d-flex">Ristorante</span>
            </button>
            <button class="btn-modern bottom-nav-item me-2" id="btn-ordini">
                <i class="bi bi-basket3"></i> 
                <span class="d-flex">Ordini</span>
            </button>
        <?php } 
            // Controlla se esiste l'array $btns passato tramite get_template_part
            if (isset($btns) && !empty($btns) && is_array($btns) && $btns != null) {
                foreach ($btns as $btn) {
                    ?>
                    <button class="btn-modern bottom-nav-item me-2" id="<?= htmlspecialchars($btn['id']) ?>" 
                            onclick="window.location.href='<?= htmlspecialchars($btn['link']) ?>'">
                        <i class="<?= htmlspecialchars($btn['icon']) ?>"></i>
                        <span><?= htmlspecialchars($btn['label']) ?></span>
                    </button>
                    <?php
                }
            }else{?>
                <button class="btn-modern bottom-nav-item me-2" id="btn-profile">
                    <i class="bi bi-person-circle"></i> 
                    <span class="d-flex">Profilo</span>
                </button>
            <?php } ?>

        

        <button id="btn-logout" class="btn-modern bottom-nav-item me-2">
            <i class="bi bi-box-arrow-right"></i> 
            <span class="d-flex">Esci</span>
        </button>
    <?php } else { ?>
        <button id="btn-login" class="btn-modern bottom-nav-item me-2">
            <i class="bi bi-box-arrow-in-right"></i> 
            <span class="d-flex">Accedi</span>
        </button>

        <button id="btn-register" class="btn-modern bottom-nav-item me-2">
            <i class="bi bi-person-add"></i>
            <span class="d-flex">Registrati</span>
        </button>
    <?php } ?>
</nav>
