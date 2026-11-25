<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Model Article : accès à la table `article`.
 */
class Article
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /** Retourne tous les articles avec le nom de leur catégorie. */
    public function getAll(): array
    {
        $sql = 'SELECT a.*, c.nom AS nom_categorie
                FROM article a
                JOIN categorie c ON c.id_categorie = a.id_categorie
                ORDER BY a.date_publication DESC, a.id_article DESC';
        return $this->pdo->query($sql)->fetchAll();
    }

    /** Retourne un article par son id (ou null). */
    public function getById(int $id): ?array
    {
        $sql = 'SELECT a.*, c.nom AS nom_categorie
                FROM article a
                JOIN categorie c ON c.id_categorie = a.id_categorie
                WHERE a.id_article = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Retourne les articles d'une catégorie. */
    public function getByCategorie(int $idCategorie): array
    {
        $sql = 'SELECT a.*, c.nom AS nom_categorie
                FROM article a
                JOIN categorie c ON c.id_categorie = a.id_categorie
                WHERE a.id_categorie = :id
                ORDER BY a.date_publication DESC, a.id_article DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $idCategorie]);
        return $stmt->fetchAll();
    }

    /** Ajoute un article. Retourne l'id créé. */
    public function add(string $titre, string $contenu, string $datePublication, ?string $image, int $idCategorie): int
    {
        $sql = 'INSERT INTO article (titre, contenu, date_publication, image, id_categorie)
                VALUES (:titre, :contenu, :date_publication, :image, :id_categorie)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'titre'            => $titre,
            'contenu'          => $contenu,
            'date_publication' => $datePublication,
            'image'            => $image,
            'id_categorie'     => $idCategorie,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /** Modifie un article. */
    public function update(int $id, string $titre, string $contenu, string $datePublication, ?string $image, int $idCategorie): bool
    {
        $sql = 'UPDATE article
                SET titre = :titre, contenu = :contenu, date_publication = :date_publication,
                    image = :image, id_categorie = :id_categorie
                WHERE id_article = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre'            => $titre,
            'contenu'          => $contenu,
            'date_publication' => $datePublication,
            'image'            => $image,
            'id_categorie'     => $idCategorie,
            'id'               => $id,
        ]);
    }

    /** Supprime un article. */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM article WHERE id_article = :id');
        return $stmt->execute(['id' => $id]);
    }
}
