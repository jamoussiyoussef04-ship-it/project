<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
      <h1 class="h4 fw-bold mb-0">Bénévoles</h1>
      <p class="text-muted small mb-0">Liste complète des bénévoles inscrits</p>
    </div>
    <form class="d-flex gap-2" method="get" action="index.php">
      <input type="hidden" name="controller" value="benevole">
      <input type="hidden" name="action" value="liste">
      <label for="q" class="visually-hidden">Rechercher</label>
      <input id="q" name="q" type="search" class="form-control form-control-sm" style="min-width: 240px;" placeholder="Nom, prénom ou e-mail" value="<?= e($recherche) ?>">
      <button class="btn btn-success btn-sm px-3" type="submit">
        <i class="bi bi-search me-1"></i> Rechercher
      </button>
    </form>
  </div>

  <div class="card-body p-0">
    <?php if (!$benevoles): ?>
      <div class="p-4 text-center text-muted">Aucun bénévole ne correspond à cette recherche.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nom</th>
              <th>Prénom</th>
              <th>E-mail</th>
              <th>Téléphone</th>
              <th>Participations</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($benevoles as $b): ?>
            <tr>
              <td class="fw-semibold"><?= e($b['nom']) ?></td>
              <td><?= e($b['prenom']) ?></td>
              <td><?= e($b['email']) ?></td>
              <td><?= e($b['telephone']) ?></td>
              <td>
                <a class="badge bg-success-subtle text-success text-decoration-none px-2 py-1" href="<?= url('participation', 'historique', ['id_benevole' => $b['id_benevole']]) ?>">
                  <?= (int) $b['nb_participations'] ?> campagne(s)
                </a>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <a class="btn btn-outline-primary btn-sm" href="<?= url('benevole', 'modifier', ['id' => $b['id_benevole']]) ?>">
                    <i class="bi bi-pencil"></i> Modifier
                  </a>
                  <form method="post" action="<?= url('benevole', 'supprimer') ?>" class="d-inline"
                        onsubmit="return confirm(<?= e(json_encode('Supprimer ' . $b['prenom'] . ' ' . $b['nom'] . ' et ses ' . (int) $b['nb_participations'] . ' participation(s) ?')) ?>);">
                    <input type="hidden" name="id" value="<?= (int) $b['id_benevole'] ?>">
                    <button class="btn btn-outline-danger btn-sm" type="submit">
                      <i class="bi bi-trash"></i> Supprimer
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>