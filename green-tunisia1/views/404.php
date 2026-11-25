<?php
$pageTitle = 'Page introuvable';
$activePage = '';
require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/layout/navbar.php'; ?>
<main class="content-wrapper text-center py-5">
    <h1 class="display-4">404</h1>
    <p class="lead">Page introuvable</p>
    <a href="/?page=dashboard" class="btn btn-success">Retour au tableau de bord</a>
</main>
</div>
<?php require __DIR__ . '/layout/footer.php'; ?>
