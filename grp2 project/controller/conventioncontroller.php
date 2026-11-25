<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/convention.php';

class ConventionController
{
    // Afficher toutes les conventions avec le nom du partenaire
    public function afficherConventions(): array
    {
        $db = Config::getConnexion();
        $sql = "SELECT c.*, p.nom AS nom_partenaire
                FROM convention c
                JOIN partenaire p ON c.id_partenaire = p.id_partenaire
                ORDER BY c.id_convention DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Récupérer une convention par ID (pour modification)
    public function getConventionById(int $id): ?array
    {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM convention WHERE id_convention = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Validation des données côté serveur (PHP)
    public function validerDonnees(
        string $date_debut,
        string $date_fin,
        string $description,
        int $id_partenaire
    ): array {
        $errors = [];

        if (empty($date_debut) || empty($date_fin)) {
            $errors[] = "Les dates de début et de fin sont obligatoires.";
        } elseif ($date_fin < $date_debut) {
            $errors[] = "La date de fin doit être supérieure ou égale à la date de début.";
        }

        if (empty(trim($description)) || strlen(trim($description)) < 5) {
            $errors[] = "La description doit contenir au moins 5 caractères.";
        }

        if ($id_partenaire <= 0) {
            $errors[] = "Veuillez sélectionner un partenaire valide.";
        }

        return $errors;
    }

    // Ajouter une convention
    public function ajouterConvention(Convention $convention): bool
    {
        $db = Config::getConnexion();
        $sql = "INSERT INTO convention (date_debut, date_fin, description, id_partenaire)
                VALUES (:date_debut, :date_fin, :description, :id_partenaire)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':date_debut' => $convention->getDateDebut(),
            ':date_fin' => $convention->getDateFin(),
            ':description' => $convention->getDescription(),
            ':id_partenaire' => $convention->getIdPartenaire()
        ]);
    }

    // Modifier une convention
    public function modifierConvention(Convention $convention, int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "UPDATE convention
                SET date_debut = :date_debut,
                    date_fin = :date_fin,
                    description = :description,
                    id_partenaire = :id_partenaire
                WHERE id_convention = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':date_debut' => $convention->getDateDebut(),
            ':date_fin' => $convention->getDateFin(),
            ':description' => $convention->getDescription(),
            ':id_partenaire' => $convention->getIdPartenaire(),
            ':id' => $id
        ]);
    }

    // Supprimer une convention
    public function supprimerConvention(int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "DELETE FROM convention WHERE id_convention = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}