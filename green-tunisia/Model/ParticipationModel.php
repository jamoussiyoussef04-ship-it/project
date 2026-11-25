<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Participation.php';

/**
 * Classe ParticipationModel (DAO / Repository POO)
 * Gère les opérations CRUD sur participation avec jointures SQL vers campagne et benevole.
 */
class ParticipationModel
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Liste toutes les participations avec jointure sur campagne et benevole.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(
        ?int $idCampagne = null,
        ?string $statut = null,
        ?string $search = null
    ): array {
        $sql = "
            SELECT
                p.id_participation,
                p.id_campagne,
                p.id_benevole,
                p.date_inscription,
                p.statut,
                p.commentaire,
                c.titre AS campagne_titre,
                c.date AS campagne_date,
                c.lieu AS campagne_lieu,
                c.type_campagne,
                c.statut AS campagne_statut,
                b.nom AS benevole_nom,
                b.prenom AS benevole_prenom,
                b.email AS benevole_email,
                b.telephone AS benevole_telephone
            FROM participation p
            INNER JOIN campagne c ON p.id_campagne = c.id_campagne
            INNER JOIN benevole b ON p.id_benevole = b.id_benevole
            WHERE 1 = 1
        ";

        $params = [];

        if ($idCampagne !== null && $idCampagne > 0) {
            $sql .= " AND p.id_campagne = :id_campagne";
            $params[':id_campagne'] = $idCampagne;
        }

        if ($statut !== null && $statut !== '' && in_array($statut, Participation::STATUTS_VALIDES, true)) {
            $sql .= " AND p.statut = :statut";
            $params[':statut'] = $statut;
        }

        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $sql .= " AND (b.nom LIKE :s1 OR b.prenom LIKE :s2 OR b.email LIKE :s3 OR c.titre LIKE :s4)";
            $params[':s1'] = $term;
            $params[':s2'] = $term;
            $params[':s3'] = $term;
            $params[':s4'] = $term;
        }

        $sql .= " ORDER BY p.date_inscription DESC, p.id_participation DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Récupère les participations d'une campagne donnée (Jointure INNER JOIN benevole).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByCampagne(int $idCampagne): array
    {
        $sql = "
            SELECT
                p.id_participation,
                p.id_campagne,
                p.id_benevole,
                p.date_inscription,
                p.statut,
                p.commentaire,
                b.nom AS benevole_nom,
                b.prenom AS benevole_prenom,
                b.email AS benevole_email,
                b.telephone AS benevole_telephone
            FROM participation p
            INNER JOIN benevole b ON p.id_benevole = b.id_benevole
            WHERE p.id_campagne = :id_campagne
            ORDER BY p.date_inscription DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_campagne' => $idCampagne]);

        return $stmt->fetchAll();
    }

    /**
     * Récupère les participations d'un bénévole donné (Jointure INNER JOIN campagne).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByBenevole(int $idBenevole): array
    {
        $sql = "
            SELECT
                p.id_participation,
                p.id_campagne,
                p.id_benevole,
                p.date_inscription,
                p.statut,
                p.commentaire,
                c.titre AS campagne_titre,
                c.date AS campagne_date,
                c.lieu AS campagne_lieu,
                c.objectif AS campagne_objectif,
                c.type_campagne,
                c.statut AS campagne_statut
            FROM participation p
            INNER JOIN campagne c ON p.id_campagne = c.id_campagne
            WHERE p.id_benevole = :id_benevole
            ORDER BY c.date ASC, p.date_inscription DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_benevole' => $idBenevole]);

        return $stmt->fetchAll();
    }

    /**
     * Récupère une participation par son ID avec les infos campagne et bénévole.
     *
     * @return array<string, mixed>|null
     */
    public function getById(int $idParticipation): ?array
    {
        $sql = "
            SELECT
                p.*,
                c.titre AS campagne_titre,
                c.date AS campagne_date,
                c.lieu AS campagne_lieu,
                c.capacite_max,
                c.statut AS campagne_statut,
                b.nom AS benevole_nom,
                b.prenom AS benevole_prenom,
                b.email AS benevole_email
            FROM participation p
            INNER JOIN campagne c ON p.id_campagne = c.id_campagne
            INNER JOIN benevole b ON p.id_benevole = b.id_benevole
            WHERE p.id_participation = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $idParticipation]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Compte les inscriptions actives (non annulées) pour une campagne.
     */
    public function countActiveByCampagne(int $idCampagne): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM participation
            WHERE id_campagne = :id_campagne AND statut != 'Annulee'
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_campagne' => $idCampagne]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Vérifie si une participation existe déjà pour ce couple (id_campagne, id_benevole).
     *
     * @return array<string, mixed>|null
     */
    public function findByCampagneAndBenevole(int $idCampagne, int $idBenevole): ?array
    {
        $sql = "
            SELECT *
            FROM participation
            WHERE id_campagne = :id_campagne AND id_benevole = :id_benevole
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_campagne' => $idCampagne,
            ':id_benevole' => $idBenevole,
        ]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Règle métier avancée : détecte si un bénévole est déjà inscrit à une autre
     * campagne se déroulant à la même date (hors inscriptions annulées).
     *
     * @return array<string, mixed>|null
     */
    public function findSameDateConflict(int $idBenevole, string $date, int $excludeCampagneId = 0): ?array
    {
        $sql = "
            SELECT p.id_participation, c.id_campagne, c.titre, c.date, c.lieu
            FROM participation p
            INNER JOIN campagne c ON p.id_campagne = c.id_campagne
            WHERE p.id_benevole = :id_benevole
              AND p.statut != 'Annulee'
              AND c.date = :date_campagne
              AND c.id_campagne != :exclude_id
              AND c.statut != 'Annulee'
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_benevole'   => $idBenevole,
            ':date_campagne' => $date,
            ':exclude_id'    => $excludeCampagneId,
        ]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Enregistre une nouvelle participation ou réactive une participation annulée.
     */
    public function create(Participation $participation): int
    {
        $existing = $this->findByCampagneAndBenevole(
            $participation->getIdCampagne(),
            $participation->getIdBenevole()
        );

        if ($existing !== null && $existing['statut'] === 'Annulee') {
            $sql = "
                UPDATE participation
                SET
                    date_inscription = :date_inscription,
                    statut = :statut,
                    commentaire = :commentaire
                WHERE id_participation = :id_participation
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':date_inscription' => $participation->getDateInscription(),
                ':statut'           => $participation->getStatut(),
                ':commentaire'      => $participation->getCommentaire(),
                ':id_participation' => (int) $existing['id_participation'],
            ]);
            $id = (int) $existing['id_participation'];
            $participation->setIdParticipation($id);

            return $id;
        }

        $sql = "
            INSERT INTO participation (id_campagne, id_benevole, date_inscription, statut, commentaire)
            VALUES (:id_campagne, :id_benevole, :date_inscription, :statut, :commentaire)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_campagne'      => $participation->getIdCampagne(),
            ':id_benevole'      => $participation->getIdBenevole(),
            ':date_inscription' => $participation->getDateInscription(),
            ':statut'           => $participation->getStatut(),
            ':commentaire'      => $participation->getCommentaire(),
        ]);

        $newId = (int) $this->pdo->lastInsertId();
        $participation->setIdParticipation($newId);

        return $newId;
    }

    /**
     * Met à jour le statut d'une participation (En attente / Confirmee / Annulee).
     */
    public function updateStatut(int $idParticipation, string $statut): bool
    {
        if (!in_array($statut, Participation::STATUTS_VALIDES, true)) {
            return false;
        }

        $stmt = $this->pdo->prepare('UPDATE participation SET statut = :statut WHERE id_participation = :id');
        return $stmt->execute([
            ':statut' => $statut,
            ':id'     => $idParticipation,
        ]);
    }

    /**
     * Supprime définitivement une participation.
     */
    public function delete(int $idParticipation): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM participation WHERE id_participation = :id');
        return $stmt->execute([':id' => $idParticipation]);
    }
}
