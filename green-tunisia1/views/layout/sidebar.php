<aside class="sidebar-wrapper" id="sidebar">
    <a href="/?page=dashboard" class="sidebar-brand">
        <i class="bi bi-leaf-fill"></i>
        <span>GREEN TUNISIA</span>
    </a>
    <div class="flex-grow-1 overflow-y-auto">
        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title">Administration</div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="/?page=dashboard" class="sidebar-menu-link <?= $activePage === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-grid-fill"></i><span>Tableau de bord</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/?page=users" class="sidebar-menu-link <?= str_starts_with($activePage, 'user') ? 'active' : '' ?>">
                        <i class="bi bi-person-lines-fill"></i><span>Utilisateurs</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/?page=roles" class="sidebar-menu-link <?= $activePage === 'roles' ? 'active' : '' ?>">
                        <i class="bi bi-shield-lock-fill"></i><span>Rôles</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="/?page=statistics" class="sidebar-menu-link <?= $activePage === 'statistics' ? 'active' : '' ?>">
                        <i class="bi bi-bar-chart-fill"></i><span>Statistiques</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar-profile">
        <img src="/assets/images/avatar.png" alt="" class="sidebar-profile-img">
        <div class="sidebar-profile-info">
            <div class="sidebar-profile-name"><?= e($_SESSION['user_name'] ?? '') ?></div>
            <div class="sidebar-profile-email"><?= e($_SESSION['role'] ?? '') ?></div>
        </div>
    </div>
</aside>
