<?php

declare(strict_types=1);

class Activite
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT a.*, u.nom, u.email
             FROM Activite a JOIN Utilisateur u ON u.id_user = a.id_user
             ORDER BY a.date_action DESC'
        );
        return $stmt->fetchAll();
    }

    public function getByUser(int $id_user): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM Activite WHERE id_user = :id_user ORDER BY date_action DESC'
        );
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll();
    }

    public function create(int $id_user, string $action): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Activite (id_user, action) VALUES (:id_user, :action)'
        );
        $stmt->execute(['id_user' => $id_user, 'action' => trim($action)]);
        return (int) $this->pdo->lastInsertId();
    }

    public function getRecentActivities(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, u.nom, u.email
             FROM Activite a JOIN Utilisateur u ON u.id_user = a.id_user
             ORDER BY a.date_action DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
