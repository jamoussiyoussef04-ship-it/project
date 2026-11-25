<header class="navbar-custom">
    <div class="navbar-left">
        <button class="btn-desktop-toggle d-none d-xl-flex align-items-center justify-content-center me-2" id="desktop-sidebar-toggle" aria-label="Réduire le menu">
            <i class="bi bi-chevron-bar-left"></i>
        </button>
        <button class="sidebar-toggle-btn me-2" id="sidebar-toggle" aria-label="Afficher le menu">
            <i class="bi bi-list"></i>
        </button>
    </div>
    <div class="navbar-search-wrapper">
        <form action="/?page=users" method="get" class="d-flex w-100">
            <input type="hidden" name="page" value="users">
            <input type="search" name="q" class="navbar-search-input" placeholder="Rechercher un utilisateur..." aria-label="Recherche">
            <button class="navbar-search-btn" type="submit" aria-label="Rechercher"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="navbar-actions">
        <div class="dropdown">
            <button class="navbar-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="profile-dropdown">
                <img src="/assets/images/avatar.png" alt="" class="navbar-profile-img">
                <span class="navbar-profile-name d-none d-md-inline"><?= e($_SESSION['user_name'] ?? 'Mon profil') ?></span>
                <i class="bi bi-chevron-down navbar-profile-caret"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="profile-dropdown">
                <li class="dropdown-header">GREEN TUNISIA</li>
                <li><a class="dropdown-item" href="/?page=profil"><i class="bi bi-person"></i> Mon profil</a></li>
                <li><a class="dropdown-item text-danger" href="/?page=logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
            </ul>
        </div>
    </div>
</header>
