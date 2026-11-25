<h1>Dashboard</h1>

<div class="cards">
    <div class="card stat"><strong><?= $nbArticles ?></strong><span>Articles</span></div>
    <div class="card stat"><strong><?= $nbCategories ?></strong><span>Catégories</span></div>
</div>

<div class="actions">
    <a class="btn" href="<?= e(url('article_add')) ?>">+ Ajouter un article</a>
    <a class="btn btn-light" href="<?= e(url('categories')) ?>">Gérer les catégories</a>
</div>

<section class="card">
    <h2>Derniers articles</h2>
    <?php if (!$recent): ?>
        <p class="muted">Aucun article pour le moment.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Titre</th><th>Catégorie</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($recent as $a): ?>
            <tr>
                <td><?= e($a['titre']) ?></td>
                <td><?= e($a['nom_categorie']) ?></td>
                <td><?= e(fdate($a['date_publication'])) ?></td>
                <td><a href="<?= e(url('article_edit', ['id' => $a['id_article']])) ?>">Modifier</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Articles par catégorie</h2>
    <?php if (!$categories): ?>
        <p class="muted">Aucune catégorie.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Catégorie</th><th>Nombre d'articles</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
            <tr>
                <td><a href="<?= e(url('articles', ['categorie' => $c['id_categorie']])) ?>"><?= e($c['nom']) ?></a></td>
                <td><?= (int) $counts[$c['id_categorie']] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>
