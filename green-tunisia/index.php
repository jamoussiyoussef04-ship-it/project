<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/Controller/CampagneController.php';
require_once __DIR__ . '/Controller/ParticipationController.php';

$space = $_GET['space'] ?? 'front';
$controllerName = $_GET['controller'] ?? 'campagne';
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) && ctype_digit((string) $_GET['id']) ? (int) $_GET['id'] : 0;

try {
    $campagneController = new CampagneController();
    $participationController = new ParticipationController();

    if ($space === 'back') {
        if ($controllerName === 'campagne') {
            match ($action) {
                'index'  => $campagneController->indexBack(),
                'show'   => $campagneController->showBack($id),
                'create' => $campagneController->createBack(),
                'store'  => $campagneController->storeBack(),
                'edit'   => $campagneController->editBack($id),
                'update' => $campagneController->updateBack($id),
                'delete' => $campagneController->deleteBack($id),
                default  => $campagneController->indexBack(),
            };
        } elseif ($controllerName === 'participation') {
            match ($action) {
                'index'        => $participationController->indexBack(),
                'store'        => $participationController->storeBack(),
                'updateStatut' => $participationController->updateStatutBack($id),
                'delete'       => $participationController->deleteBack($id),
                'exportCsv'    => $participationController->exportCsvBack(),
                default        => $participationController->indexBack(),
            };
        } else {
            $campagneController->indexBack();
        }
    } else {
        if ($controllerName === 'participation') {
            match ($action) {
                'inscrire'          => $participationController->inscrireFront(),
                'mesParticipations' => $participationController->mesParticipationsFront(),
                'desinscrire'       => $participationController->desinscrireFront($id),
                default             => $participationController->mesParticipationsFront(),
            };
        } else {
            match ($action) {
                'index' => $campagneController->indexFront(),
                'show'  => $campagneController->showFront($id),
                default => $campagneController->indexFront(),
            };
        }
    }
} catch (Throwable $e) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Configuration Base de Données - Green Tunisia</title>
      <link rel="stylesheet" href="View/BackOffice/assets/libs/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" href="View/BackOffice/assets/libs/bootstrap-icons/bootstrap-icons.css">
      <link rel="stylesheet" href="View/BackOffice/assets/css/main.css">
    </head>
    <body class="p-4">
      <div class="container py-5">
        <div class="card border-light shadow-sm p-4">
          <div class="alert-custom alert-custom-danger mb-3">
            <i class="bi bi-database-exclamation alert-custom-icon"></i>
            <div class="alert-custom-content">
              <strong>Connexion MySQL / PDO indisponible :</strong> <?= e($e->getMessage()) ?>
            </div>
          </div>
          <h4 class="mb-2">Vérifiez votre environnement XAMPP</h4>
          <ol class="text-muted-green mb-0">
            <li>Démarrez <strong>Apache</strong> et <strong>MySQL</strong> dans le panneau de contrôle XAMPP.</li>
            <li>Importez le script SQL <code>database/green_tunisia.sql</code> dans phpMyAdmin (base <code>green_tunisia</code>).</li>
            <li>Rechargez la page.</li>
          </ol>
        </div>
      </div>
    </body>
    </html>
    <?php
}
