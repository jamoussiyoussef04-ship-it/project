<?php
require_once __DIR__ . '/../../controller/conventioncontroller.php';
require_once __DIR__ . '/../../controller/partenairecontroller.php';

$conventionCtrl = new ConventionController();
$partenaireCtrl = new PartenaireController();

$errors = [];
$successMessage = "";
$conventionToEdit = null;

// 1. Suppression
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($conventionCtrl->supprimerConvention((int) $_GET['delete'])) {
        header("Location: gestion_conventions.php?msg=deleted");
        exit;
    } else {
        $errors[] = "Erreur lors de la suppression de la convention.";
    }
}

// 2. Chargement pour modification
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $conventionToEdit = $conventionCtrl->getConventionById((int) $_GET['edit']);
}

// 3. Ajout ou Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $id_partenaire = (int) ($_POST['id_partenaire'] ?? 0);
    $id = !empty($_POST['id_convention']) ? (int) $_POST['id_convention'] : null;

    $errors = $conventionCtrl->validerDonnees($date_debut, $date_fin, $description, $id_partenaire);

    if (empty($errors)) {
        $convention = new Convention($id, $date_debut, $date_fin, $description, $id_partenaire);

        if ($id !== null) {
            if ($conventionCtrl->modifierConvention($convention, $id)) {
                header("Location: gestion_conventions.php?msg=updated");
                exit;
            } else {
                $errors[] = "Erreur lors de la modification.";
            }
        } else {
            if ($conventionCtrl->ajouterConvention($convention)) {
                header("Location: gestion_conventions.php?msg=added");
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout.";
            }
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $successMessage = "Convention ajoutée avec succès.";
    if ($_GET['msg'] === 'updated') $successMessage = "Convention modifiée avec succès.";
    if ($_GET['msg'] === 'deleted') $successMessage = "Convention supprimée avec succès.";
}

$partenaires = $partenaireCtrl->afficherPartenaires();
$conventions = $conventionCtrl->afficherConventions();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Gestion des Conventions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <?php $active = 'conventions'; include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-wrapper">
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des conventions</h1>
                <p class="page-subtitle">Gérez les conventions signées avec les partenaires.</p>
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

    <?php if (empty($partenaires)): ?>
        <div class="alert-custom alert-custom-danger">
            Aucun partenaire n'existe encore. Veuillez d'abord <a href="gestion_partenaires.php">ajouter un partenaire</a>.
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h2 class="card-title"><?= $conventionToEdit ? 'Modifier' : 'Ajouter' ?> — convention</h2></div>
        <form id="conventionForm" method="POST" action="" class="form-stack">
        <input type="hidden" name="id_convention" value="<?= htmlspecialchars($conventionToEdit['id_convention'] ?? '') ?>">

        <label class="form-label-custom" for="id_partenaire">Partenaire :</label>
        <select class="form-select-custom" id="id_partenaire" name="id_partenaire" required>
            <option value="">-- Choisir un partenaire --</option>
            <?php
            $selectedPartenaire = $conventionToEdit['id_partenaire'] ?? ($_POST['id_partenaire'] ?? '');
            foreach ($partenaires as $p):
            ?>
                <option value="<?= $p['id_partenaire'] ?>" <?= ((string)$selectedPartenaire === (string)$p['id_partenaire']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label class="form-label-custom" for="date_debut">Date de début :</label>
        <input class="form-control-custom" type="date" id="date_debut" name="date_debut" value="<?= htmlspecialchars($conventionToEdit['date_debut'] ?? ($_POST['date_debut'] ?? '')) ?>" required>

        <label class="form-label-custom" for="date_fin">Date de fin :</label>
        <input class="form-control-custom" type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($conventionToEdit['date_fin'] ?? ($_POST['date_fin'] ?? '')) ?>" required>

        <label class="form-label-custom" for="description">Description de la convention :</label>
        <textarea class="form-control-custom" id="description" name="description" rows="3" required><?= htmlspecialchars($conventionToEdit['description'] ?? ($_POST['description'] ?? '')) ?></textarea>

        <div class="actions"><button type="submit" class="btn-custom btn-custom-primary">
            <?= $conventionToEdit ? 'Mettre à jour la convention' : 'Ajouter la convention' ?>
        </button>

        <?php if ($conventionToEdit): ?>
            <a class="btn-custom btn-custom-light" href="gestion_conventions.php">Annuler</a>
        <?php endif; ?>
        </div>
    </form>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="card-title">Liste des conventions</h2></div>
        <div class="table-responsive">
        <table class="table-custom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Partenaire</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($conventions)): ?>
                <tr><td colspan="6" class="text-center py-4">Aucune convention enregistrée.</td></tr>
            <?php else: ?>
                <?php foreach ($conventions as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['id_convention']) ?></td>
                        <td><?= htmlspecialchars($c['nom_partenaire']) ?></td>
                        <td><?= htmlspecialchars($c['date_debut']) ?></td>
                        <td><?= htmlspecialchars($c['date_fin']) ?></td>
                        <td><?= htmlspecialchars($c['description']) ?></td>
                        <td>
                            <a class="table-btn-action" title="Modifier" href="gestion_conventions.php?edit=<?= $c['id_convention'] ?>"><i class="bi bi-pencil"></i></a>
                            <a class="table-btn-action delete" title="Supprimer" href="gestion_conventions.php?delete=<?= $c['id_convention'] ?>" onclick="return confirm('Supprimer cette convention ?');"><i class="bi bi-trash"></i></a>
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
        document.getElementById('conventionForm').addEventListener('submit', function(e) {
            const partenaire = document.getElementById('id_partenaire').value;
            const dateDebut = document.getElementById('date_debut').value;
            const dateFin = document.getElementById('date_fin').value;
            const desc = document.getElementById('description').value.trim();
            const jsErrors = document.getElementById('js-errors');
            let messages = [];

            if (!partenaire) {
                messages.push("Veuillez choisir un partenaire.");
            }
            if (!dateDebut || !dateFin) {
                messages.push("Veuillez renseigner les deux dates.");
            } else if (dateFin < dateDebut) {
                messages.push("La date de fin ne peut pas être antérieure à la date de début.");
            }
            if (desc.length < 5) {
                messages.push("La description doit contenir au moins 5 caractères.");
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