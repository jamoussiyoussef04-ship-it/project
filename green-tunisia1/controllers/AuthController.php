<?php

declare(strict_types=1);

class AuthController
{
    public function __construct(
        private PDO $pdo,
        private Utilisateur $utilisateurs,
        private Activite $activites
    ) {}

    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            redirect('/?page=dashboard');
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Votre session a expiré. Veuillez réessayer.');
            redirect('/?page=login');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['mot_de_passe'] ?? '');
        $user = $this->utilisateurs->getByEmail($email);

        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            flash('error', 'Email ou mot de passe incorrect.');
            redirect('/?page=login');
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id_user'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['nom_role'];
        $_SESSION['role_id'] = (int) $user['id_role'];
        $this->activites->create((int) $user['id_user'], 'Connexion au système');
        redirect('/?page=dashboard');
    }

    public function logout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->activites->create((int) $_SESSION['user_id'], 'Déconnexion du système');
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect('/?page=login');
    }

    public function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            redirect('/?page=login');
        }
    }

    public function requireAdmin(): void
    {
        $this->requireLogin();
        if (!is_admin()) {
            flash('error', "Accès réservé aux administrateurs.");
            redirect('/?page=profil');
        }
    }

    public function profile(): void
    {
        $this->requireLogin();
        $user = $this->utilisateurs->getById((int) $_SESSION['user_id']);
        require __DIR__ . '/../views/profile.php';
    }

    public function updateProfile(): void
    {
        $this->requireLogin();
        if (!csrf_verify()) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/?page=profil');
        }

        $id = (int) $_SESSION['user_id'];
        $nom = trim((string) ($_POST['nom'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['mot_de_passe'] ?? '');

        if ($nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Nom ou adresse email invalide.');
            redirect('/?page=profil');
        }
        $existing = $this->utilisateurs->getByEmail($email);
        if ($existing && (int) $existing['id_user'] !== $id) {
            flash('error', 'Cette adresse email est déjà utilisée.');
            redirect('/?page=profil');
        }

        $current = $this->utilisateurs->getById($id);
        $this->utilisateurs->update($id, [
            'nom' => $nom,
            'email' => $email,
            'id_role' => (int) $current['id_role'],
        ]);
        if ($password !== '') {
            if (strlen($password) < 8) {
                flash('error', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
                redirect('/?page=profil');
            }
            $this->utilisateurs->updatePassword($id, $password);
        }
        $_SESSION['user_name'] = $nom;
        $_SESSION['user_email'] = $email;
        $this->activites->create($id, 'Modification du profil');
        flash('success', 'Profil enregistré avec succès.');
        redirect('/?page=profil');
    }
}
