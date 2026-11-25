<?php
$pageTitle = 'Gestion des rôles';
$activePage = 'roles';
require __DIR__ . '/../layout/header.php';
require __DIR__ . '/../layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/../layout/navbar.php'; ?>
<main class="content-wrapper">
    <div class="page-header"><h1 class="page-title">Rôles</h1></div>
    <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($message = flash('error')): ?><div class="alert alert-danger"><?= e($message) ?></div><?php endif; ?>
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card"><div class="card-body">
                <h2 class="h5">Ajouter un rôle</h2>
                <form method="post" action="/?page=roles">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="mb-3"><label class="form-label">Nom</label><input class="form-control" name="nom_role" required></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                    <button class="btn btn-success">Ajouter</button>
                </form>
            </div></div>
        </div>
        <div class="col-lg-7">
            <div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Rôle</th><th>Description</th><th>Utilisateurs</th><th class="text-end">Actions</th></tr></thead><tbody>
            <?php foreach ($roles as $role): ?>
            <tr>
                <td><?= e($role['nom_role']) ?></td><td><?= e($role['description']) ?></td><td><span class="badge bg-secondary"><?= (int) $role['user_count'] ?></span></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-role-<?= (int) $role['id_role'] ?>"><i class="bi bi-pencil"></i></button>
                    <form method="post" action="/?page=roles" class="d-inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id_role" value="<?= (int) $role['id_role'] ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn btn-sm btn-outline-danger" data-confirm="Supprimer ce rôle ?"><i class="bi bi-trash"></i></button></form>
                </td>
            </tr>
            <tr class="collapse" id="edit-role-<?= (int) $role['id_role'] ?>"><td colspan="4">
                <form method="post" action="/?page=roles" class="row g-2 p-2">
                    <input type="hidden" name="action" value="update"><input type="hidden" name="id_role" value="<?= (int) $role['id_role'] ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <div class="col-md-4"><input class="form-control" name="nom_role" value="<?= e($role['nom_role']) ?>" required></div><div class="col-md-6"><input class="form-control" name="description" value="<?= e($role['description']) ?>"></div><div class="col-md-2"><button class="btn btn-primary w-100">Modifier</button></div>
                </form>
            </td></tr>
            <?php endforeach; ?>
            <?php if (!$roles): ?><tr><td colspan="4" class="text-center text-muted">Aucun rôle</td></tr><?php endif; ?>
            </tbody></table></div></div>
        </div>
    </div>
</main>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
