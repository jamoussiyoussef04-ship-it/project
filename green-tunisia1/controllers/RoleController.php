<?php

declare(strict_types=1);

class RoleController
{
    public function __construct(
        private PDO $pdo,
        private Role $roles,
        private Activite $activites
    ) {}

    public function index(): void
    {
        $roles = $this->roles->getAll();
        require __DIR__ . '/../views/roles/index.php';
    }

    public function store(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/?page=roles');
        }
        $nom = trim((string) ($_POST['nom_role'] ?? ''));
        if ($nom === '') {
            flash('error', 'Le nom du rôle est obligatoire.');
            redirect('/?page=roles');
        }
        $id = $this->roles->create([
            'nom_role' => $nom,
            'description' => (string) ($_POST['description'] ?? ''),
        ]);
        $this->activites->create((int) $_SESSION['user_id'], 'Ajout du rôle #' . $id);
        flash('success', 'Rôle ajouté avec succès.');
        redirect('/?page=roles');
    }

    public function update(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/?page=roles');
        }
        $id = (int) ($_POST['id_role'] ?? 0);
        $nom = trim((string) ($_POST['nom_role'] ?? ''));
        if ($id <= 0 || $nom === '') {
            flash('error', 'Données du rôle invalides.');
            redirect('/?page=roles');
        }
        $this->roles->update($id, [
            'nom_role' => $nom,
            'description' => (string) ($_POST['description'] ?? ''),
        ]);
        $this->activites->create((int) $_SESSION['user_id'], 'Modification du rôle #' . $id);
        flash('success', 'Rôle modifié avec succès.');
        redirect('/?page=roles');
    }

    public function delete(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/?page=roles');
        }
        $id = (int) ($_POST['id_role'] ?? 0);
        $users = $this->roles->getUsersByRole($id);
        if ($users) {
            flash('error', 'Ce rôle ne peut pas être supprimé car des utilisateurs lui sont associés.');
            redirect('/?page=roles');
        }
        if ($id > 0) {
            $this->roles->delete($id);
            $this->activites->create((int) $_SESSION['user_id'], 'Suppression du rôle #' . $id);
            flash('success', 'Rôle supprimé avec succès.');
        }
        redirect('/?page=roles');
    }
}
