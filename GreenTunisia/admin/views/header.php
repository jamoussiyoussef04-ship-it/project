<?php
$nav    = ['dashboard' => 'Dashboard', 'articles' => 'Articles', 'categories' => 'Catégories'];
$active = in_array($page, ['article_add', 'article_edit'], true) ? 'articles' : $page;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> – Green Tunisia</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">🌿 Green Tunisia <span>Back Office</span></div>
    <nav>
        <?php foreach ($nav as $key => $label): ?>
            <a href="<?= e(url($key)) ?>" class="<?= $active === $key ? 'active' : '' ?>"><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
</header>
<main class="container">
    <?php foreach (get_flash() as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>
