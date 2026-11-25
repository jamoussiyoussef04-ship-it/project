<?php $active = $active ?? ''; ?>
<nav class="front-nav">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a href="faire_don.php" class="brand"><i class="bi bi-tree-fill"></i><span>Green Tunisia Association</span></a>
        <div class="d-flex gap-2">
            <a class="nav-link-front <?= $active === 'don' ? 'active' : '' ?>" href="faire_don.php">Faire un don</a>
            <a class="nav-link-front <?= $active === 'partenaires' ? 'active' : '' ?>" href="partenaires.php">Nos partenaires</a>
        </div>
    </div>
</nav>
