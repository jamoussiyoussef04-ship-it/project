<?php
$pageTitle = $pageTitle ?? 'Campagnes Écologiques - Green Tunisia';
$activeFront = $activeFront ?? 'campagnes';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> - Green Tunisia</title>
  <meta name="description" content="Green Tunisia Association - Plateforme collaborative pour l'écologie en Tunisie">

  <link rel="icon" type="image/png" href="View/FrontOffice/assets/images/favicon.ico">
  <link rel="stylesheet" href="View/FrontOffice/assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="View/FrontOffice/assets/libs/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="View/FrontOffice/assets/css/main.css">
  <link rel="stylesheet" href="View/FrontOffice/assets/css/app-custom.css">
</head>

<body class="front-body">
  <nav class="front-navbar">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
      <a href="index.php?space=front&controller=campagne&action=index" class="front-brand">
        <i class="bi bi-asterisk"></i>
        <span>Green Tunisia</span>
      </a>

      <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="index.php?space=front&controller=campagne&action=index"
           class="front-nav-link <?= $activeFront === 'campagnes' ? 'active' : '' ?>">
          <i class="bi bi-tree-fill"></i>
          <span>Campagnes disponibles</span>
        </a>
        <a href="index.php?space=front&controller=participation&action=mesParticipations"
           class="front-nav-link <?= $activeFront === 'participations' ? 'active' : '' ?>">
          <i class="bi bi-person-check-fill"></i>
          <span>Mes inscriptions</span>
        </a>
        <a href="index.php?space=back&controller=campagne&action=index"
           class="btn-custom btn-custom-secondary btn-custom-sm">
          <i class="bi bi-speedometer2"></i>
          <span>Back Office Admin</span>
        </a>
      </div>
    </div>
  </nav>

  <main class="front-main">
    <div class="container">
      <?php if (!empty($flashes)): ?>
        <?php foreach ($flashes as $flash): ?>
          <?php
            $alertClass = match ($flash['type']) {
                'success' => 'alert-custom-success',
                'danger'  => 'alert-custom-danger',
                'warning' => 'alert-custom-warning',
                default   => 'alert-custom-info',
            };
            $iconClass = match ($flash['type']) {
                'success' => 'bi-check-circle-fill',
                'danger'  => 'bi-exclamation-triangle-fill',
                'warning' => 'bi-exclamation-circle-fill',
                default   => 'bi-info-circle-fill',
            };
          ?>
          <div class="alert-custom <?= $alertClass ?>" role="alert">
            <i class="bi <?= $iconClass ?> alert-custom-icon"></i>
            <div class="alert-custom-content"><?= e($flash['message']) ?></div>
            <button class="alert-custom-close" type="button" aria-label="Fermer" onclick="this.parentElement.remove();">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
