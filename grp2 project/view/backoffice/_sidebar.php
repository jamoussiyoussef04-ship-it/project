<?php $active = $active ?? ''; ?>
<aside class="sidebar-wrapper">
    <a href="liste_dons.php" class="sidebar-brand"><i class="bi bi-tree-fill"></i><span>Green Tunisia</span></a>
    <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Dons</div>
        <ul class="sidebar-menu-list">
            <li class="sidebar-menu-item"><a class="sidebar-menu-link <?= $active === 'dons' ? 'active' : '' ?>" href="liste_dons.php"><i class="bi bi-heart"></i><span>Dons reçus</span></a></li>
            <li class="sidebar-menu-item"><a class="sidebar-menu-link <?= $active === 'donateurs' ? 'active' : '' ?>" href="gestion_donateurs.php"><i class="bi bi-people"></i><span>Donateurs</span></a></li>
        </ul>
    </div>
    <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Partenariats</div>
        <ul class="sidebar-menu-list">
            <li class="sidebar-menu-item"><a class="sidebar-menu-link <?= $active === 'partenaires' ? 'active' : '' ?>" href="gestion_partenaires.php"><i class="bi bi-building"></i><span>Partenaires</span></a></li>
            <li class="sidebar-menu-item"><a class="sidebar-menu-link <?= $active === 'conventions' ? 'active' : '' ?>" href="gestion_conventions.php"><i class="bi bi-file-earmark-text"></i><span>Conventions</span></a></li>
        </ul>
    </div>
    <div class="sidebar-menu-section">
        <div class="sidebar-menu-title">Site public</div>
        <ul class="sidebar-menu-list">
            <li class="sidebar-menu-item"><a class="sidebar-menu-link" href="../frontoffice/faire_don.php"><i class="bi bi-box-arrow-up-right"></i><span>Faire un don</span></a></li>
            <li class="sidebar-menu-item"><a class="sidebar-menu-link" href="../frontoffice/partenaires.php"><i class="bi bi-box-arrow-up-right"></i><span>Nos partenaires</span></a></li>
        </ul>
    </div>
</aside>
