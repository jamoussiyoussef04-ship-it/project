<?php
$pageTitle = 'Détails utilisateur';
$activePage = 'users';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/../layout/navbar.php'; ?>
<main class="content-wrapper">
    <div class="page-header d-flex justify-content-between"><h1 class="page-title">Détails utilisateur</h1><a class="btn btn-outline-secondary" href="/?page=users">Retour</a></div>
    <div class="card mb-3"><div class="card-body">
        <dl class="row mb-0"><dt class="col-sm-3">Nom</dt><dd class="col-sm-9"><?= e($user['nom']) ?></dd><dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?= e($user['email']) ?></dd><dt class="col-sm-3">Rôle</dt><dd class="col-sm-9"><?= e($user['nom_role']) ?></dd></dl>
    </div></div>
    <div class="card"><div class="card-body"><h2 class="h5">Activités</h2><div class="table-responsive"><table class="table"><thead><tr><th>Action</th><th>Date</th></tr></thead><tbody><?php foreach ($activities as $activity): ?><tr><td><?= e($activity['action']) ?></td><td><?= e(date('d/m/Y H:i', strtotime($activity['date_action']))) ?></td></tr><?php endforeach; ?><?php if (!$activities): ?><tr><td colspan="2" class="text-muted text-center">Aucune activité</td></tr><?php endif; ?></tbody></table></div></div></div>
</main>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
