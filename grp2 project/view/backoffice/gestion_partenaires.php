<?php
require_once __DIR__ . '/../../controller/partenairecontroller.php';

$controller = new PartenaireController();
$errors = [];
$successMessage = "";
$partenaireToEdit = null;

// 1. Suppression d'un partenaire
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $idDelete = (int) $_GET['delete'];
    if ($controller->supprimerPartenaire($idDelete)) {
        header("Location: gestion_partenaires.php?msg=deleted");
        exit;
    } else {
        $errors[] = "Erreur lors de la suppression du partenaire.";
    }
}

// 2. Chargement d'un partenaire pour modification
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $partenaireToEdit = $controller->getPartenaireById((int) $_GET['edit']);
}

// 3. Traitement du formulaire (Ajout ou Modification)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $id = !empty($_POST['id_partenaire']) ? (int) $_POST['id_partenaire'] : null;

    $errors = $controller->validerDonnees($nom, $email, $telephone);

    if (empty($errors)) {
        $partenaire = new Partenaire($id, $nom, $email, $telephone);

        if ($id !== null) {
            if ($controller->modifierPartenaire($partenaire, $id)) {
                header("Location: gestion_partenaires.php?msg=updated");
                exit;
            } else {
                $errors[] = "Erreur lors de la modification.";
            }
        } else {
            if ($controller->ajouterPartenaire($partenaire)) {
                header("Location: gestion_partenaires.php?msg=added");
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout.";
            }
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $successMessage = "Partenaire ajouté avec succès.";
    if ($_GET['msg'] === 'updated') $successMessage = "Partenaire modifié avec succès.";
    if ($_GET['msg'] === 'deleted') $successMessage = "Partenaire supprimé avec succès.";
}

$partenaires = $controller->afficherPartenaires();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Gestion des Partenaires</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <?php $active = 'partenaires'; include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-wrapper">
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des partenaires</h1>
                <p class="page-subtitle">Gérez les partenaires de l'association.</p>
            </div>
            <button type="button" class="btn-custom btn-custom-light d-xl-none" onclick="document.querySelector('.sidebar-wrapper').classList.toggle('show')"><i class="bi bi-list"></i></button>
        </div>

    <?php if (!empty($successMessage)): ?>
        <div class="alert-custom alert-custom-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert-custom alert-custom-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div id="js-errors" class="alert-custom alert-custom-danger" style="display:none;"></div>

    <!-- Formulaire Ajout / Modification -->
    <div class="card">
        <div class="card-header"><h2 class="card-title"><?= $partenaireToEdit ? 'Modifier' : 'Ajouter' ?> — partenaire</h2></div>
        <form id="partenaireForm" method="POST" action="" class="form-stack">
        <input type="hidden" name="id_partenaire" value="<?= htmlspecialchars($partenaireToEdit['id_partenaire'] ?? '') ?>">

        <label class="form-label-custom" for="nom">Nom du partenaire :</label>
        <input class="form-control-custom" type="text" id="nom" name="nom" value="<?= htmlspecialchars($partenaireToEdit['nom'] ?? ($_POST['nom'] ?? '')) ?>" required>

        <label class="form-label-custom" for="email">Email :</label>
        <input class="form-control-custom" type="email" id="email" name="email" value="<?= htmlspecialchars($partenaireToEdit['email'] ?? ($_POST['email'] ?? '')) ?>" required>

        <label class="form-label-custom" for="telephone">Téléphone (8 chiffres) :</label>
        <input class="form-control-custom" type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($partenaireToEdit['telephone'] ?? ($_POST['telephone'] ?? '')) ?>" required>

        <div class="actions"><button type="submit" class="btn-custom btn-custom-primary">
            <?= $partenaireToEdit ? 'Mettre à jour le partenaire' : 'Ajouter le partenaire' ?>
        </button>

        <?php if ($partenaireToEdit): ?>
            <a class="btn-custom btn-custom-light" href="gestion_partenaires.php">Annuler</a>
        <?php endif; ?>
        </div>
    </form>
    </div>

    <!-- Liste des partenaires -->
    <div class="card">
        <div class="card-header"><h2 class="card-title">Liste des partenaires</h2></div>
        <div class="table-responsive">
        <table class="table-custom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($partenaires)): ?>
                <tr><td colspan="5" class="text-center py-4">Aucun partenaire enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($partenaires as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id_partenaire']) ?></td>
                        <td><?= htmlspecialchars($p['nom']) ?></td>
                        <td><?= htmlspecialchars($p['email']) ?></td>
                        <td><?= htmlspecialchars($p['telephone']) ?></td>
                        <td>
                            <a class="table-btn-action" title="Modifier" href="gestion_partenaires.php?edit=<?= $p['id_partenaire'] ?>"><i class="bi bi-pencil"></i></a>
                            <a class="table-btn-action delete" title="Supprimer" href="gestion_partenaires.php?delete=<?= $p['id_partenaire'] ?>" onclick="return confirm('Supprimer ce partenaire ?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
        </div>
    </div>
    </main>

    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('partenaireForm').addEventListener('submit', function(e) {
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const tel = document.getElementById('telephone').value.trim();
            const jsErrors = document.getElementById('js-errors');
            let messages = [];

            if (nom.length < 2) {
                messages.push("Le nom doit contenir au moins 2 caractères.");
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                messages.push("L'adresse email n'est pas valide.");
            }
            if (!/^[0-9]{8}$/.test(tel)) {
                messages.push("Le téléphone doit contenir exactement 8 chiffres.");
            }

            if (messages.length > 0) {
                e.preventDefault();
                jsErrors.style.display = 'block';
                jsErrors.innerHTML = '<ul>' + messages.map(m => '<li>' + m + '</li>').join('') + '</ul>';
            } else {
                jsErrors.style.display = 'none';
            }
        });
    </script>

</body>
</html>