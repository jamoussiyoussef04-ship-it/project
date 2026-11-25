<?php
$pageTitle = 'Détails Campagne - ' . ($campagne['titre'] ?? '');
$activeMenu = 'campagnes';
require __DIR__ . '/../partials/header.php';

$taux = (int) $campagne['capacite_max'] > 0
    ? (int) min(100, round(((int) $campagne['nb_participants'] / (int) $campagne['capacite_max']) * 100))
    : 0;
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= e($campagne['titre']) ?></h1>
    <p class="page-subtitle">
      <i class="bi bi-geo-alt me-1"></i><?= e($campagne['lieu']) ?> &bull;
      <i class="bi bi-calendar-event ms-2 me-1"></i><?= e(formatDateFr((string) $campagne['date'])) ?>
    </p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="index.php?space=back&controller=campagne&action=edit&id=<?= (int) $campagne['id_campagne'] ?>"
       class="btn-custom btn-custom-outline-primary">
      <i class="bi bi-pencil"></i> Modifier
    </a>
    <a href="index.php?space=back&controller=participation&action=exportCsv&id_campagne=<?= (int) $campagne['id_campagne'] ?>"
       class="btn-custom btn-custom-secondary">
      <i class="bi bi-file-earmark-arrow-down"></i> Exporter CSV
    </a>
    <a href="index.php?space=back&controller=campagne&action=index" class="btn-custom btn-custom-light">
      <i class="bi bi-arrow-left"></i> Retour
    </a>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12 col-lg-8">
    <div class="card border-light shadow-sm p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="badge-type-campagne badge-type-nettoyage"><?= e($campagne['type_campagne']) ?></span>
        <span class="badge-table <?= getCampagneBadgeClass((string) $campagne['statut']) ?>"><?= e($campagne['statut']) ?></span>
      </div>
      <h5 class="card-title mb-2">Objectif de la campagne</h5>
      <p class="fw-semibold text-main mb-3"><?= e($campagne['objectif']) ?></p>

      <h5 class="card-title mb-2">Description</h5>
      <p class="text-muted-green mb-4"><?= nl2br(e($campagne['description'])) ?></p>

      <div class="progress-container mb-0">
        <div class="progress-label-row">
          <span class="progress-label">Mobilisation des bénévoles</span>
          <span class="progress-value">
            <?= (int) $campagne['nb_participants'] ?> / <?= (int) $campagne['capacite_max'] ?> inscrits
            (<?= (int) $campagne['places_restantes'] ?> places restantes)
          </span>
        </div>
        <div class="progress">
          <div class="progress-bar bg-lime-accent w-progress-bar" data-progress="<?= $taux ?>"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card border-light shadow-sm p-4 h-100">
      <h5 class="card-title mb-3">Inscrire un bénévole</h5>
      <form method="POST"
            action="index.php?space=back&controller=participation&action=store"
            data-validate="participation"
            novalidate>
        <input type="hidden" name="id_campagne" value="<?= (int) $campagne['id_campagne'] ?>">
        <input type="hidden" name="mode_benevole" value="existing">
        <input type="hidden" name="redirect_to" value="campagne_show">

        <div class="mb-3">
          <label for="id_benevole" class="form-label-custom">Bénévole *</label>
          <select id="id_benevole"
                  name="id_benevole"
                  class="form-select-custom <?= isset($errors['id_benevole']) ? 'is-invalid-custom' : '' ?>"
                  required>
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

        <div class="mb-3">
          <label for="statut_part" class="form-label-custom">Statut initial</label>
          <select id="statut_part" name="statut" class="form-select-custom">
            <option value="Confirmee" <?= (($old['statut'] ?? 'Confirmee') === 'Confirmee') ? 'selected' : '' ?>>Confirmée</option>
            <option value="En attente" <?= (($old['statut'] ?? '') === 'En attente') ? 'selected' : '' ?>>En attente</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="commentaire" class="form-label-custom">Note / Rôle terrain</label>
          <input type="text" id="commentaire" name="commentaire"
                 class="form-control-custom <?= isset($errors['commentaire']) ? 'is-invalid-custom' : '' ?>"
                 value="<?= e($old['commentaire'] ?? '') ?>"
                 maxlength="255" placeholder="Ex : Équipe logistique...">
          <div class="form-feedback-custom invalid-custom" id="error-commentaire">
            <?= isset($errors['commentaire']) ? '<i class="bi bi-exclamation-circle-fill"></i> ' . e($errors['commentaire']) : '' ?>
          </div>
        </div>

        <button type="submit" class="btn-custom btn-custom-primary w-100">
          <i class="bi bi-person-plus"></i> Enregistrer l'inscription
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Liste des inscriptions jointes -->
<div class="table-card-custom">
  <div class="table-header-control">
    <h5 class="card-title mb-0">Liste des bénévoles inscrits (<?= count($participations) ?>)</h5>
    <a href="index.php?space=front&controller=campagne&action=show&id=<?= (int) $campagne['id_campagne'] ?>"
       class="btn-table-action">
      <i class="bi bi-globe2"></i> Voir fiche Front Office
    </a>
  </div>

  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>ID</th>
          <th>Bénévole</th>
          <th>Téléphone</th>
          <th>Date d'inscription</th>
          <th>Commentaire</th>
          <th>Statut</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($participations)): ?>
          <tr>
            <td colspan="7" class="text-center py-4 text-muted-green">Aucun bénévole inscrit pour le moment.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($participations as $p): ?>
            <tr>
              <td class="table-order-id">#PRT-<?= (int) $p['id_participation'] ?></td>
              <td>
                <div class="table-user-name"><?= e($p['benevole_prenom'] . ' ' . $p['benevole_nom']) ?></div>
                <div class="table-user-sub"><?= e($p['benevole_email']) ?></div>
              </td>
              <td><?= e($p['benevole_telephone'] ?: '-') ?></td>
              <td><?= e(formatDateFr((string) $p['date_inscription'])) ?></td>
              <td class="table-cell-wrap"><?= e($p['commentaire'] ?: '-') ?></td>
              <td>
                <span class="badge-table <?= getParticipationBadgeClass((string) $p['statut']) ?>">
                  <?= e($p['statut']) ?>
                </span>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-1">
                  <?php if ($p['statut'] !== 'Confirmee'): ?>
                    <form method="POST" action="index.php?space=back&controller=participation&action=updateStatut&id=<?= (int) $p['id_participation'] ?>" class="d-inline">
                      <input type="hidden" name="statut" value="Confirmee">
                      <input type="hidden" name="redirect_to" value="campagne_show">
                      <button type="submit" class="table-btn-action" title="Valider"><i class="bi bi-check-lg"></i></button>
                    </form>
                  <?php endif; ?>
                  <?php if ($p['statut'] !== 'Annulee'): ?>
                    <form method="POST" action="index.php?space=back&controller=participation&action=updateStatut&id=<?= (int) $p['id_participation'] ?>" class="d-inline">
                      <input type="hidden" name="statut" value="Annulee">
                      <input type="hidden" name="redirect_to" value="campagne_show">
                      <button type="submit" class="table-btn-action" title="Annuler"><i class="bi bi-x-lg"></i></button>
                    </form>
                  <?php endif; ?>
                  <form method="POST"
                        action="index.php?space=back&controller=participation&action=delete&id=<?= (int) $p['id_participation'] ?>"
                        class="d-inline js-confirm-form"
                        data-confirm-message="Supprimer définitivement cette participation ?">
                    <input type="hidden" name="redirect_to" value="campagne_show">
                    <button type="submit" class="table-btn-action delete" title="Supprimer"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
