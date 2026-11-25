<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
          <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mb-2">Espace Bénévole</span>
          <h1 class="h3 fw-bold mb-1">Devenir bénévole</h1>
          <p class="text-muted small mb-0">Créez votre compte pour vous inscrire aux campagnes écologiques de l'association.</p>
        </div>

        <form method="post" action="<?= url('benevole', 'inscription') ?>" novalidate>
          <?php $motDePasseObligatoire = true; require __DIR__ . '/../../partials/champs_benevole.php'; ?>
          <button class="btn btn-success w-100 py-2 fw-semibold mt-2" type="submit">
            <i class="bi bi-person-plus me-1"></i> Créer mon compte
          </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
          Déjà inscrit ? <a href="<?= url('benevole', 'connexion') ?>" class="text-success fw-semibold text-decoration-none">Se connecter</a>
        </p>
      </div>
    </div>
  </div>
</div>