<?php
require_once __DIR__ . '/../../controller/DonController.php';
require_once __DIR__ . '/../../controller/DonateurController.php';

$donCtrl = new DonController();
$donateurCtrl = new DonateurController();

$errors = [];
$successMessage = "";
$donToEdit = null;

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if ($donCtrl->supprimerDon((int) $_GET['delete'])) {
        header("Location: liste_dons.php?msg=deleted");
        exit;
    } else {
        $errors[] = "Erreur lors de la suppression du don.";
    }
}

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $donToEdit = $donCtrl->getDonById((int) $_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = $_POST['montant'] ?? 0;
    $date_don = $_POST['date_don'] ?? '';
    $type_don = $_POST['type_don'] ?? '';
    $id_donateur = (int) ($_POST['id_donateur'] ?? 0);
    $id = !empty($_POST['id_don']) ? (int) $_POST['id_don'] : null;

    $errors = $donCtrl->validerDonnees($montant, $date_don, $type_don, $id_donateur);

    if (empty($errors)) {
        $don = new Don($id, (float) $montant, $date_don, $type_don, $id_donateur);

        if ($id !== null) {
            if ($donCtrl->modifierDon($don, $id)) {
                header("Location: liste_dons.php?msg=updated");
                exit;
            } else {
                $errors[] = "Erreur lors de la modification.";
            }
        } else {
            if ($donCtrl->ajouterDon($don)) {
                header("Location: liste_dons.php?msg=added");
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout.";
            }
        }
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $successMessage = "Don enregistré avec succès.";
    if ($_GET['msg'] === 'updated') $successMessage = "Don modifié avec succès.";
    if ($_GET['msg'] === 'deleted') $successMessage = "Don supprimé avec succès.";
}

$donateurs = $donateurCtrl->afficherDonateurs();
$dons = $donCtrl->afficherDons();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Back Office - Dons Reçus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <?php $active = 'dons'; include __DIR__ . '/_sidebar.php'; ?>
    <main class="main-wrapper">
        <div class="page-header">
            <div>
                <h1 class="page-title">Dons reçus</h1>
                <p class="page-subtitle">Consultez et gérez les dons enregistrés.</p>
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

    <div class="card">
        <div class="card-header"><h2 class="card-title"><?= $donToEdit ? 'Modifier' : 'Ajouter' ?> — don</h2></div>
        <form id="donForm" method="POST" action="" class="form-stack">
        <input type="hidden" name="id_don" value="<?= htmlspecialchars($donToEdit['id_don'] ?? '') ?>">

        <label class="form-label-custom" for="id_donateur">Donateur :</label>
        <select class="form-select-custom" id="id_donateur" name="id_donateur" required>
            <option value="">-- Choisir un donateur --</option>
            <?php
            $selectedDonateur = $donToEdit['id_donateur'] ?? ($_POST['id_donateur'] ?? '');
            foreach ($donateurs as $dn):
            ?>
                <option value="<?= $dn['id_donateur'] ?>" <?= ((string)$selectedDonateur === (string)$dn['id_donateur']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dn['nom']) ?> (<?= htmlspecialchars($dn['email']) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label class="form-label-custom" for="montant">Montant (TND) :</label>
        <input class="form-control-custom" type="number" step="0.01" min="0.01" id="montant" name="montant" value="<?= htmlspecialchars($donToEdit['montant'] ?? ($_POST['montant'] ?? '')) ?>" required>

        <label class="form-label-custom" for="type_don">Type de don :</label>
        <?php $selectedType = $donToEdit['type_don'] ?? ($_POST['type_don'] ?? 'Financier'); ?>
        <select class="form-select-custom" id="type_don" name="type_don" required>
            <option value="Financier" <?= $selectedType === 'Financier' ? 'selected' : '' ?>>Financier</option>
            <option value="Matériel" <?= $selectedType === 'Matériel' ? 'selected' : '' ?>>Matériel</option>
            <option value="Nature" <?= $selectedType === 'Nature' ? 'selected' : '' ?>>Nature</option>
        </select>

        <label class="form-label-custom" for="date_don">Date du don :</label>
        <input class="form-control-custom" type="date" id="date_don" name="date_don" value="<?= htmlspecialchars($donToEdit['date_don'] ?? ($_POST['date_don'] ?? date('Y-m-d'))) ?>" required>

        <div class="actions"><button type="submit" class="btn-custom btn-custom-primary">
            <?= $donToEdit ? 'Mettre à jour le don' : 'Enregistrer un don' ?>
        </button>
        <?php if ($donToEdit): ?>
            <a class="btn-custom btn-custom-light" href="liste_dons.php">Annuler</a>
        <?php endif; ?>
        </div>
    </form>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="card-title">Liste des dons reçus</h2></div>
        <div class="table-responsive">
        <table class="table-custom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Donateur</th>
                <th>Email</th>
                <th>Montant</th>
                <th>Type</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dons)): ?>
                <tr><td colspan="7" class="text-center py-4">Aucun don enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($dons as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['id_don']) ?></td>
                        <td><?= htmlspecialchars($d['nom_donateur']) ?></td>
                        <td><?= htmlspecialchars($d['email_donateur']) ?></td>
                        <td><?= htmlspecialchars(number_format((float)$d['montant'], 2)) ?> TND</td>
                        <td><span class="badge-table success"><?= htmlspecialchars($d['type_don']) ?></span></td>
                        <td><?= htmlspecialchars($d['date_don']) ?></td>
                        <td>
                            <a class="table-btn-action" title="Modifier" href="liste_dons.php?edit=<?= $d['id_don'] ?>"><i class="bi bi-pencil"></i></a>
                            <a class="table-btn-action delete" title="Supprimer" href="liste_dons.php?delete=<?= $d['id_don'] ?>" onclick="return confirm('Supprimer ce don ?');"><i class="bi bi-trash"></i></a>
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
        document.getElementById('donForm').addEventListener('submit', function(e) {
            const donateur = document.getElementById('id_donateur').value;
            const montant = parseFloat(document.getElementById('montant').value);
            const dateDon = document.getElementById('date_don').value;
            const typeDon = document.getElementById('type_don').value;
            const jsErrors = document.getElementById('js-errors');
            let messages = [];

            if (!donateur) messages.push("Veuillez choisir un donateur.");
            if (isNaN(montant) || montant <= 0) messages.push("Le montant doit être supérieur à 0.");
            if (!typeDon) messages.push("Veuillez choisir un type de don.");
            if (!dateDon) messages.push("Veuillez saisir la date du don.");

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