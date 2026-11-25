<h1>Catégories</h1>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="two-cols">
    <section class="card">
        <h2><?= $editCat ? 'Modifier la catégorie' : 'Ajouter une catégorie' ?></h2>
        <form method="post" action="<?= e($editCat ? url('categories', ['edit' => $editCat['id_categorie']]) : url('categories')) ?>"
              data-validate="categorie" novalidate class="form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="<?= $editCat ? 'update' : 'add' ?>">
            <?php if ($editCat): ?>
                <input type="hidden" name="id" value="<?= (int) $editCat['id_categorie'] ?>">
            <?php endif; ?>

            <div class="field">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" maxlength="100" value="<?= e($old['nom'] ?? '') ?>">
                <small class="field-error" data-for="nom"></small>
            </div>
            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?= e($old['description'] ?? '') ?></textarea>
            </div>
            <div class="actions">
                <button class="btn"><?= $editCat ? 'Enregistrer' : 'Ajouter' ?></button>
                <?php if ($editCat): ?>
                    <a class="btn btn-light" href="<?= e(url('categories')) ?>">Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>Liste (<?= count($categories) ?>)</h2>
        <?php if (!$categories): ?>
            <p class="muted">Aucune catégorie.</p>
        <?php else: ?>
        <table>
            <thead><tr><th>Nom</th><th>Articles</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><strong><?= e($c['nom']) ?></strong><br><small class="muted"><?= e(excerpt($c['description'], 60)) ?></small></td>
                    <td><?= (int) $counts[$c['id_categorie']] ?></td>
                    <td class="row-actions">
                        <a class="btn btn-small btn-light" href="<?= e(url('categories', ['edit' => $c['id_categorie']])) ?>">Modifier</a>
                        <form method="post" action="<?= e(url('categories')) ?>"
                              onsubmit="return confirm('Supprimer cette catégorie ?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $c['id_categorie'] ?>">
                            <button class="btn btn-small btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>
</div>
