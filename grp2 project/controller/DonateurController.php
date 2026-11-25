<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/donateur.php';

class DonateurController
{
    public function afficherDonateurs(): array
    {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM donateur ORDER BY id_donateur DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDonateurById(int $id): ?array
    {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM donateur WHERE id_donateur = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getDonateurByEmail(string $email): ?array
    {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM donateur WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => trim($email)]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function validerDonnees(string $nom, string $email, string $telephone): array
    {
        $errors = [];

        if (empty(trim($nom)) || strlen(trim($nom)) < 2) {
            $errors[] = "Le nom du donateur doit contenir au moins 2 caractères.";
        }

        if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Veuillez saisir une adresse email valide.";
        }

        if (!preg_match('/^[0-9]{8}$/', trim($telephone))) {
            $errors[] = "Le numéro de téléphone doit contenir exactement 8 chiffres.";
        }

        return $errors;
    }

    // Retourne l'ID du donateur inséré (ou 0 en cas d'échec)
    public function ajouterDonateur(Donateur $donateur): int
    {
        $db = Config::getConnexion();
        $sql = "INSERT INTO donateur (nom, email, telephone) VALUES (:nom, :email, :telephone)";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            ':nom' => $donateur->getNom(),
            ':email' => $donateur->getEmail(),
            ':telephone' => $donateur->getTelephone()
        ]);
        return $ok ? (int) $db->lastInsertId() : 0;
    }

    public function modifierDonateur(Donateur $donateur, int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "UPDATE donateur SET nom = :nom, email = :email, telephone = :telephone WHERE id_donateur = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nom' => $donateur->getNom(),
            ':email' => $donateur->getEmail(),
            ':telephone' => $donateur->getTelephone(),
            ':id' => $id
        ]);
    }

    public function supprimerDonateur(int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "DELETE FROM donateur WHERE id_donateur = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}