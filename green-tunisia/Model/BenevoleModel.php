<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Benevole.php';

/**
 * Classe BenevoleModel (DAO / Repository POO)
 * Fournit l'accès aux bénévoles (table partagée avec Étudiant 3).
 */
class BenevoleModel
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM benevole ORDER BY nom ASC, prenom ASC');
        return $stmt->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getById(int $idBenevole): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM benevole WHERE id_benevole = :id');
        $stmt->execute([':id' => $idBenevole]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM benevole WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => strtolower(trim($email))]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Crée rapidement un profil bénévole s'il n'existe pas encore par email.
     */
    public function findOrCreate(Benevole $benevole): int
    {
        $existing = $this->findByEmail($benevole->getEmail());
        if ($existing !== null) {
            return (int) $existing['id_benevole'];
        }

        $sql = 'INSERT INTO benevole (nom, prenom, email, telephone) VALUES (:nom, :prenom, :email, :telephone)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'       => $benevole->getNom(),
            ':prenom'    => $benevole->getPrenom(),
            ':email'     => $benevole->getEmail(),
            ':telephone' => $benevole->getTelephone(),
        ]);

        $newId = (int) $this->pdo->lastInsertId();
        $benevole->setIdBenevole($newId);

        return $newId;
    }
}
