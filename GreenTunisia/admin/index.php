<?php
/**
 * Back Office – point d'entrée unique et routeur.
 * Pages : dashboard | articles | article_add | article_edit | categories
 * Exemple : admin/index.php?page=articles
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../controllers/ArticleController.php';
require_once __DIR__ . '/../controllers/CategorieController.php';

$ac     = new ArticleController();
$cc     = new CategorieController();
$page   = $_GET['page'] ?? 'dashboard';
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($isPost && !csrf_check()) {
    http_response_code(419);
    die('Session expirée ou requête invalide. Rechargez la page et réessayez.');
}

$errors = [];
$old    = [];
$title  = 'Back Office';

switch ($page) {

    case 'dashboard':
        $title      = 'Dashboard';
        $all        = $ac->index()['articles'];
        $categories = $cc->index();
        $catModel   = new Categorie();
        $counts     = [];
        foreach ($categories as $c) {
            $counts[$c['id_categorie']] = $catModel->countArticles((int) $c['id_categorie']);
        }
        $nbArticles   = count($all);
        $nbCategories = count($categories);
        $recent       = array_slice($all, 0, 5);
        break;

    case 'articles':
        $title = 'Articles';
        if ($isPost && ($_POST['action'] ?? '') === 'delete') {
            $r = $ac->delete((int) ($_POST['id'] ?? 0));
            $r['success'] ? flash('success', 'Article supprimé.')
                          : flash('danger', implode(' ', $r['errors']));
            redirect(url('articles'));
        }
        $selected = isset($_GET['categorie']) && ctype_digit((string) $_GET['categorie'])
            ? (int) $_GET['categorie'] : null;
        $data       = $ac->index($selected);
        $articles   = $data['articles'];
        $categories = $data['categories'];
        break;

    case 'article_add':
        $title      = 'Ajouter un article';
        $categories = $cc->index();
        $old        = ['titre' => '', 'contenu' => '', 'date_publication' => date('Y-m-d'), 'id_categorie' => 0];
        if ($isPost) {
            $r = $ac->store($_POST, $_FILES['image'] ?? null);
            if ($r['success']) {
                flash('success', 'Article ajouté avec succès.');
                redirect(url('articles'));
            }
            $errors = $r['errors'];
            $old    = $r['old'];
        }
        $formAction   = url('article_add');
        $submitLabel  = 'Ajouter';
        $currentImage = null;
        $view         = 'add_article';
        break;

    case 'article_edit':
        $title = 'Modifier un article';
        $id    = (int) ($_GET['id'] ?? 0);
        $article = $ac->show($id);
        if ($article === null) {
            flash('danger', "Cet article n'existe pas.");
            redirect(url('articles'));
        }
        $categories = $cc->index();
        $old        = $article;
        if ($isPost) {
            $r = $ac->update($id, $_POST, $_FILES['image'] ?? null);
            if ($r['success']) {
                flash('success', 'Article modifié avec succès.');
                redirect(url('articles'));
            }
            $errors = $r['errors'];
            $old    = $r['old'];
        }
        $formAction   = url('article_edit', ['id' => $id]);
        $submitLabel  = 'Enregistrer';
        $currentImage = $article['image'];
        $view         = 'edit_article';
        break;

    case 'categories':
        $title  = 'Catégories';
        $editId = (int) ($_GET['edit'] ?? 0);
        if ($isPost) {
            $action = $_POST['action'] ?? '';
            if ($action === 'add') {
                $r = $cc->store($_POST);
                if ($r['success']) { flash('success', 'Catégorie ajoutée.'); redirect(url('categories')); }
                $errors = $r['errors']; $old = $r['old'];
            } elseif ($action === 'update') {
                $editId = (int) ($_POST['id'] ?? 0);
                $r = $cc->update($editId, $_POST);
                if ($r['success']) { flash('success', 'Catégorie modifiée.'); redirect(url('categories')); }
                $errors = $r['errors']; $old = $r['old'];
            } elseif ($action === 'delete') {
                $r = $cc->delete((int) ($_POST['id'] ?? 0));
                $r['success'] ? flash('success', 'Catégorie supprimée.')
                              : flash('danger', implode(' ', $r['errors']));
                redirect(url('categories'));
            }
        }
        $categories = $cc->index();
        $editCat    = $editId ? $cc->show($editId) : null;
        if ($editCat && !$old) {
            $old = ['nom' => $editCat['nom'], 'description' => $editCat['description']];
        }
        $catModel = new Categorie();
        $counts   = [];
        foreach ($categories as $c) {
            $counts[$c['id_categorie']] = $catModel->countArticles((int) $c['id_categorie']);
        }
        break;

    default:
        http_response_code(404);
        $title = 'Page introuvable';
        $view  = 'not_found';
}

$view = $view ?? $page;
require __DIR__ . '/views/header.php';
require __DIR__ . '/views/' . $view . '.php';
require __DIR__ . '/views/footer.php';
