<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$utilisateurs = new Utilisateur($pdo);
$roles = new Role($pdo);
$activites = new Activite($pdo);
$auth = new AuthController($pdo, $utilisateurs, $activites);
$utilisateurController = new UtilisateurController($pdo, $utilisateurs, $roles, $activites);
$roleController = new RoleController($pdo, $roles, $activites);
$statistiqueController = new StatistiqueController($pdo, $utilisateurs, $activites);

$page = (string) ($_GET['page'] ?? 'dashboard');
$action = (string) ($_POST['action'] ?? '');

if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->login();
}
if ($page === 'logout') {
    $auth->logout();
}

$publicPages = ['login'];
if (!in_array($page, $publicPages, true)) {
    $auth->requireLogin();
}

$adminPages = ['dashboard', 'users', 'user_create', 'user_edit', 'user_show', 'roles', 'statistics'];
if (in_array($page, $adminPages, true)) {
    $auth->requireAdmin();
}

switch ($page) {
    case 'login':
        $auth->loginForm();
        break;
    case 'dashboard':
        $statistiqueController->dashboard();
        break;
    case 'users':
        if ($action === 'delete') {
            $utilisateurController->delete();
        }
        $utilisateurController->index();
        break;
    case 'user_create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurController->store();
        }
        $utilisateurController->createForm();
        break;
    case 'user_edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurController->update();
        }
        $utilisateurController->editForm();
        break;
    case 'user_show':
        $utilisateurController->show();
        break;
    case 'roles':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($action === 'update') {
                $roleController->update();
            } elseif ($action === 'delete') {
                $roleController->delete();
            } else {
                $roleController->store();
            }
        }
        $roleController->index();
        break;
    case 'statistics':
        $statistiqueController->statistics();
        break;
    case 'profil':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->updateProfile();
        }
        $auth->profile();
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/../views/404.php';
}
