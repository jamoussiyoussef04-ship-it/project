<?php $layout = $layout ?? 'front'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Green Tunisia Association<?= $layout === 'back' ? ' - Administration' : '' ?></title>
  <link rel="icon" type="image/png" href="assets/images/favicon.ico">
  <link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/libs/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body class="<?= e($layout) ?> bg-light">

<?php if ($layout === 'back'): ?>
  <div class="d-flex min-vh-100">
    <!-- Sidebar Back-Office -->
    <aside class="bg-dark text-white p-3 d-flex flex-column" style="width: 260px; flex-shrink: 0;">
      <a class="d-flex align-items-center gap-2 text-white text-decoration-none fs-5 fw-bold mb-4 px-2" href="index.php">
        <i class="bi bi-tree-fill text-success"></i>
        <span>Green Tunisia</span>
      </a>
      <div class="text-uppercase text-secondary small fw-semibold px-2 mb-2">Administration</div>
      <ul class="nav nav-pills flex-column gap-1 mb-auto">
        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center gap-2" href="<?= url('benevole', 'liste') ?>">
            <i class="bi bi-people"></i> Bénévoles
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white d-flex align-items-center gap-2" href="<?= url('participation', 'historique') ?>">
            <i class="bi bi-calendar-check"></i> Historique participations
          </a>
        </li>
      </ul>
      <hr class="border-secondary">
      <a class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center gap-2" href="<?= url('benevole', 'connexion') ?>">
        <i class="bi bi-box-arrow-up-right"></i> Espace bénévole
      </a>
    </aside>

    <!-- Contenu Principal Back-Office -->
    <div class="flex-grow-1 d-flex flex-column">
      <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold text-muted">Tableau de bord — Gestion des bénévoles</span>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
          <i class="bi bi-shield-check me-1"></i> Administrateur
        </span>
      </header>
      <main class="container-fluid p-4 flex-grow-1">

<?php else: ?>
  <!-- Navbar Front-Office -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
        <i class="bi bi-tree-fill"></i> Green Tunisia
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFront">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarFront">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
          <?php if (!empty($_SESSION['benevole_id'])): ?>
            <li class="nav-item">
              <a class="nav-link text-white" href="<?= url('participation', 'mesParticipations') ?>">
                <i class="bi bi-calendar2-event me-1"></i> Mes participations
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="<?= url('benevole', 'profil') ?>">
                <i class="bi bi-person-circle me-1"></i> Mon profil
              </a>
            </li>
            <li class="nav-item">
              <a class="btn btn-outline-light btn-sm ms-lg-2" href="<?= url('benevole', 'deconnexion') ?>">
                <i class="bi bi-box-arrow-right me-1"></i> Se déconnecter
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link text-white" href="<?= url('benevole', 'connexion') ?>">Se connecter</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-light text-success fw-semibold btn-sm ms-lg-2" href="<?= url('benevole', 'inscription') ?>">Créer un compte</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <main class="container py-5">
<?php endif; ?>

<?php if (!empty($_SESSION['flash'])): ?>
  <?php $alertClass = ($_SESSION['flash']['type'] ?? '') === 'erreur' ? 'alert-danger' : 'alert-success'; ?>
  <div class="alert <?= $alertClass ?> alert-dismissible fade show shadow-sm" role="alert">
    <?= e($_SESSION['flash']['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>