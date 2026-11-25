<?php

declare(strict_types=1);

class Role
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT r.*, COUNT(u.id_user) AS user_count
             FROM Role r LEFT JOIN Utilisateur u ON u.id_role = r.id_role
             GROUP BY r.id_role, r.nom_role, r.description
             ORDER BY r.nom_role'
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Role WHERE id_role = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Role (nom_role, description) VALUES (:nom_role, :description)'
        );
        $stmt->execute([
            'nom_role' => trim($data['nom_role']),
            'description' => trim($data['description'] ?? ''),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Role SET nom_role = :nom_role, description = :description WHERE id_role = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'nom_role' => trim($data['nom_role']),
            'description' => trim($data['description'] ?? ''),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Role WHERE id_role = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function getUsersByRole(int $id_role): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Utilisateur WHERE id_role = :id_role');
        $stmt->execute(['id_role' => $id_role]);
        return $stmt->fetchAll();
    }
}
