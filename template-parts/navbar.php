<nav class="bottom-nav">
    <button class="btn-modern bottom-nav-item me-2" id="btn-homepage">
        <i class="bi bi-house"></i>
        <span>Home</span>
    </button>

    <?php if(isset($_SESSION['user_id'])){ 
        $role = $_SESSION['role'] ?? null;

        if($role === 'client' ){ ?>
            <button class="btn-modern bottom-nav-item me-2" id="btn-prenotazioni">
                <i class="bi bi-calendar-check"></i> 
                <span>Prenotazioni</span>
            </button>

            <button class="btn-modern bottom-nav-item me-2" id="btn-carrello">
                <i class="bi bi-receipt"></i>
                <span>Carrelli</span>
            </button>

            <button class="btn-modern bottom-nav-item me-2" id="btn-ordini">
                <i class="bi bi-basket3"></i> 
                <span>Ordini</span>
            </button>
        <?php } elseif($role === 'restaurant' ){ ?>
            <button class="btn-modern bottom-nav-item me-2" id="btn-prenotazioni">
                <i class="bi bi-calendar-check"></i> 
                <span>Prenotazioni</span>
            </button>
            <button class="btn-modern bottom-nav-item me-2" id="btn-ordini">
                <i class="bi bi-basket3"></i> 
                <span>Ordini</span>
            </button>
            <button class="btn-modern bottom-nav-item me-2" id="btn-pagamenti">
                <i class="bi bi-credit-card-2-front"></i> 
                <span>Pagamenti</span>
            </button>
        <?php } elseif($role === 'chef'){ ?>
            <button class="btn-modern bottom-nav-item me-2" id="btn-ristorante">
                <i class="bi bi-shop"></i> 
                <span>Ristorante</span>
            </button>
            <button class="btn-modern bottom-nav-item me-2" id="btn-ordini">
                <i class="bi bi-basket3"></i> 
                <span>Ordini</span>
            </button>
        <?php } ?>

        <?php 
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
            }
        ?>

        <button class="btn-modern bottom-nav-item me-2" id="btn-profile">
            <i class="bi bi-person-circle"></i> 
            <span>Profilo</span>
        </button>

        <button id="btn-logout" class="btn-modern bottom-nav-item">
            <i class="bi bi-box-arrow-right"></i> 
            <span>Esci</span>
        </button>
    <?php } else { ?>
        <button id="btn-login" class="btn-modern bottom-nav-item">
            <i class="bi bi-box-arrow-in-right"></i> 
            <span>Accedi</span>
        </button>
    <?php } ?>
</nav>
