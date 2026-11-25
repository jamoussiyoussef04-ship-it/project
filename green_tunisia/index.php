<?php

session_start();

require __DIR__ . '/config/Database.php';
require __DIR__ . '/config/helpers.php';

spl_autoload_register(function (string $classe) {
    foreach (['models', 'controllers'] as $dossier) {
        $fichier = __DIR__ . "/$dossier/$classe.php";
        if (is_file($fichier)) {
            require $fichier;
            return;
        }
    }
});

$controllers = ['benevole' => 'BenevoleController', 'participation' => 'ParticipationController'];
$nom    = $_GET['controller'] ?? 'benevole';
$action = $_GET['action'] ?? 'connexion';

if (!isset($controllers[$nom])) {
    http_response_code(404);
    exit('Page introuvable.');
}

$controller = new $controllers[$nom]();
if (!is_callable([$controller, $action])) {
    http_response_code(404);
    exit('Action introuvable.');
}

try {
    $controller->$action();
} catch (PDOException $ex) {
    http_response_code(500);
    exit('Erreur de base de données : ' . e($ex->getMessage()));
}
