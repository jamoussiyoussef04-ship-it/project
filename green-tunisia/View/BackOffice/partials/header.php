<?php
$pageTitle = $pageTitle ?? 'Administration Campagnes - Green Tunisia';
$activeMenu = $activeMenu ?? 'campagnes';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> - Green Tunisia</title>
  <meta name="description" content="Green Tunisia Association - Back Office Gestion des campagnes écologiques">

  <link rel="icon" type="image/png" href="View/BackOffice/assets/images/favicon.ico">
  <link rel="stylesheet" href="View/BackOffice/assets/libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="View/BackOffice/assets/libs/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="View/BackOffice/assets/libs/apexcharts/apexcharts.css">
  <link rel="stylesheet" href="View/BackOffice/assets/libs/flatpickr/flatpickr.min.css">
  <link rel="stylesheet" href="View/BackOffice/assets/css/main.css">
  <link rel="stylesheet" href="View/BackOffice/assets/css/app-custom.css">
</head>

<body>
  <!-- Sidebar fidèle au template Spark Admin -->
  <div class="sidebar-wrapper" id="sidebar">
    <a href="index.php?space=back&controller=campagne&action=index" class="sidebar-brand">
      <i class="bi bi-asterisk"></i>
      <span>Green Tunisia</span>
    </a>

    <div class="flex-grow-1 overflow-y-auto">
      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Back Office</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="index.php?space=back&controller=campagne&action=index"
               class="sidebar-menu-link <?= $activeMenu === 'campagnes' ? 'active' : '' ?>"
               id="menu-campagnes" title="Campagnes écologiques">
              <i class="bi bi-tree-fill"></i>
              <span>Campagnes</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="index.php?space=back&controller=campagne&action=create"
               class="sidebar-menu-link <?= $activeMenu === 'campagne_create' ? 'active' : '' ?>"
               id="menu-campagne-create" title="Nouvelle campagne">
              <i class="bi bi-plus-circle-fill"></i>
              <span>Ajouter Campagne</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="index.php?space=back&controller=participation&action=index"
               class="sidebar-menu-link <?= $activeMenu === 'participations' ? 'active' : '' ?>"
               id="menu-participations" title="Inscriptions et participations">
              <i class="bi bi-people-fill"></i>
              <span>Participations</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Front Office</div>
        <ul class="sidebar-menu-list">
          <li class="sidebar-menu-item">
            <a href="index.php?space=front&controller=campagne&action=index"
               class="sidebar-menu-link" title="Consulter les campagnes publiques">
              <i class="bi bi-globe2"></i>
              <span>Portail Public</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="index.php?space=front&controller=participation&action=mesParticipations"
               class="sidebar-menu-link" title="Suivi des bénévoles">
              <i class="bi bi-person-check-fill"></i>
              <span>Mes Inscriptions</span>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="sidebar-profile">
      <img src="View/BackOffice/assets/images/avatar.png" alt="Responsable Campagnes" class="sidebar-profile-img">
      <div class="sidebar-profile-info">
        <div class="sidebar-profile-name">Étudiant 2</div>
        <div class="sidebar-profile-email">Campagnes &amp; Participations</div>
      </div>
    </div>
  </div>

  <div class="main-wrapper">
    <!-- Top Navbar fidèle à Spark Admin -->
    <header class="navbar-custom">
      <div class="navbar-left">
        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-3"
          id="desktop-sidebar-toggle" aria-label="Réduire le menu">
          <i class="bi bi-chevron-bar-left"></i>
        </button>
        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Ouvrir le menu">
          <i class="bi bi-list"></i>
        </button>

        <div class="dropdown ms-2">
          <button class="btn-quick-action dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"
            id="quick-actions-dropdown">
            <i class="bi bi-plus-lg"></i>
            <span>Actions rapides</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-quick-action" aria-labelledby="quick-actions-dropdown">
            <li class="dropdown-header">Gestion Écologique</li>
            <li>
              <a class="dropdown-item" href="index.php?space=back&controller=campagne&action=create">
                <i class="bi bi-calendar-plus"></i> Nouvelle campagne
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="index.php?space=back&controller=participation&action=index">
                <i class="bi bi-person-plus"></i> Gérer les inscriptions
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item" href="index.php?space=front&controller=campagne&action=index">
                <i class="bi bi-box-arrow-up-right"></i> Voir le Front Office
              </a>
            </li>
          </ul>
        </div>
      </div>

      <form class="navbar-search-wrapper" method="GET" action="index.php">
        <input type="hidden" name="space" value="back">
        <input type="hidden" name="controller" value="<?= $activeMenu === 'participations' ? 'participation' : 'campagne' ?>">
        <input type="hidden" name="action" value="index">
        <input type="text" class="navbar-search-input" name="search"
               value="<?= e($_GET['search'] ?? '') ?>"
               placeholder="Rechercher une campagne, un lieu, un bénévole..." id="main-search">
        <button class="navbar-search-btn" type="submit" aria-label="Rechercher">
          <i class="bi bi-search"></i>
        </button>
      </form>

      <div class="navbar-actions">
        <a href="index.php?space=front&controller=campagne&action=index"
           class="btn-custom btn-custom-outline-primary btn-custom-sm d-none d-sm-inline-flex">
          <i class="bi bi-globe2"></i> Front Office
        </a>
        <button class="navbar-action-btn" aria-label="Plein écran" id="btn-fullscreen" type="button">
          <i class="bi bi-arrows-fullscreen"></i>
        </button>
      </div>
    </header>

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
