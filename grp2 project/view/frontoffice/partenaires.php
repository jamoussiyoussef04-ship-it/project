<?php
require_once __DIR__ . '/../../controller/partenairecontroller.php';
require_once __DIR__ . '/../../controller/conventioncontroller.php';

$partenaireCtrl = new PartenaireController();
$conventionCtrl = new ConventionController();

$partenaires = $partenaireCtrl->afficherPartenaires();
$conventions = $conventionCtrl->afficherConventions();

// Regrouper les conventions par id_partenaire
$conventionsParPartenaire = [];
foreach ($conventions as $c) {
    $conventionsParPartenaire[$c['id_partenaire']][] = $c;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos Partenaires - Green Tunisia Association</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <?php $active = 'partenaires'; include __DIR__ . '/_navbar.php'; ?>
    <div class="container pb-5">
        <div class="hero">
            <h1>Découvrir nos <span class="accent">partenaires</span></h1>
            <p>Ils accompagnent Green Tunisia Association dans ses projets écologiques et durables.</p>
        </div>
    <?php if (empty($partenaires)): ?>
        <div class="alert-custom alert-custom-info">Aucun partenaire n'est affiché pour le moment.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($partenaires as $p): ?>
                <div class="col-md-6 col-lg-4"><div class="card partner-card h-100">
                    <h3><?= htmlspecialchars($p['nom']) ?></h3>
                    <p class="mb-1"><i class="bi bi-envelope"></i>  <?= htmlspecialchars($p['email']) ?></p>
                    <p class="mb-1"><i class="bi bi-telephone"></i> <?= htmlspecialchars($p['telephone']) ?></p>

                    <?php $pConvs = $conventionsParPartenaire[$p['id_partenaire']] ?? []; ?>
                    <?php if (!empty($pConvs)): ?>
                        <div class="conv">
                            <strong>Conventions :</strong>
                            <ul>
                                <?php foreach ($pConvs as $cv): ?>
                                    <li>
                                        <?= htmlspecialchars($cv['description']) ?>
                                        (<?= htmlspecialchars($cv['date_debut']) ?> au <?= htmlspecialchars($cv['date_fin']) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    </div>
    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
