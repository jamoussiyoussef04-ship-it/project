<?php
$pageTitle = $campagne['titre'];
$activeFront = 'campagnes';
require __DIR__ . '/../partials/header.php';

$today = date('Y-m-d');
$isPast = $campagne['date'] < $today;
$isClosed = in_array($campagne['statut'], ['Terminee', 'Annulee'], true);
$isFull = (int) $campagne['nb_participants'] >= (int) $campagne['capacite_max'];
$canJoin = !$isPast && !$isClosed && !$isFull;
$taux = (int) $campagne['capacite_max'] > 0
    ? (int) min(100, round(((int) $campagne['nb_participants'] / (int) $campagne['capacite_max']) * 100))
    : 0;
$selectedMode = ($old['mode_benevole'] ?? 'existing') === 'new' ? 'new' : 'existing';
?>

<div class="mb-4">
  <a href="index.php?space=front&controller=campagne&action=index" class="btn-custom btn-custom-light btn-custom-sm">
    <i class="bi bi-arrow-left"></i> Retour aux campagnes
  </a>
</div>

<div class="row g-4">
  <!-- Détails de la campagne -->
  <div class="col-12 col-lg-7">
    <div class="card border-light shadow-sm p-4 mb-4">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <span class="badge-type-campagne badge-type-plantation"><?= e($campagne['type_campagne']) ?></span>
        <span class="badge-table <?= getCampagneBadgeClass((string) $campagne['statut']) ?>">
          <?= e($campagne['statut']) ?>
        </span>
      </div>

      <h1 class="page-title mb-3"><?= e($campagne['titre']) ?></h1>

      <div class="row g-3 mb-4">
        <div class="col-sm-6">
          <div class="transaction-item">
            <div class="transaction-icon bg-forest-light text-lime">
              <i class="bi bi-calendar4-event"></i>
            </div>
            <div class="transaction-info">
              <div class="transaction-date">Date prévue</div>
              <div class="transaction-name"><?= e(formatDateFr((string) $campagne['date'])) ?></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="transaction-item">
            <div class="transaction-icon bg-forest-light text-lime">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="transaction-info">
              <div class="transaction-date">Lieu de rassemblement</div>
              <div class="transaction-name"><?= e($campagne['lieu']) ?></div>
            </div>
          </div>
        </div>
      </div>

      <h5 class="card-title mb-2">Objectif de l'action</h5>
      <div class="alert-custom alert-custom-primary mb-4">
        <i class="bi bi-bullseye alert-custom-icon"></i>
        <div class="alert-custom-content fw-semibold"><?= e($campagne['objectif']) ?></div>
      </div>

      <h5 class="card-title mb-2">Description &amp; Programme</h5>
      <p class="text-muted-green mb-4"><?= nl2br(e($campagne['description'])) ?></p>

      <div class="progress-container mb-0">
        <div class="progress-label-row">
          <span class="progress-label">Participants inscrits : <?= (int) $campagne['nb_participants'] ?> / <?= (int) $campagne['capacite_max'] ?></span>
          <span class="progress-value"><?= (int) $campagne['places_restantes'] ?> place(s) disponible(s)</span>
        </div>
        <div class="progress">
          <div class="progress-bar <?= $isFull ? 'bg-brand-orange' : 'bg-lime-accent' ?> w-progress-bar"
               data-progress="<?= $taux ?>"></div>
        </div>
      </div>
    </div>

    <!-- Aperçu des participants (jointure SQL) -->
    <div class="card border-light shadow-sm p-4">
      <div class="card-header mb-3">
        <h5 class="card-title">Bénévoles mobilisés (<?= (int) $campagne['nb_participants'] ?>)</h5>
      </div>
      <?php
        $activeParticipations = array_filter(
            $participations,
            static fn(array $row): bool => $row['statut'] !== 'Annulee'
        );
      ?>
      <?php if (empty($activeParticipations)): ?>
        <p class="text-muted-green mb-0">Soyez le premier bénévole à rejoindre cette campagne !</p>
      <?php else: ?>
        <div class="transaction-list">
          <?php foreach ($activeParticipations as $p): ?>
            <div class="transaction-item">
              <div class="transaction-icon bg-forest-light text-lime">
                <i class="bi bi-person-fill"></i>
              </div>
              <div class="transaction-info">
                <div class="transaction-name"><?= e($p['benevole_prenom'] . ' ' . $p['benevole_nom']) ?></div>
                <div class="transaction-date">
                  Inscrit le <?= e(formatDateFr((string) $p['date_inscription'])) ?>
                  <?= !empty($p['commentaire']) ? ' • ' . e($p['commentaire']) : '' ?>
                </div>
              </div>
              <span class="badge-table <?= getParticipationBadgeClass((string) $p['statut']) ?>">
                <?= e($p['statut']) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Formulaire d'inscription Front Office -->
  <div class="col-12 col-lg-5">
    <div class="card border-light shadow-sm p-4">
      <h5 class="card-title mb-3">Rejoindre cette campagne</h5>

      <?php if ($isPast): ?>
        <div class="alert-custom alert-custom-danger mb-3">
          <i class="bi bi-calendar-x-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            Cette campagne a déjà eu lieu le <?= e(formatDateFr((string) $campagne['date'])) ?>. Les inscriptions sont fermées.
          </div>
        </div>
      <?php elseif ($isClosed): ?>
        <div class="alert-custom alert-custom-danger mb-3">
          <i class="bi bi-lock-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            Cette campagne est actuellement <strong><?= e($campagne['statut']) ?></strong>.
          </div>
        </div>
      <?php elseif ($isFull): ?>
        <div class="alert-custom alert-custom-warning mb-3">
          <i class="bi bi-people-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            Capacité maximale atteinte (<?= (int) $campagne['capacite_max'] ?>/<?= (int) $campagne['capacite_max'] ?> places).
          </div>
        </div>
      <?php else: ?>
        <div class="alert-custom alert-custom-success mb-3">
          <i class="bi bi-check-circle-fill alert-custom-icon"></i>
          <div class="alert-custom-content">
            <strong><?= (int) $campagne['places_restantes'] ?> place(s) disponible(s)</strong> — contrôle anti-doublon et conflit de date actif.
          </div>
        </div>
      <?php endif; ?>

      <form method="POST"
            action="index.php?space=front&controller=participation&action=inscrire"
            id="frontParticipationForm"
            data-validate="participation"
            novalidate>
        <input type="hidden" name="id_campagne" value="<?= (int) $campagne['id_campagne'] ?>">

        <div class="mb-3">
          <label class="form-label-custom">Profil bénévole *</label>
          <div class="form-check-custom">
            <input class="form-check-input-custom js-mode-toggle" type="radio" name="mode_benevole"
                   id="mode_existing" value="existing" <?= $selectedMode === 'existing' ? 'checked' : '' ?>>
            <label class="form-check-label" for="mode_existing">Sélectionner un bénévole existant</label>
          </div>
          <div class="form-check-custom">
            <input class="form-check-input-custom js-mode-toggle" type="radio" name="mode_benevole"
                   id="mode_new" value="new" <?= $selectedMode === 'new' ? 'checked' : '' ?>>
            <label class="form-check-label" for="mode_new">Nouveau bénévole (inscription rapide)</label>
          </div>
        </div>

        <div id="section-existing-benevole" class="<?= $selectedMode === 'new' ? 'd-none' : '' ?>">
          <div class="mb-3">
            <label for="id_benevole" class="form-label-custom">Choisir votre profil *</label>
            <select id="id_benevole"
                    name="id_benevole"
                    class="form-select-custom <?= isset($errors['id_benevole']) ? 'is-invalid-custom' : '' ?>">
              <option value="">Sélectionner un bénévole...</option>
              <?php foreach ($benevoles as $b): ?>
                <option value="<?= (int) $b['id_benevole'] ?>"
                  <?= ((string) ($old['id_benevole'] ?? '') === (string) $b['id_benevole']) ? 'selected' : '' ?>>
                  <?= e($b['prenom'] . ' ' . $b['nom']) ?> (<?= e($b['email']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-feedback-custom invalid-custom" id="error-id_benevole">
              <?= isset($errors['id_benevole']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['id_benevole']) : '' ?>
            </div>
          </div>
        </div>

        <div id="section-new-benevole" class="<?= $selectedMode === 'existing' ? 'd-none' : '' ?>">
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label for="prenom" class="form-label-custom">Prénom *</label>
              <input type="text" id="prenom" name="prenom"
                     class="form-control-custom <?= isset($errors['prenom']) ? 'is-invalid-custom' : '' ?>"
                     value="<?= e($old['prenom'] ?? '') ?>" placeholder="Ex : Lina">
              <div class="form-feedback-custom invalid-custom" id="error-prenom">
                <?= isset($errors['prenom']) ? e($errors['prenom']) : '' ?>
              </div>
            </div>
            <div class="col-6">
              <label for="nom" class="form-label-custom">Nom *</label>
              <input type="text" id="nom" name="nom"
                     class="form-control-custom <?= isset($errors['nom']) ? 'is-invalid-custom' : '' ?>"
                     value="<?= e($old['nom'] ?? '') ?>" placeholder="Ex : Mansouri">
              <div class="form-feedback-custom invalid-custom" id="error-nom">
                <?= isset($errors['nom']) ? e($errors['nom']) : '' ?>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label-custom">Adresse email *</label>
            <input type="email" id="email" name="email"
                   class="form-control-custom <?= isset($errors['email']) ? 'is-invalid-custom' : '' ?>"
                   value="<?= e($old['email'] ?? '') ?>" placeholder="nom@exemple.tn">
            <div class="form-feedback-custom invalid-custom" id="error-email">
              <?= isset($errors['email']) ? e($errors['email']) : '' ?>
            </div>
          </div>

          <div class="mb-3">
            <label for="telephone" class="form-label-custom">Téléphone</label>
            <input type="text" id="telephone" name="telephone"
                   class="form-control-custom <?= isset($errors['telephone']) ? 'is-invalid-custom' : '' ?>"
                   value="<?= e($old['telephone'] ?? '') ?>" placeholder="Ex : 22334455">
            <div class="form-feedback-custom invalid-custom" id="error-telephone">
              <?= isset($errors['telephone']) ? e($errors['telephone']) : '' ?>
            </div>
          </div>
        </div>

        <div class="mb-4">
          <label for="commentaire" class="form-label-custom">Disponibilité / Commentaire</label>
          <textarea id="commentaire" name="commentaire" rows="2" maxlength="255"
                    class="form-control-custom <?= isset($errors['commentaire']) ? 'is-invalid-custom' : '' ?>"
                    placeholder="Précisez si vous apportez du matériel ou vos disponibilités..."><?= e($old['commentaire'] ?? '') ?></textarea>
          <div class="form-feedback-custom invalid-custom" id="error-commentaire">
            <?= isset($errors['commentaire']) ? e($errors['commentaire']) : '' ?>
          </div>
        </div>

        <button type="submit"
                class="btn-custom <?= $canJoin ? 'btn-custom-primary' : 'btn-custom-light' ?> w-100"
                <?= !$canJoin ? 'disabled' : '' ?>>
          <i class="bi bi-check2-circle"></i>
          <span><?= $canJoin ? 'Confirmer mon inscription' : 'Inscriptions indisponibles' ?></span>
        </button>
      </form>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
