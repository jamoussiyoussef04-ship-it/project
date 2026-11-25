<?php
$pageTitle = 'Gestion des Campagnes Écologiques';
$activeMenu = 'campagnes';
require __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Campagnes Écologiques</h1>
    <p class="page-subtitle">Organisez les campagnes de nettoyage, plantation et sensibilisation et suivez les participations.</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="index.php?space=back&controller=participation&action=index" class="btn-custom btn-custom-light">
      <i class="bi bi-people"></i> Voir les inscriptions
    </a>
    <a href="index.php?space=back&controller=campagne&action=create" class="btn-custom btn-custom-primary">
      <i class="bi bi-plus-lg"></i> Nouvelle campagne
    </a>
  </div>
</div>

<!-- Cartes statistiques fidèles au dashboard Spark Admin -->
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card alert-green-card">
      <div class="position-relative z-index-2">
        <span class="alert-green-badge">Green Tunisia</span>
        <div class="alert-green-date"><?= date('d/m/Y') ?></div>
        <div class="alert-green-text">
          <?= (int) $stats['campagnes_actives'] ?> campagne(s) à venir ou en cours sur <?= (int) $stats['total_campagnes'] ?>
        </div>
      </div>
      <a href="index.php?space=back&controller=participation&action=index" class="alert-green-link z-index-2">
        <span>Gérer les participations</span>
        <i class="bi bi-arrow-right"></i>
      </a>
      <svg class="alert-green-bg-shape" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g transform="translate(50,50)">
          <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" />
          <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" transform="rotate(60)" />
          <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" transform="rotate(120)" />
        </g>
      </svg>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-stat d-flex flex-column justify-content-between">
      <div>
        <div class="card-header">
          <span class="stat-label">Inscriptions actives</span>
          <span class="stat-icon-box bg-forest-light text-lime"><i class="bi bi-person-check-fill"></i></span>
        </div>
        <div class="stat-value"><?= (int) $stats['total_participations'] ?></div>
        <div class="trend-badge trend-up">
          <i class="bi bi-check2-circle"></i>
          <span><?= (int) $stats['en_attente'] ?> en attente de validation</span>
        </div>
      </div>
      <div class="mt-3">
        <div class="progress-label-row">
          <span class="progress-label">Taux de mobilisation global</span>
          <span class="progress-value"><?= (int) $stats['taux_remplissage'] ?>%</span>
        </div>
        <div class="progress" role="progressbar" aria-valuenow="<?= (int) $stats['taux_remplissage'] ?>" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar bg-lime-accent w-progress-bar" data-progress="<?= (int) $stats['taux_remplissage'] ?>"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-stat d-flex flex-column justify-content-between">
      <div class="card-header mb-2">
        <span class="stat-label mb-0">Répartition par thématique</span>
      </div>
      <?php foreach ($stats['repartition'] as $typeLabel => $typeData): ?>
        <?php
          $pct = $stats['total_campagnes'] > 0
              ? (int) round(($typeData['campagnes'] / $stats['total_campagnes']) * 100)
              : 0;
        ?>
        <div class="progress-container mb-2">
          <div class="progress-label-row">
            <span class="progress-label"><?= e($typeLabel) ?> (<?= (int) $typeData['campagnes'] ?>)</span>
            <span class="progress-value"><?= (int) $typeData['participants'] ?> bénévole(s)</span>
          </div>
          <div class="progress">
            <div class="progress-bar bg-forest-medium w-progress-bar" data-progress="<?= $pct ?>"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Table Card Container fidèle à tables-basic.html -->
<div class="table-card-custom">
  <form method="GET" action="index.php" class="table-header-control">
    <input type="hidden" name="space" value="back">
    <input type="hidden" name="controller" value="campagne">
    <input type="hidden" name="action" value="index">

    <div class="table-search-box">
      <i class="bi bi-search table-search-icon"></i>
      <input type="text" class="table-search-input" name="search"
             value="<?= e($_GET['search'] ?? '') ?>"
             placeholder="Rechercher par titre, lieu ou objectif...">
    </div>

    <div class="table-filter-group flex-wrap">
      <select name="type" class="form-select-custom w-auto" aria-label="Filtrer par type">
        <option value="">Tous les types</option>
        <?php foreach (Campagne::TYPES_VALIDES as $t): ?>
          <option value="<?= e($t) ?>" <?= (($_GET['type'] ?? '') === $t) ? 'selected' : '' ?>><?= e($t) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="statut" class="form-select-custom w-auto" aria-label="Filtrer par statut">
        <option value="">Tous les statuts</option>
        <?php foreach (Campagne::STATUTS_VALIDES as $s): ?>
          <option value="<?= e($s) ?>" <?= (($_GET['statut'] ?? '') === $s) ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>

      <button class="btn-table-action" type="submit">
        <i class="bi bi-funnel"></i> Filtrer
      </button>
      <a href="index.php?space=back&controller=campagne&action=index" class="btn-table-action">
        <i class="bi bi-arrow-counterclockwise"></i> Réinitialiser
      </a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>ID</th>
          <th>Campagne</th>
          <th>Type</th>
          <th>Date</th>
          <th>Lieu</th>
          <th>Objectif</th>
          <th>Participants</th>
          <th>Statut</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($campagnes)): ?>
          <tr>
            <td colspan="9" class="text-center py-5 text-muted-green">
              <i class="bi bi-inbox fs-2 d-block mb-2"></i>
              Aucune campagne trouvée pour ces critères.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($campagnes as $c): ?>
            <?php
              $typeCss = match ($c['type_campagne']) {
                  'Plantation'      => 'badge-type-plantation',
                  'Sensibilisation' => 'badge-type-sensibilisation',
                  default           => 'badge-type-nettoyage',
              };
              $badgeStatut = getCampagneBadgeClass((string) $c['statut']);
              $isFull = (int) $c['nb_participants'] >= (int) $c['capacite_max'];
            ?>
            <tr>
              <td class="table-order-id">#CMP-<?= (int) $c['id_campagne'] ?></td>
              <td class="table-cell-wrap">
                <div class="table-user-name"><?= e($c['titre']) ?></div>
                <div class="table-user-sub"><?= e(mb_strimwidth((string) $c['description'], 0, 65, '...')) ?></div>
              </td>
              <td>
                <span class="badge-type-campagne <?= $typeCss ?>">
                  <?= e($c['type_campagne']) ?>
                </span>
              </td>
              <td><?= e(formatDateFr((string) $c['date'])) ?></td>
              <td class="table-cell-wrap"><?= e($c['lieu']) ?></td>
              <td class="table-cell-wrap"><?= e($c['objectif']) ?></td>
              <td>
                <a href="index.php?space=back&controller=campagne&action=show&id=<?= (int) $c['id_campagne'] ?>"
                   class="badge-table <?= $isFull ? 'pending' : 'success' ?>">
                  <?= (int) $c['nb_participants'] ?> / <?= (int) $c['capacite_max'] ?>
                </a>
              </td>
              <td>
                <span class="badge-table <?= $badgeStatut ?>">
                  <?= e($c['statut']) ?>
                </span>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-1">
                  <a href="index.php?space=back&controller=campagne&action=show&id=<?= (int) $c['id_campagne'] ?>"
                     class="table-btn-action" title="Détails &amp; Participants">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="index.php?space=back&controller=campagne&action=edit&id=<?= (int) $c['id_campagne'] ?>"
                     class="table-btn-action" title="Modifier">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="POST"
                        action="index.php?space=back&controller=campagne&action=delete&id=<?= (int) $c['id_campagne'] ?>"
                        class="d-inline js-confirm-form"
                        data-confirm-message="Confirmer la suppression de la campagne « <?= e($c['titre']) ?> » et de ses participations ?">
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
    <span class="table-pagination-info">Affichage de <?= count($campagnes) ?> campagne(s) enregistrée(s)</span>
    <a href="index.php?space=back&controller=campagne&action=create" class="btn-custom btn-custom-secondary btn-custom-sm">
      <i class="bi bi-plus-lg"></i> Ajouter une campagne
    </a>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
