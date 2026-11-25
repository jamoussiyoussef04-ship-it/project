<div class="row justify-content-center">
  <div class="col-md-6 col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
          <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
            <i class="bi bi-tree-fill fs-4"></i>
          </div>
          <h1 class="h3 fw-bold mb-1">Se connecter</h1>
          <p class="text-muted small mb-0">Accédez à votre espace bénévole Green Tunisia</p>
        </div>

        <form method="post" action="<?= url('benevole', 'connexion') ?>">
          <div class="mb-3">
            <label for="email" class="form-label fw-medium">Adresse e-mail</label>
            <input id="email" name="email" type="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label for="mot_de_passe" class="form-label fw-medium">Mot de passe</label>
            <input id="mot_de_passe" name="mot_de_passe" type="password" class="form-control<?= $erreur ? ' is-invalid' : '' ?>" required>
            <?php if ($erreur): ?>
              <div class="invalid-feedback d-block"><?= e($erreur) ?></div>
            <?php endif; ?>
          </div>
          <button class="btn btn-success w-100 py-2 fw-semibold mt-2" type="submit">
            <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
          </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
          Pas encore de compte ? <a href="<?= url('benevole', 'inscription') ?>" class="text-success fw-semibold text-decoration-none">Créer un compte</a>
        </p>
      </div>
    </div>
  </div>
</div>