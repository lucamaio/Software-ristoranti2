<?php 
    include_once '../../includes/functions.php';

    session_start(); 
    if(!isset($_SESSION['user_id'])){
        message("Devi accedere per poter visualizzare questa pagina");
    }

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $role = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menù Ristorante</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../../css/index.css" rel ="stylesheet" />
    <link href="../../css/menu.css" rel ="stylesheet" />
    <link href="../../css/global.css"rel="stylesheet">
    <script>
        window.APP_CONFIG = {
            ID_ristorante : <?= $id; ?>,
            role : <?php echo json_encode($role); ?>
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/ristoranti/menu.js"></script>
    <script src="../../js/navbar.js" ></script>
</head>

<body>

<div class="container-fluid">
    <div id="menu-data"></div>
</div>

<?php 
$btns[] = [
    'icon' => 'bi bi-shop-window',
    'id'   => 'btn-show-ristorante',
    'label'=> 'ristornate',
    'link' => 'mostra.php?id=' . urlencode($id)
];

get_template_part('navbar', ['btns' => $btns]); ?>

<?php get_template_part('footer'); ?>

</body>
</html>
