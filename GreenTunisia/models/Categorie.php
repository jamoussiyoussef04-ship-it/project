<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Model Categorie : accès à la table `categorie`.
 */
class Categorie
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /** Retourne toutes les catégories. */
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categorie ORDER BY nom');
        return $stmt->fetchAll();
    }

    /** Retourne une catégorie par son id (ou null si elle n'existe pas). */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categorie WHERE id_categorie = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Ajoute une catégorie. Retourne l'id créé. */
    public function add(string $nom, ?string $description): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categorie (nom, description) VALUES (:nom, :description)'
        );
        $stmt->execute(['nom' => $nom, 'description' => $description]);
        return (int) $this->pdo->lastInsertId();
    }

    /** Modifie une catégorie. */
    public function update(int $id, string $nom, ?string $description): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE categorie SET nom = :nom, description = :description
             WHERE id_categorie = :id'
        );
        return $stmt->execute(['nom' => $nom, 'description' => $description, 'id' => $id]);
    }

    /** Compte les articles liés à une catégorie (règle métier 6). */
    public function countArticles(int $id): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM article WHERE id_categorie = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Supprime une catégorie.
     * Règle métier 6 : refusée si des articles y sont encore rattachés.
     * Retourne true si supprimée, false sinon.
     */
    public function delete(int $id): bool
    {
        if ($this->countArticles($id) > 0) {
            return false;
        }
        $stmt = $this->pdo->prepare('DELETE FROM categorie WHERE id_categorie = :id');
        return $stmt->execute(['id' => $id]);
    }
}
