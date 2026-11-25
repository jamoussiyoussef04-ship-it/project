<div class="page-head">
    <h1>Articles</h1>
    <a class="btn" href="<?= e(url('article_add')) ?>">+ Ajouter un article</a>
</div>

<form method="get" class="filter">
    <input type="hidden" name="page" value="articles">
    <label for="categorie">Catégorie :</label>
    <select name="categorie" id="categorie" onchange="this.form.submit()">
        <option value="">Toutes</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= (int) $c['id_categorie'] ?>" <?= $selected === (int) $c['id_categorie'] ? 'selected' : '' ?>>
                <?= e($c['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <noscript><button class="btn btn-light">Filtrer</button></noscript>
</form>

<section class="card">
<?php if (!$articles): ?>
    <p class="muted">Aucun article à afficher.</p>
<?php else: ?>
    <table>
        <thead><tr><th>Image</th><th>Titre</th><th>Catégorie</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($articles as $a): ?>
            <tr>
                <td>
                    <?php if ($a['image']): ?>
                        <img class="thumb" src="../public/images/<?= e($a['image']) ?>" alt="">
                    <?php else: ?>
                        <span class="muted">—</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= e($a['titre']) ?></strong><br><small class="muted"><?= e(excerpt($a['contenu'])) ?></small></td>
                <td><?= e($a['nom_categorie']) ?></td>
                <td><?= e(fdate($a['date_publication'])) ?></td>
                <td class="row-actions">
                    <a class="btn btn-small btn-light" href="<?= e(url('article_edit', ['id' => $a['id_article']])) ?>">Modifier</a>
                    <form method="post" action="<?= e(url('articles')) ?>"
                          onsubmit="return confirm('Supprimer cet article ?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $a['id_article'] ?>">
                        <button class="btn btn-small btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</section>
