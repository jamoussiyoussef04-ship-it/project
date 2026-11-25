<?php
$pageTitle = 'Gestion des Participations';
$activeMenu = 'participations';
require __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Inscriptions &amp; Participations</h1>
    <p class="page-subtitle">Consultez, validez ou exportez les inscriptions des bénévoles (jointure Campagne × Participation × Bénévole).</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="index.php?space=back&controller=participation&action=exportCsv&id_campagne=<?= e($_GET['id_campagne'] ?? '') ?>&statut=<?= e($_GET['statut'] ?? '') ?>&search=<?= e($_GET['search'] ?? '') ?>"
       class="btn-custom btn-custom-secondary">
      <i class="bi bi-file-earmark-arrow-down"></i> Exporter CSV
    </a>
    <a href="index.php?space=back&controller=campagne&action=index" class="btn-custom btn-custom-light">
      <i class="bi bi-tree"></i> Retour aux campagnes
    </a>
  </div>
</div>

<!-- Formulaire d'ajout manuel en Back Office -->
<div class="card border-light shadow-sm p-4 mb-4">
  <h5 class="card-title mb-3">Enregistrer une nouvelle participation</h5>
  <form method="POST"
        action="index.php?space=back&controller=participation&action=store"
        data-validate="participation"
        novalidate>
    <input type="hidden" name="mode_benevole" value="existing">
    <div class="row g-3 align-items-end">
      <div class="col-12 col-md-4">
        <label for="id_campagne" class="form-label-custom">Campagne *</label>
        <select id="id_campagne"
                name="id_campagne"
                class="form-select-custom <?= isset($errors['id_campagne']) ? 'is-invalid-custom' : '' ?>"
                required>
          <option value="">Choisir une campagne...</option>
          <?php foreach ($campagnes as $c): ?>
            <option value="<?= (int) $c['id_campagne'] ?>" <?= ((string) ($old['id_campagne'] ?? '') === (string) $c['id_campagne']) ? 'selected' : '' ?>>
              <?= e($c['titre']) ?> (<?= e(formatDateFr((string) $c['date'])) ?> — <?= (int) $c['nb_participants'] ?>/<?= (int) $c['capacite_max'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-feedback-custom invalid-custom" id="error-id_campagne">
          <?= isset($errors['id_campagne']) ? e($errors['id_campagne']) : '' ?>
        </div>
      </div>

      <div class="col-12 col-md-3">
        <label for="id_benevole" class="form-label-custom">Bénévole *</label>
        <select id="id_benevole"
                name="id_benevole"
                class="form-select-custom <?= isset($errors['id_benevole']) ? 'is-invalid-custom' : '' ?>"
                required>
          <option value="">Choisir un bénévole...</option>
          <?php foreach ($benevoles as $b): ?>
            <option value="<?= (int) $b['id_benevole'] ?>" <?= ((string) ($old['id_benevole'] ?? '') === (string) $b['id_benevole']) ? 'selected' : '' ?>>
              <?= e($b['prenom'] . ' ' . $b['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-feedback-custom invalid-custom" id="error-id_benevole">
          <?= isset($errors['id_benevole']) ? e($errors['id_benevole']) : '' ?>
        </div>
      </div>

      <div class="col-12 col-md-2">
        <label for="statut_back" class="form-label-custom">Statut</label>
        <select id="statut_back" name="statut" class="form-select-custom">
          <option value="Confirmee" <?= (($old['statut'] ?? 'Confirmee') === 'Confirmee') ? 'selected' : '' ?>>Confirmée</option>
          <option value="En attente" <?= (($old['statut'] ?? '') === 'En attente') ? 'selected' : '' ?>>En attente</option>
        </select>
      </div>

      <div class="col-12 col-md-3">
        <label for="commentaire" class="form-label-custom">Commentaire</label>
        <div class="d-flex gap-2">
          <input type="text" id="commentaire" name="commentaire" class="form-control-custom"
                 value="<?= e($old['commentaire'] ?? '') ?>"
                 maxlength="255" placeholder="Note optionnelle">
          <button type="submit" class="btn-custom btn-custom-primary">
            <i class="bi bi-plus-lg"></i>
          </button>
        </div>
        <div class="form-feedback-custom invalid-custom" id="error-commentaire">
          <?= isset($errors['commentaire']) ? e($errors['commentaire']) : '' ?>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- Tableau des participations -->
<div class="table-card-custom">
  <form method="GET" action="index.php" class="table-header-control">
    <input type="hidden" name="space" value="back">
    <input type="hidden" name="controller" value="participation">
    <input type="hidden" name="action" value="index">

    <div class="table-search-box">
      <i class="bi bi-search table-search-icon"></i>
      <input type="text" class="table-search-input" name="search"
             value="<?= e($_GET['search'] ?? '') ?>"
             placeholder="Rechercher bénévole, email ou campagne...">
    </div>

    <div class="table-filter-group flex-wrap">
      <select name="id_campagne" class="form-select-custom w-auto" aria-label="Filtrer par campagne">
        <option value="">Toutes les campagnes</option>
        <?php foreach ($campagnes as $c): ?>
          <option value="<?= (int) $c['id_campagne'] ?>" <?= ((string) ($_GET['id_campagne'] ?? '') === (string) $c['id_campagne']) ? 'selected' : '' ?>>
            <?= e($c['titre']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="statut" class="form-select-custom w-auto" aria-label="Filtrer par statut">
        <option value="">Tous les statuts</option>
        <?php foreach (Participation::STATUTS_VALIDES as $s): ?>
          <option value="<?= e($s) ?>" <?= (($_GET['statut'] ?? '') === $s) ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn-table-action">
        <i class="bi bi-funnel"></i> Filtrer
      </button>
      <a href="index.php?space=back&controller=participation&action=index" class="btn-table-action">
        <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
      </a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>ID</th>
          <th>Bénévole</th>
          <th>Campagne</th>
          <th>Date campagne</th>
          <th>Date inscription</th>
          <th>Commentaire</th>
          <th>Statut</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($participations)): ?>
          <tr>
            <td colspan="8" class="text-center py-5 text-muted-green">
              <i class="bi bi-people fs-2 d-block mb-2"></i>
              Aucune inscription trouvée.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($participations as $p): ?>
            <tr>
              <td class="table-order-id">#PRT-<?= (int) $p['id_participation'] ?></td>
              <td>
                <div class="table-user-name"><?= e($p['benevole_prenom'] . ' ' . $p['benevole_nom']) ?></div>
                <div class="table-user-sub"><?= e($p['benevole_email']) ?></div>
              </td>
              <td class="table-cell-wrap">
                <a href="index.php?space=back&controller=campagne&action=show&id=<?= (int) $p['id_campagne'] ?>"
                   class="fw-bold text-main">
                  <?= e($p['campagne_titre']) ?>
                </a>
                <div class="table-user-sub"><?= e($p['campagne_lieu']) ?></div>
              </td>
              <td><?= e(formatDateFr((string) $p['campagne_date'])) ?></td>
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
                      <button type="submit" class="table-btn-action" title="Valider l'inscription">
                        <i class="bi bi-check-lg"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                  <?php if ($p['statut'] !== 'Annulee'): ?>
                    <form method="POST" action="index.php?space=back&controller=participation&action=updateStatut&id=<?= (int) $p['id_participation'] ?>" class="d-inline">
                      <input type="hidden" name="statut" value="Annulee">
                      <button type="submit" class="table-btn-action" title="Annuler l'inscription">
                        <i class="bi bi-x-lg"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                  <form method="POST"
                        action="index.php?space=back&controller=participation&action=delete&id=<?= (int) $p['id_participation'] ?>"
                        class="d-inline js-confirm-form"
                        data-confirm-message="Supprimer définitivement l'inscription #<?= (int) $p['id_participation'] ?> ?">
                    <button type="submit" class="table-btn-action delete" title="Supprimer">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="table-footer-control">
    <span class="table-pagination-info">Total : <?= count($participations) ?> inscription(s) affichée(s)</span>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
