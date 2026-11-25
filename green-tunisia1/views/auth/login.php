<?php $error = flash('error'); ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - GREEN TUNISIA</title>
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
        <a href="/?page=login" class="login-brand">
            <i class="bi bi-leaf-fill"></i>
            <span>GREEN TUNISIA</span>
        </a>
        <h1 class="login-title">Connexion</h1>
        <p class="login-subtitle">Accédez à votre espace d’administration</p>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post" action="/?page=login">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="login-form-group">
                <label for="email" class="login-form-label">Email</label>
                <div class="login-input-group">
                    <i class="bi bi-envelope input-icon"></i>
                    <input id="email" name="email" type="email" class="login-input" required autocomplete="email">
                </div>
            </div>
            <div class="login-form-group">
                <label for="password" class="login-form-label">Mot de passe</label>
                <div class="login-input-group">
                    <i class="bi bi-shield-lock input-icon"></i>
                    <input id="password" name="mot_de_passe" type="password" class="login-input" required autocomplete="current-password">
                </div>
            </div>
            <button class="btn-login" type="submit">
                <span>Se connecter</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    </section>
</main>
<script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
