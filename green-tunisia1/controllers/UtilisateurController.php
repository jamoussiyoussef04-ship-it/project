<?php

declare(strict_types=1);

class UtilisateurController
{
    public function __construct(
        private PDO $pdo,
        private Utilisateur $utilisateurs,
        private Role $roles,
        private Activite $activites
    ) {}

    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $roleId = (int) ($_GET['role'] ?? 0);
        $users = $this->utilisateurs->search($search, $roleId);
        $roles = $this->roles->getAll();
        require __DIR__ . '/../views/users/index.php';
    }

    public function createForm(): void
    {
        $roles = $this->roles->getAll();
        require __DIR__ . '/../views/users/form.php';
    }

    public function store(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/?page=users');
        }
        $data = [
            'nom' => trim((string) ($_POST['nom'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'mot_de_passe' => (string) ($_POST['mot_de_passe'] ?? ''),
            'id_role' => (int) ($_POST['id_role'] ?? 0),
        ];
        if ($data['nom'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            || strlen($data['mot_de_passe']) < 8 || $data['id_role'] <= 0) {
            flash('error', 'Veuillez fournir des données valides (mot de passe: 8 caractères minimum).');
            redirect('/?page=user_create');
        }
        if ($this->utilisateurs->getByEmail($data['email'])) {
            flash('error', 'Cette adresse email existe déjà.');
            redirect('/?page=user_create');
        }
        $id = $this->utilisateurs->create($data);
        $this->activites->create((int) $_SESSION['user_id'], 'Ajout de l’utilisateur #' . $id);
        flash('success', 'Utilisateur ajouté avec succès.');
        redirect('/?page=users');
    }

    public function editForm(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->utilisateurs->getById($id);
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
            redirect('/?page=users');
        }
        $roles = $this->roles->getAll();
        require __DIR__ . '/../views/users/form.php';
    }

    public function update(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/?page=users');
        }
        $id = (int) ($_POST['id'] ?? 0);
        $nom = trim((string) ($_POST['nom'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $roleId = (int) ($_POST['id_role'] ?? 0);
        if ($id <= 0 || $nom === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $roleId <= 0) {
            flash('error', 'Données utilisateur invalides.');
            redirect('/?page=user_edit&id=' . $id);
        }
        $existing = $this->utilisateurs->getByEmail($email);
        if ($existing && (int) $existing['id_user'] !== $id) {
            flash('error', 'Cette adresse email existe déjà.');
            redirect('/?page=user_edit&id=' . $id);
        }
        $this->utilisateurs->update($id, ['nom' => $nom, 'email' => $email, 'id_role' => $roleId]);
        $password = (string) ($_POST['mot_de_passe'] ?? '');
        if ($password !== '') {
            if (strlen($password) < 8) {
                flash('error', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
                redirect('/?page=user_edit&id=' . $id);
            }
            $this->utilisateurs->updatePassword($id, $password);
        }
        $this->activites->create((int) $_SESSION['user_id'], 'Modification de l’utilisateur #' . $id);
        flash('success', 'Utilisateur modifié avec succès.');
        redirect('/?page=users');
    }

    public function delete(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/?page=users');
        }
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) $_SESSION['user_id']) {
            flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect('/?page=users');
        }
        $target = $this->utilisateurs->getById($id);
        if ($target && in_array(strtolower($target['nom_role']), ['admin', 'administrateur'], true)
            && $this->utilisateurs->countAdmins() <= 1) {
            flash('error', 'Impossible de supprimer le dernier administrateur.');
            redirect('/?page=users');
        }
        if ($target) {
            $this->utilisateurs->delete($id);
            $this->activites->create((int) $_SESSION['user_id'], 'Suppression de l’utilisateur #' . $id);
            flash('success', 'Utilisateur supprimé avec succès.');
        }
        redirect('/?page=users');
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $user = $this->utilisateurs->getById($id);
        if (!$user) {
            flash('error', 'Utilisateur introuvable.');
            redirect('/?page=users');
        }
        $activities = $this->activites->getByUser($id);
        require __DIR__ . '/../views/users/show.php';
    }
}
