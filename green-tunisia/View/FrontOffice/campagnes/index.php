<?php
$pageTitle = 'Campagnes Écologiques Disponibles';
$activeFront = 'campagnes';
require __DIR__ . '/../partials/header.php';
?>

<!-- Bannière d'accueil Front Office fidèle à la palette Spark Admin -->
<section class="front-hero mb-4">
  <svg class="front-hero-shape" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
    <g transform="translate(50,50)">
      <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" />
      <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" transform="rotate(60)" />
      <rect x="-6" y="-45" width="12" height="90" rx="6" ry="6" fill="#B4F105" transform="rotate(120)" />
    </g>
  </svg>
  <div class="position-relative z-index-2">
    <span class="alert-green-badge mb-3">Mouvement Citoyen Tunisie</span>
    <h1 class="front-hero-title mb-2">Participez aux campagnes écologiques près de chez vous</h1>
    <p class="front-hero-subtitle mb-4">
      Nettoyage des plages, reboisement et ateliers de sensibilisation : consultez les campagnes ouvertes,
      vérifiez le nombre de participants en temps réel et rejoignez une action locale.
    </p>
    <div class="d-flex flex-wrap gap-3 align-items-center">
      <a href="#liste-campagnes" class="btn-custom btn-custom-secondary">
        <i class="bi bi-compass"></i> Découvrir les campagnes (<?= (int) $stats['campagnes_actives'] ?> actives)
      </a>
      <a href="index.php?space=front&controller=participation&action=mesParticipations"
         class="btn-custom btn-custom-light">
        <i class="bi bi-person-lines-fill"></i> Consulter mes inscriptions
      </a>
    </div>
  </div>
</section>

<!-- Barre de recherche & filtres -->
<section class="card border-light shadow-sm p-4 mb-4" id="liste-campagnes">
  <form method="GET" action="index.php" class="row g-3 align-items-end">
    <input type="hidden" name="space" value="front">
    <input type="hidden" name="controller" value="campagne">
    <input type="hidden" name="action" value="index">

    <div class="col-12 col-md-5">
      <label for="search-front" class="form-label-custom">Rechercher une campagne</label>
      <input type="text" id="search-front" name="search" class="form-control-custom"
             value="<?= e($_GET['search'] ?? '') ?>"
             placeholder="Titre, ville, objectif écologique...">
    </div>

    <div class="col-12 col-md-3">
      <label for="type-front" class="form-label-custom">Catégorie</label>
      <select id="type-front" name="type" class="form-select-custom">
        <option value="">Toutes les thématiques</option>
        <?php foreach (Campagne::TYPES_VALIDES as $t): ?>
          <option value="<?= e($t) ?>" <?= (($_GET['type'] ?? '') === $t) ? 'selected' : '' ?>><?= e($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12 col-md-2">
      <div class="form-switch-custom mb-2">
        <input class="form-switch-input-custom" type="checkbox" id="disponibles" name="disponibles" value="1"
               <?= (($_GET['disponibles'] ?? '') === '1') ? 'checked' : '' ?>>
        <label class="form-switch-label fw-semibold" for="disponibles">À venir uniquement</label>
      </div>
    </div>

    <div class="col-12 col-md-2 d-flex gap-2">
      <button type="submit" class="btn-custom btn-custom-primary w-100">
        <i class="bi bi-search"></i> Filtrer
      </button>
    </div>
  </form>
</section>

<!-- Grille des campagnes -->
<?php if (empty($campagnes)): ?>
  <div class="card p-5 text-center border-light shadow-sm">
    <div class="mb-3 text-lime empty-state-icon">
      <i class="bi bi-tree"></i>
    </div>
    <h3 class="mb-2">Aucune campagne disponible pour cette recherche</h3>
    <p class="text-muted-green mb-3">Modifiez vos filtres ou réinitialisez la recherche pour consulter toutes les campagnes.</p>
    <div>
      <a href="index.php?space=front&controller=campagne&action=index" class="btn-custom btn-custom-primary">
        Voir toutes les campagnes
      </a>
    </div>
  </div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach ($campagnes as $c): ?>
      <?php
        $today = date('Y-m-d');
        $isPast = $c['date'] < $today;
        $isClosed = in_array($c['statut'], ['Terminee', 'Annulee'], true);
        $isFull = (int) $c['nb_participants'] >= (int) $c['capacite_max'];
        $taux = (int) $c['capacite_max'] > 0
            ? (int) min(100, round(((int) $c['nb_participants'] / (int) $c['capacite_max']) * 100))
            : 0;
        $typeCss = match ($c['type_campagne']) {
            'Plantation'      => 'badge-type-plantation',
            'Sensibilisation' => 'badge-type-sensibilisation',
            default           => 'badge-type-nettoyage',
        };
      ?>
      <div class="col-12 col-md-6 col-lg-4">
        <article class="card campagne-card h-100 d-flex flex-column justify-content-between mb-0">
          <div>
            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
              <span class="badge-type-campagne <?= $typeCss ?>">
                <?= e($c['type_campagne']) ?>
              </span>
              <?php if ($isPast || $isClosed): ?>
                <span class="badge-table failed"><?= $isPast ? 'Passée' : e($c['statut']) ?></span>
              <?php elseif ($isFull): ?>
                <span class="badge-table pending">Complète</span>
              <?php else: ?>
                <span class="badge-table success">Ouverte</span>
              <?php endif; ?>
            </div>

            <h2 class="h5 mb-2"><?= e($c['titre']) ?></h2>
            <p class="text-muted-green mb-3"><?= e(mb_strimwidth((string) $c['description'], 0, 115, '...')) ?></p>

            <div class="campagne-meta-list mb-3">
              <div class="campagne-meta-item">
                <i class="bi bi-calendar-event-fill"></i>
                <span><?= e(formatDateFr((string) $c['date'])) ?></span>
              </div>
              <div class="campagne-meta-item">
                <i class="bi bi-geo-alt-fill"></i>
                <span><?= e($c['lieu']) ?></span>
              </div>
              <div class="campagne-meta-item">
                <i class="bi bi-bullseye"></i>
                <span><?= e($c['objectif']) ?></span>
              </div>
            </div>
          </div>

          <div>
            <div class="progress-container mb-3">
              <div class="progress-label-row">
                <span class="progress-label">
                  <i class="bi bi-people-fill me-1"></i>
                  <?= (int) $c['nb_participants'] ?> participant(s)
                </span>
                <span class="progress-value">
                  <?= (int) $c['places_restantes'] ?> place(s) restante(s)
                </span>
              </div>
              <div class="progress">
                <div class="progress-bar <?= $isFull ? 'bg-brand-orange' : 'bg-lime-accent' ?> w-progress-bar"
                     data-progress="<?= $taux ?>"></div>
              </div>
            </div>

            <a href="index.php?space=front&controller=campagne&action=show&id=<?= (int) $c['id_campagne'] ?>"
               class="btn-custom <?= ($isPast || $isClosed) ? 'btn-custom-light' : 'btn-custom-primary' ?> w-100">
              <span><?= ($isPast || $isClosed || $isFull) ? 'Voir les détails' : 'Voir détails &amp; Participer' ?></span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </article>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
