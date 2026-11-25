<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
    <h1 class="h5 fw-bold mb-0"><i class="bi bi-calendar-check text-success me-2"></i>Mes participations</h1>
    <span class="badge bg-success-subtle text-success"><?= count($participations) ?> inscription(s)</span>
  </div>
  <div class="card-body p-0">
    <?php if (!$participations): ?>
      <div class="p-4 text-center text-muted">
        Vous n'êtes inscrit à aucune campagne pour le moment. Choisissez-en une ci-dessous.
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Campagne</th>
              <th>Lieu</th>
              <th>Date de la campagne</th>
              <th>Inscrit le</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($participations as $p): ?>
            <tr>
              <td class="fw-semibold"><?= e($p['titre']) ?></td>
              <td><i class="bi bi-geo-alt text-muted me-1"></i><?= e($p['lieu']) ?></td>
              <td><?= e(date('d/m/Y', strtotime($p['date_campagne']))) ?></td>
              <td class="text-muted small"><?= e(date('d/m/Y', strtotime($p['date_inscription']))) ?></td>
              <td class="text-end">
                <?php if ($p['date_campagne'] >= date('Y-m-d')): ?>
                  <form method="post" action="<?= url('participation', 'annuler') ?>" class="d-inline" onsubmit="return confirm('Annuler cette participation ?');">
                    <input type="hidden" name="id_participation" value="<?= (int) $p['id_participation'] ?>">
                    <button class="btn btn-outline-danger btn-sm" type="submit">Annuler</button>
                  </form>
                <?php else: ?>
                  <span class="badge bg-secondary-subtle text-secondary">Terminée</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-bottom py-3">
    <h2 class="h5 fw-bold mb-0"><i class="bi bi-megaphone text-success me-2"></i>Campagnes ouvertes aux inscriptions</h2>
  </div>
  <div class="card-body p-0">
    <?php if (!$disponibles): ?>
      <div class="p-4 text-center text-muted">Aucune nouvelle campagne n'est ouverte pour le moment.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Campagne</th>
              <th>Lieu</th>
              <th>Date</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($disponibles as $c): ?>
            <tr>
              <td class="fw-semibold"><?= e($c['titre']) ?></td>
              <td><i class="bi bi-geo-alt text-muted me-1"></i><?= e($c['lieu']) ?></td>
              <td><?= e(date('d/m/Y', strtotime($c['date']))) ?></td>
              <td class="text-end">
                <form method="post" action="<?= url('participation', 'inscrire') ?>" class="d-inline">
                  <input type="hidden" name="id_campagne" value="<?= (int) $c['id_campagne'] ?>">
                  <button class="btn btn-success btn-sm" type="submit">
                    <i class="bi bi-plus-circle me-1"></i> S'inscrire
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>