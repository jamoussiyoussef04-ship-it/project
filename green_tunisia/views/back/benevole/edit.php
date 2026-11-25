<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h1 class="h4 fw-bold mb-0">Modifier le bénévole</h1>
        <a class="btn btn-outline-secondary btn-sm" href="<?= url('benevole', 'liste') ?>">
          <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
      </div>
      <div class="card-body p-4">
        <form method="post" action="<?= url('benevole', 'modifier', ['id' => $id]) ?>" novalidate>
          <?php $motDePasseObligatoire = false; require __DIR__ . '/../../partials/champs_benevole.php'; ?>
          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-success px-4 fw-semibold" type="submit">
              <i class="bi bi-check2-circle me-1"></i> Enregistrer les modifications
            </button>
            <a class="btn btn-light border" href="<?= url('benevole', 'liste') ?>">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>