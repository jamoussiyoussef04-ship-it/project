<?php
$pageTitle = $isEdit ? 'Modifier une Campagne' : 'Ajouter une Campagne';
$activeMenu = $isEdit ? 'campagnes' : 'campagne_create';
$actionUrl = $isEdit
    ? 'index.php?space=back&controller=campagne&action=update&id=' . (int) $old['id_campagne']
    : 'index.php?space=back&controller=campagne&action=store';

require __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= $isEdit ? 'Modifier la campagne' : 'Nouvelle campagne écologique' ?></h1>
    <p class="page-subtitle">Remplissez tous les champs requis. Les règles de date et de capacité sont vérifiées en JavaScript et en PHP.</p>
  </div>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item">
        <a href="index.php?space=back&controller=campagne&action=index" class="text-decoration-none text-muted-green">Campagnes</a>
      </li>
      <li class="breadcrumb-item active text-main" aria-current="page">
        <?= $isEdit ? 'Modifier' : 'Ajouter' ?>
      </li>
    </ol>
  </nav>
</div>

<div class="row g-4">
  <div class="col-12 col-lg-8">
    <div class="card border-light shadow-sm p-4">
      <h5 class="card-title mb-4">Informations de la campagne</h5>

      <form method="POST" action="<?= e($actionUrl) ?>" id="campagneForm" data-validate="campagne" data-is-edit="<?= $isEdit ? '1' : '0' ?>" novalidate>
        <div class="row g-3">
          <div class="col-12 col-md-8">
            <label for="titre" class="form-label-custom">Titre de la campagne *</label>
            <input type="text"
                   id="titre"
                   name="titre"
                   class="form-control-custom <?= isset($errors['titre']) ? 'is-invalid-custom' : '' ?>"
                   value="<?= e($old['titre'] ?? '') ?>"
                   placeholder="Ex : Nettoyage Plage de La Marsa"
                   required minlength="3" maxlength="150">
            <div class="form-feedback-custom invalid-custom" id="error-titre">
              <?= isset($errors['titre']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['titre']) : '' ?>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <label for="type_campagne" class="form-label-custom">Type *</label>
            <select id="type_campagne"
                    name="type_campagne"
                    class="form-select-custom <?= isset($errors['type_campagne']) ? 'is-invalid-custom' : '' ?>"
                    required>
              <?php foreach (Campagne::TYPES_VALIDES as $type): ?>
                <option value="<?= e($type) ?>" <?= (($old['type_campagne'] ?? 'Nettoyage') === $type) ? 'selected' : '' ?>>
                  <?= e($type) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-feedback-custom invalid-custom" id="error-type_campagne">
              <?= isset($errors['type_campagne']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['type_campagne']) : '' ?>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <label for="date" class="form-label-custom">Date de la campagne *</label>
            <input type="date"
                   id="date"
                   name="date"
                   class="form-control-custom <?= isset($errors['date']) ? 'is-invalid-custom' : '' ?>"
                   value="<?= e($old['date'] ?? '') ?>"
                   required>
            <div class="form-feedback-custom invalid-custom" id="error-date">
              <?= isset($errors['date']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['date']) : '' ?>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <label for="capacite_max" class="form-label-custom">Capacité maximale *</label>
            <input type="number"
                   id="capacite_max"
                   name="capacite_max"
                   min="1" max="5000"
                   class="form-control-custom <?= isset($errors['capacite_max']) ? 'is-invalid-custom' : '' ?>"
                   value="<?= e($old['capacite_max'] ?? 30) ?>"
                   required>
            <div class="form-feedback-custom invalid-custom" id="error-capacite_max">
              <?= isset($errors['capacite_max']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['capacite_max']) : '' ?>
            </div>
          </div>

          <div class="col-12 col-md-4">
            <label for="statut" class="form-label-custom">Statut *</label>
            <select id="statut"
                    name="statut"
                    class="form-select-custom <?= isset($errors['statut']) ? 'is-invalid-custom' : '' ?>"
                    required>
              <?php foreach (Campagne::STATUTS_VALIDES as $statut): ?>
                <option value="<?= e($statut) ?>" <?= (($old['statut'] ?? 'Planifiee') === $statut) ? 'selected' : '' ?>>
                  <?= e($statut) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-feedback-custom invalid-custom" id="error-statut">
              <?= isset($errors['statut']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['statut']) : '' ?>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label for="lieu" class="form-label-custom">Lieu *</label>
            <input type="text"
                   id="lieu"
                   name="lieu"
                   class="form-control-custom <?= isset($errors['lieu']) ? 'is-invalid-custom' : '' ?>"
                   value="<?= e($old['lieu'] ?? '') ?>"
                   placeholder="Ex : Plage de La Marsa, Tunis"
                   required minlength="3" maxlength="150">
            <div class="form-feedback-custom invalid-custom" id="error-lieu">
              <?= isset($errors['lieu']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['lieu']) : '' ?>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label for="objectif" class="form-label-custom">Objectif mesurable *</label>
            <input type="text"
                   id="objectif"
                   name="objectif"
                   class="form-control-custom <?= isset($errors['objectif']) ? 'is-invalid-custom' : '' ?>"
                   value="<?= e($old['objectif'] ?? '') ?>"
                   placeholder="Ex : Collecter 400 kg de plastique"
                   required minlength="5" maxlength="255">
            <div class="form-feedback-custom invalid-custom" id="error-objectif">
              <?= isset($errors['objectif']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['objectif']) : '' ?>
            </div>
          </div>

          <div class="col-12">
            <label for="description" class="form-label-custom">Description détaillée *</label>
            <textarea id="description"
                      name="description"
                      rows="4"
                      class="form-control-custom <?= isset($errors['description']) ? 'is-invalid-custom' : '' ?>"
                      placeholder="Décrivez le programme, le matériel prévu et les consignes aux bénévoles..."
                      required minlength="10"><?= e($old['description'] ?? '') ?></textarea>
            <div class="form-feedback-custom invalid-custom" id="error-description">
              <?= isset($errors['description']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['description']) : '' ?>
            </div>
          </div>

          <div class="col-12 d-flex justify-content-end gap-2 mt-4">
            <a href="index.php?space=back&controller=campagne&action=index" class="btn-custom btn-custom-light">
              Annuler
            </a>
            <button type="submit" class="btn-custom btn-custom-primary">
              <i class="bi bi-check-lg"></i>
              <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la campagne' ?>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card border-light shadow-sm p-4">
      <h5 class="card-title mb-3">Règles métiers appliquées</h5>
      <div class="alert-custom alert-custom-primary">
        <i class="bi bi-calendar-check alert-custom-icon"></i>
        <div class="alert-custom-content">
          <strong>Vérification des dates :</strong> une campagne active (<em>Planifiée</em> ou <em>En cours</em>) ne peut pas être enregistrée avec une date passée.
        </div>
      </div>
      <div class="alert-custom alert-custom-info">
        <i class="bi bi-people-fill alert-custom-icon"></i>
        <div class="alert-custom-content">
          <strong>Capacité contrôlée :</strong> lors de la modification, la capacité ne peut pas être inférieure au nombre de participants déjà inscrits.
        </div>
      </div>
      <div class="alert-custom alert-custom-success mb-0">
        <i class="bi bi-shield-check alert-custom-icon"></i>
        <div class="alert-custom-content">
          <strong>Double validation :</strong> tous les champs sont validés côté client (JavaScript) et côté serveur (PHP + PDO).
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
