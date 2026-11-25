<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white border-bottom py-3">
        <h1 class="h4 fw-bold mb-0"><i class="bi bi-person-gear text-success me-2"></i>Mon profil</h1>
      </div>
      <div class="card-body p-4">
        <form method="post" action="<?= url('benevole', 'profil') ?>" novalidate>
          <?php $motDePasseObligatoire = false; require __DIR__ . '/../../partials/champs_benevole.php'; ?>
          <button class="btn btn-success px-4 fw-semibold" type="submit">
            <i class="bi bi-check2-circle me-1"></i> Enregistrer les modifications
          </button>
        </form>
      </div>
    </div>
  </div>
</div>