<?php
$editing = isset($user);
$pageTitle = $editing ? 'Modifier un utilisateur' : 'Ajouter un utilisateur';
$activePage = 'users';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/../layout/navbar.php'; ?>
<main class="content-wrapper">
    <div class="page-header"><h1 class="page-title"><?= $editing ? 'Modifier l’utilisateur' : 'Ajouter un utilisateur' ?></h1></div>
    <?php if ($message = flash('error')): ?><div class="alert alert-danger"><?= e($message) ?></div><?php endif; ?>
    <div class="card"><div class="card-body">
        <form method="post" action="/?page=<?= $editing ? 'user_edit' : 'user_create' ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $user['id_user'] ?>"><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nom</label><input class="form-control" name="nom" value="<?= e($user['nom'] ?? '') ?>" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required></div>
                <div class="col-md-6"><label class="form-label"><?= $editing ? 'Nouveau mot de passe' : 'Mot de passe' ?></label><input class="form-control" type="password" name="mot_de_passe" minlength="8" <?= $editing ? '' : 'required' ?> placeholder="<?= $editing ? 'Laisser vide pour ne pas changer' : '8 caractères minimum' ?>"></div>
                <div class="col-md-6"><label class="form-label">Rôle</label><select class="form-select" name="id_role" required><option value="">Sélectionner</option><?php foreach ($roles as $role): ?><option value="<?= (int) $role['id_role'] ?>" <?= isset($user) && (int) $user['id_role'] === (int) $role['id_role'] ? 'selected' : '' ?>><?= e($role['nom_role']) ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="mt-4"><button class="btn btn-success">Enregistrer</button><a class="btn btn-link" href="/?page=users">Annuler</a></div>
        </form>
    </div></div>
</main>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
