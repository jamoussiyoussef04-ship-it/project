<?php
// Formulaire partagé par add_article et edit_article.
// Variables attendues : $old, $errors, $categories, $formAction, $submitLabel, $currentImage
$val = fn(string $k) => $old[$k] ?? '';
?>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($formAction) ?>" enctype="multipart/form-data" data-validate="article" novalidate class="card form">
    <?= csrf_field() ?>

    <div class="field">
        <label for="titre">Titre *</label>
        <input type="text" id="titre" name="titre" maxlength="255" value="<?= e($val('titre')) ?>">
        <small class="field-error" data-for="titre"></small>
    </div>

    <div class="field">
        <label for="id_categorie">Catégorie *</label>
        <select id="id_categorie" name="id_categorie">
            <option value="">— Choisir —</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id_categorie'] ?>"
                    <?= (int) $val('id_categorie') === (int) $c['id_categorie'] ? 'selected' : '' ?>>
                    <?= e($c['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="field-error" data-for="id_categorie"></small>
    </div>

    <div class="field">
        <label for="date_publication">Date de publication *</label>
        <input type="date" id="date_publication" name="date_publication" value="<?= e(substr((string) $val('date_publication'), 0, 10)) ?>">
        <small class="field-error" data-for="date_publication"></small>
    </div>

    <div class="field">
        <label for="contenu">Contenu *</label>
        <textarea id="contenu" name="contenu" rows="8"><?= e($val('contenu')) ?></textarea>
        <small class="field-error" data-for="contenu"></small>
    </div>

    <div class="field">
        <label for="image">Image (JPG, PNG, WEBP ou GIF, 2 Mo max)</label>
        <?php if ($currentImage): ?>
            <div><img class="preview" src="../public/images/<?= e($currentImage) ?>" alt="Image actuelle"></div>
            <small class="muted">Choisir un nouveau fichier remplace l'image actuelle.</small>
        <?php endif; ?>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
        <small class="field-error" data-for="image"></small>
    </div>

    <div class="actions">
        <button type="submit" class="btn"><?= e($submitLabel) ?></button>
        <a class="btn btn-light" href="<?= e(url('articles')) ?>">Annuler</a>
    </div>
</form>
