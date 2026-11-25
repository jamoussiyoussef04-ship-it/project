<?php

declare(strict_types=1);

class Utilisateur
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT u.*, r.nom_role FROM Utilisateur u
             JOIN Role r ON r.id_role = u.id_role
             ORDER BY u.id_user DESC'
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, r.nom_role FROM Utilisateur u
             JOIN Role r ON r.id_role = u.id_role WHERE u.id_user = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, r.nom_role FROM Utilisateur u
             JOIN Role r ON r.id_role = u.id_role WHERE u.email = :email'
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Utilisateur (nom, email, mot_de_passe, id_role)
             VALUES (:nom, :email, :mot_de_passe, :id_role)'
        );
        $stmt->execute([
            'nom' => trim($data['nom']),
            'email' => strtolower(trim($data['email'])),
            'mot_de_passe' => password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            'id_role' => (int) $data['id_role'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Utilisateur SET nom = :nom, email = :email, id_role = :id_role
             WHERE id_user = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'nom' => trim($data['nom']),
            'email' => strtolower(trim($data['email'])),
            'id_role' => (int) $data['id_role'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Utilisateur WHERE id_user = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function getUsersWithRoles(): array
    {
        return $this->getAll();
    }

    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Utilisateur SET mot_de_passe = :mot_de_passe WHERE id_user = :id'
        );
        return $stmt->execute([
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $id,
        ]);
    }

    public function search(string $search = '', int $roleId = 0): array
    {
        $sql = 'SELECT u.*, r.nom_role FROM Utilisateur u
                JOIN Role r ON r.id_role = u.id_role WHERE 1=1';
        $params = [];
        if ($search !== '') {
            $sql .= ' AND (u.nom LIKE :search OR u.email LIKE :search_email)';
            $params['search'] = '%' . $search . '%';
            $params['search_email'] = '%' . $search . '%';
        }
        if ($roleId > 0) {
            $sql .= ' AND u.id_role = :role';
            $params['role'] = $roleId;
        }
        $sql .= ' ORDER BY u.id_user DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAdmins(): int
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM Utilisateur u
             JOIN Role r ON r.id_role = u.id_role
             WHERE LOWER(r.nom_role) IN ('administrateur', 'admin')"
        );
        return (int) $stmt->fetchColumn();
    }
}
