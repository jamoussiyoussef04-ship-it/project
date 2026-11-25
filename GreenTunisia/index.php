<?php
// Page de test temporaire : vérifie la connexion PDO et la lecture des tables.
require_once __DIR__ . '/config/database.php';

$pdo = Database::getConnection();

echo '<h2>Connexion PDO réussie ✅</h2>';

$categories = $pdo->query('SELECT * FROM categorie')->fetchAll();
echo '<h3>Catégories (' . count($categories) . ')</h3><ul>';
foreach ($categories as $c) {
    echo '<li>' . htmlspecialchars($c['nom']) . '</li>';
}
echo '</ul>';

$articles = $pdo->query('SELECT * FROM article')->fetchAll();
echo '<h3>Articles (' . count($articles) . ')</h3><ul>';
foreach ($articles as $a) {
    echo '<li>' . htmlspecialchars($a['titre']) . '</li>';
}
echo '</ul>';
