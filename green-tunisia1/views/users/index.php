<?php
$pageTitle = 'Gestion des utilisateurs';
$activePage = 'users';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/../layout/navbar.php'; ?>
<main class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="page-title">Utilisateurs</h1>
        <a class="btn btn-success" href="/?page=user_create"><i class="bi bi-person-plus me-1"></i>Ajouter</a>
    </div>
    <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($message = flash('error')): ?><div class="alert alert-danger"><?= e($message) ?></div><?php endif; ?>
    <div class="card mb-3"><div class="card-body">
        <form class="row g-2" method="get">
            <input type="hidden" name="page" value="users">
            <div class="col-md-6"><input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Nom ou email"></div>
            <div class="col-md-4"><select class="form-select" name="role"><option value="0">Tous les rôles</option><?php foreach ($roles as $role): ?><option value="<?= (int) $role['id_role'] ?>" <?= $roleId === (int) $role['id_role'] ? 'selected' : '' ?>><?= e($role['nom_role']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><button class="btn btn-outline-success w-100">Filtrer</button></div>
        </form>
    </div></div>
    <div class="card"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= e($user['nom']) ?></td><td><?= e($user['email']) ?></td>
                <td><span class="badge bg-success"><?= e($user['nom_role']) ?></span></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="/?page=user_show&id=<?= (int) $user['id_user'] ?>"><i class="bi bi-eye"></i></a>
                    <a class="btn btn-sm btn-outline-primary" href="/?page=user_edit&id=<?= (int) $user['id_user'] ?>"><i class="bi bi-pencil"></i></a>
                    <form method="post" action="/?page=users" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="id" value="<?= (int) $user['id_user'] ?>">
                        <button class="btn btn-sm btn-outline-danger" data-confirm="Supprimer cet utilisateur ?"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$users): ?><tr><td colspan="4" class="text-center text-muted py-4">Aucun utilisateur trouvé</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div></div>
</main>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
