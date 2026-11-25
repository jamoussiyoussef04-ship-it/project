<?php
$pageTitle = 'Mon profil';
$activePage = 'profil';
require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/layout/navbar.php'; ?>
<main class="content-wrapper">
    <div class="page-header"><h1 class="page-title">Mon profil</h1></div>
    <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($message = flash('error')): ?><div class="alert alert-danger"><?= e($message) ?></div><?php endif; ?>
    <div class="card"><div class="card-body">
        <form method="post" action="/?page=profil">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nom</label><input class="form-control" name="nom" value="<?= e($user['nom']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= e($user['email']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">Rôle</label><input class="form-control" value="<?= e($user['nom_role']) ?>" disabled></div>
                <div class="col-md-6"><label class="form-label">Nouveau mot de passe</label><input class="form-control" type="password" name="mot_de_passe" minlength="8" placeholder="Laisser vide pour ne pas changer"></div>
            </div>
            <button class="btn btn-success mt-4">Enregistrer</button>
        </form>
    </div></div>
</main>
</div>
<?php require __DIR__ . '/layout/footer.php'; ?>
