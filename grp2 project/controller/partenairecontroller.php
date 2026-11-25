<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/partenaire.php';

class PartenaireController
{
    // Afficher tous les partenaires
    public function afficherPartenaires(): array
    {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM partenaire ORDER BY id_partenaire DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupérer un seul partenaire par ID (utile pour la modification)
    public function getPartenaireById(int $id): ?array
    {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM partenaire WHERE id_partenaire = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Valider les données côté serveur (PHP)
    public function validerDonnees(string $nom, string $email, string $telephone): array
    {
        $errors = [];

        if (empty(trim($nom))) {
            $errors[] = "Le nom du partenaire est obligatoire.";
        }

        if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Veuillez saisir une adresse email valide.";
        }

        // Téléphone tunisien : 8 chiffres
        if (!preg_match('/^[0-9]{8}$/', trim($telephone))) {
            $errors[] = "Le numéro de téléphone doit contenir exactement 8 chiffres.";
        }

        return $errors;
    }

    // Ajouter un partenaire
    public function ajouterPartenaire(Partenaire $partenaire): bool
    {
        $db = Config::getConnexion();
        $sql = "INSERT INTO partenaire (nom, email, telephone) VALUES (:nom, :email, :telephone)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nom' => $partenaire->getNom(),
            ':email' => $partenaire->getEmail(),
            ':telephone' => $partenaire->getTelephone()
        ]);
    }

    // Modifier un partenaire
    public function modifierPartenaire(Partenaire $partenaire, int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "UPDATE partenaire SET nom = :nom, email = :email, telephone = :telephone WHERE id_partenaire = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':nom' => $partenaire->getNom(),
            ':email' => $partenaire->getEmail(),
            ':telephone' => $partenaire->getTelephone(),
            ':id' => $id
        ]);
    }

    // Supprimer un partenaire
    public function supprimerPartenaire(int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "DELETE FROM partenaire WHERE id_partenaire = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}