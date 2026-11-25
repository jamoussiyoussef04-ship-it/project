<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Jeton CSRF invalide.';
    } else {
        $nom = trim((string) ($_POST['nom'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['mot_de_passe'] ?? '');
        if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $error = 'Renseignez un nom, un email valide et un mot de passe de 8 caractères minimum.';
        } else {
            $count = (int) $pdo->query('SELECT COUNT(*) FROM Utilisateur')->fetchColumn();
            if ($count > 0) {
                $error = 'Un utilisateur existe déjà. Utilisez la page de connexion.';
            } else {
                $pdo->beginTransaction();
                try {
                    $role = $pdo->prepare(
                        'INSERT INTO Role (nom_role, description)
                         SELECT :nom, :description
                         WHERE NOT EXISTS (SELECT 1 FROM Role WHERE LOWER(nom_role) IN (\'administrateur\', \'admin\'))'
                    );
                    $role->execute(['nom' => 'Administrateur', 'description' => 'Accès complet au back-office']);
                    $roleRow = $pdo->query(
                        "SELECT id_role FROM Role
                         WHERE LOWER(nom_role) IN ('administrateur', 'admin') ORDER BY id_role LIMIT 1"
                    )->fetch();
                    if (!$roleRow) {
                        throw new RuntimeException('Impossible de créer le rôle administrateur.');
                    }
                    $stmt = $pdo->prepare(
                        'INSERT INTO Utilisateur (nom, email, mot_de_passe, id_role)
                         VALUES (:nom, :email, :password, :role)'
                    );
                    $stmt->execute([
                        'nom' => $nom,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => (int) $roleRow['id_role'],
                    ]);
                    $pdo->commit();
                    $success = 'Compte administrateur créé. Vous pouvez maintenant vous connecter.';
                } catch (Throwable $exception) {
                    $pdo->rollBack();
                    $error = 'Impossible de créer le compte : ' . $exception->getMessage();
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Initialisation - GREEN TUNISIA</title>
    <link rel="stylesheet" href="/assets/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/libs/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<main class="login-wrapper">
    <div class="login-bg-shape login-bg-shape-1"></div>
    <div class="login-bg-shape login-bg-shape-2"></div>
    <section class="login-card">
        <a href="/?page=login" class="login-brand"><i class="bi bi-leaf-fill"></i><span>GREEN TUNISIA</span></a>
        <h1 class="login-title">Initialiser l’administration</h1>
        <p class="login-subtitle">Créez le compte administrateur principal</p>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="login-form-group"><label class="login-form-label" for="setup-name">Nom</label><div class="login-input-group"><i class="bi bi-person input-icon"></i><input id="setup-name" class="login-input" name="nom" required></div></div>
            <div class="login-form-group"><label class="login-form-label" for="setup-email">Email</label><div class="login-input-group"><i class="bi bi-envelope input-icon"></i><input id="setup-email" class="login-input" type="email" name="email" required></div></div>
            <div class="login-form-group"><label class="login-form-label" for="setup-password">Mot de passe</label><div class="login-input-group"><i class="bi bi-shield-lock input-icon"></i><input id="setup-password" class="login-input" type="password" name="mot_de_passe" minlength="8" required></div></div>
            <button class="btn-login" type="submit"><span>Créer le compte administrateur</span><i class="bi bi-arrow-right"></i></button>
            <p class="login-footer-text mt-4"><a href="/?page=login">Se connecter</a></p>
        </form>
    </section>
</main>
</body>
</html>
