<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Don.php';

class DonController
{
    public function afficherDons(): array
    {
        $db = Config::getConnexion();
        $sql = "SELECT d.*, dn.nom AS nom_donateur, dn.email AS email_donateur, dn.telephone AS tel_donateur
                FROM don d
                JOIN donateur dn ON d.id_donateur = dn.id_donateur
                ORDER BY d.id_don DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDonById(int $id): ?array
    {
        $db = Config::getConnexion();
        $sql = "SELECT * FROM don WHERE id_don = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function validerDonnees(
        $montant,
        string $date_don,
        string $type_don,
        int $id_donateur
    ): array {
        $errors = [];

        if (!is_numeric($montant) || (float) $montant <= 0) {
            $errors[] = "Le montant du don doit être un nombre supérieur à 0.";
        }

        if (empty($date_don)) {
            $errors[] = "La date du don est obligatoire.";
        }

        $typesAutorises = ['Financier', 'Matériel', 'Nature'];
        if (empty(trim($type_don)) || !in_array($type_don, $typesAutorises, true)) {
            $errors[] = "Veuillez choisir un type de don valide.";
        }

        if ($id_donateur <= 0) {
            $errors[] = "Veuillez sélectionner un donateur valide.";
        }

        return $errors;
    }

    public function ajouterDon(Don $don): bool
    {
        $db = Config::getConnexion();
        $sql = "INSERT INTO don (montant, date_don, type_don, id_donateur)
                VALUES (:montant, :date_don, :type_don, :id_donateur)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':montant' => $don->getMontant(),
            ':date_don' => $don->getDateDon(),
            ':type_don' => $don->getTypeDon(),
            ':id_donateur' => $don->getIdDonateur()
        ]);
    }

    public function modifierDon(Don $don, int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "UPDATE don
                SET montant = :montant,
                    date_don = :date_don,
                    type_don = :type_don,
                    id_donateur = :id_donateur
                WHERE id_don = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':montant' => $don->getMontant(),
            ':date_don' => $don->getDateDon(),
            ':type_don' => $don->getTypeDon(),
            ':id_donateur' => $don->getIdDonateur(),
            ':id' => $id
        ]);
    }

    public function supprimerDon(int $id): bool
    {
        $db = Config::getConnexion();
        $sql = "DELETE FROM don WHERE id_don = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}