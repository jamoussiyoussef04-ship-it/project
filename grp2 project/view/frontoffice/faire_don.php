<?php
require_once __DIR__ . '/../../controller/DonateurController.php';
require_once __DIR__ . '/../../controller/DonController.php';

$donateurCtrl = new DonateurController();
$donCtrl = new DonController();

$errors = [];
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $montant = $_POST['montant'] ?? 0;
    $type_don = trim($_POST['type_don'] ?? '');
    $date_don = date('Y-m-d');

    // 1. Validation des champs donateur + don côté serveur (PHP)
    $errorsDonateur = $donateurCtrl->validerDonnees($nom, $email, $telephone);
    // On passe temporairement id_donateur = 1 pour valider montant/date/type
    $errorsDon = $donCtrl->validerDonnees($montant, $date_don, $type_don, 1);
    $errors = array_merge($errorsDonateur, $errorsDon);

    if (empty($errors)) {
        // 2. Vérifier si le donateur existe déjà par email
        $existingDonateur = $donateurCtrl->getDonateurByEmail($email);

        if ($existingDonateur) {
            $id_donateur = (int) $existingDonateur['id_donateur'];
        } else {
            $newDonateur = new Donateur(null, $nom, $email, $telephone);
            $id_donateur = $donateurCtrl->ajouterDonateur($newDonateur);
        }

        if ($id_donateur > 0) {
            // 3. Enregistrer le don lié au donateur
            $don = new Don(null, (float) $montant, $date_don, $type_don, $id_donateur);
            if ($donCtrl->ajouterDon($don)) {
                $successMessage = "Merci " . htmlspecialchars($nom) . " ! Votre don de " .
                    htmlspecialchars(number_format((float)$montant, 2)) . " TND a bien été enregistré.";
                $_POST = [];
            } else {
                $errors[] = "Une erreur est survenue lors de l'enregistrement de votre don.";
            }
        } else {
            $errors[] = "Impossible d'enregistrer les informations du donateur.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Faire un Don - Green Tunisia Association</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../assets/images/favicon.ico">
    <link rel="stylesheet" href="../assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/libs/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <?php $active = 'don'; include __DIR__ . '/_navbar.php'; ?>
    <div class="container pb-5">
        <div class="hero">
            <h1>Soutenir <span class="accent">Green Tunisia</span> Association</h1>
            <p>Vos contributions (financières, matérielles ou en nature) financent directement nos campagnes écologiques, la protection de l'environnement et les actions de nos bénévoles sur le terrain.</p>
        </div>
        <div class="row justify-content-center"><div class="col-lg-8">
    <?php if (!empty($successMessage)): ?>
        <div class="alert-custom alert-custom-success"><?= $successMessage ?></div>
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
    <form id="frontDonForm" method="POST" action="" class="form-stack wide">
        <h3 class="h5 mt-3">Vos coordonnées</h3>

        <label class="form-label-custom" for="nom">Nom complet :</label>
        <input class="form-control-custom" type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>

        <label class="form-label-custom" for="email">Adresse email :</label>
        <input class="form-control-custom" type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

        <label class="form-label-custom" for="telephone">Téléphone (8 chiffres) :</label>
        <input class="form-control-custom" type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required>

        <h3 class="h5 mt-3">Détails du don</h3>

        <label class="form-label-custom" for="type_don">Type de don :</label>
        <?php $selectedType = $_POST['type_don'] ?? 'Financier'; ?>
        <select class="form-select-custom" id="type_don" name="type_don" required>
            <option value="Financier" <?= $selectedType === 'Financier' ? 'selected' : '' ?>>Financier</option>
            <option value="Matériel" <?= $selectedType === 'Matériel' ? 'selected' : '' ?>>Matériel</option>
            <option value="Nature" <?= $selectedType === 'Nature' ? 'selected' : '' ?>>Nature</option>
        </select>

        <label class="form-label-custom" for="montant">Montant (TND) :</label>
        <input class="form-control-custom" type="number" step="0.01" min="0.01" id="montant" name="montant" value="<?= htmlspecialchars($_POST['montant'] ?? '') ?>" required>

        <div class="actions"><button type="submit" class="btn-custom btn-custom-primary btn-custom-lg"><i class="bi bi-heart-fill"></i> Valider mon don</button></div>
    </form>
    </div>
        </div></div>
    </div>

    <script src="../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('frontDonForm').addEventListener('submit', function(e) {
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const tel = document.getElementById('telephone').value.trim();
            const typeDon = document.getElementById('type_don').value;
            const montant = parseFloat(document.getElementById('montant').value);
            const jsErrors = document.getElementById('js-errors');
            let messages = [];

            if (nom.length < 2) {
                messages.push("Veuillez saisir votre nom complet (au moins 2 caractères).");
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                messages.push("Veuillez saisir une adresse email valide.");
            }
            if (!/^[0-9]{8}$/.test(tel)) {
                messages.push("Le numéro de téléphone doit contenir exactement 8 chiffres.");
            }
            if (!typeDon) {
                messages.push("Veuillez choisir un type de don.");
            }
            if (isNaN(montant) || montant <= 0) {
                messages.push("Le montant du don doit être supérieur à 0.");
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