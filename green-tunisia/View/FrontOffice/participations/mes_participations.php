<?php
$pageTitle = 'Mes Inscriptions aux Campagnes';
$activeFront = 'participations';
require __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Suivi de mes inscriptions</h1>
    <p class="page-subtitle">Consultez les campagnes auxquelles vous êtes inscrit(e) et gérez vos désinscriptions avant la date prévue.</p>
  </div>
  <a href="index.php?space=front&controller=campagne&action=index" class="btn-custom btn-custom-primary">
    <i class="bi bi-compass"></i> Explorer les campagnes
  </a>
</div>

<div class="card border-light shadow-sm p-4 mb-4">
  <form method="GET" action="index.php" class="row g-3 align-items-end">
    <input type="hidden" name="space" value="front">
    <input type="hidden" name="controller" value="participation">
    <input type="hidden" name="action" value="mesParticipations">

    <div class="col-12 col-md-8">
      <label for="select-benevole-lookup" class="form-label-custom">Sélectionner un profil bénévole</label>
      <select id="select-benevole-lookup" name="id_benevole" class="form-select-custom">
        <?php foreach ($benevoles as $b): ?>
          <option value="<?= (int) $b['id_benevole'] ?>" <?= ((int) $selectedId === (int) $b['id_benevole']) ? 'selected' : '' ?>>
            <?= e($b['prenom'] . ' ' . $b['nom']) ?> — <?= e($b['email']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 col-md-4">
      <button type="submit" class="btn-custom btn-custom-secondary w-100">
        <i class="bi bi-person-badge"></i> Afficher mes participations
      </button>
    </div>
  </form>
</div>

<div class="table-card-custom">
  <div class="table-header-control">
    <div>
      <h5 class="card-title mb-0">
        <?= $benevole ? 'Inscriptions de ' . e($benevole['prenom'] . ' ' . $benevole['nom']) : 'Inscriptions' ?>
      </h5>
    </div>
    <span class="badge-table success"><?= count($participations) ?> participation(s)</span>
  </div>

  <div class="table-responsive">
    <table class="table-custom">
      <thead>
        <tr>
          <th>Campagne</th>
          <th>Type</th>
          <th>Date prévue</th>
          <th>Lieu</th>
          <th>Date d'inscription</th>
          <th>Statut</th>
          <th class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($participations)): ?>
          <tr>
            <td colspan="7" class="text-center py-5 text-muted-green">
              Aucune inscription enregistrée pour ce bénévole.
            </td>
          </tr>
        <?php else: ?>
          <?php $today = date('Y-m-d'); ?>
          <?php foreach ($participations as $p): ?>
            <?php $canCancel = $p['statut'] !== 'Annulee' && $p['campagne_date'] >= $today; ?>
            <tr>
              <td class="table-cell-wrap">
                <a href="index.php?space=front&controller=campagne&action=show&id=<?= (int) $p['id_campagne'] ?>"
                   class="fw-bold text-main">
                  <?= e($p['campagne_titre']) ?>
                </a>
                <div class="table-user-sub"><?= e($p['campagne_objectif']) ?></div>
              </td>
              <td><span class="badge-type-campagne badge-type-nettoyage"><?= e($p['type_campagne']) ?></span></td>
              <td><?= e(formatDateFr((string) $p['campagne_date'])) ?></td>
              <td class="table-cell-wrap"><?= e($p['campagne_lieu']) ?></td>
              <td><?= e(formatDateFr((string) $p['date_inscription'])) ?></td>
              <td>
                <span class="badge-table <?= getParticipationBadgeClass((string) $p['statut']) ?>">
                  <?= e($p['statut']) ?>
                </span>
              </td>
              <td class="text-center">
                <?php if ($canCancel): ?>
                  <form method="POST"
                        action="index.php?space=front&controller=participation&action=desinscrire&id=<?= (int) $p['id_participation'] ?>"
                        class="d-inline js-confirm-form"
                        data-confirm-message="Voulez-vous vraiment annuler votre participation à « <?= e($p['campagne_titre']) ?> » ?">
                    <button type="submit" class="btn-custom btn-custom-outline-danger btn-custom-sm">
                      <i class="bi bi-x-circle"></i> Se désinscrire
                    </button>
                  </form>
                <?php else: ?>
                  <span class="text-muted-green fs-xs">Non modifiable</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
