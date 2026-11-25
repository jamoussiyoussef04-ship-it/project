<?php
abstract class Controller
{
    protected function render(string $vue, array $donnees = [], string $layout = 'front'): void
    {
        extract($donnees);
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . "/../views/$vue.php";
        require __DIR__ . '/../views/layout/footer.php';
    }

    protected function redirect(string $controller, string $action, array $params = []): void
    {
        header('Location: ' . url($controller, $action, $params));
        exit;
    }

    protected function flash(string $message, string $type = 'succes'): void
    {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }

    protected function estPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function exigerConnexion(): int
    {
        if (empty($_SESSION['benevole_id'])) {
            $this->flash('Connectez-vous pour accéder à cette page.', 'erreur');
            $this->redirect('benevole', 'connexion');
        }
        return (int) $_SESSION['benevole_id'];
    }
}
