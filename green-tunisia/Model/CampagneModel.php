<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Campagne.php';

/**
 * Classe CampagneModel (DAO / Repository POO)
 * Gère les opérations CRUD sur les campagnes et les jointures avec participation via PDO.
 */
class CampagneModel
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Liste toutes les campagnes avec le nombre de participants actifs (Jointure LEFT JOIN).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(
        ?string $search = null,
        ?string $type = null,
        ?string $statut = null,
        bool $onlyAvailable = false
    ): array {
        $sql = "
            SELECT
                c.id_campagne,
                c.titre,
                c.description,
                c.date,
                c.lieu,
                c.objectif,
                c.type_campagne,
                c.capacite_max,
                c.statut,
                c.created_at,
                COUNT(CASE WHEN p.statut != 'Annulee' THEN 1 END) AS nb_participants,
                COUNT(CASE WHEN p.statut = 'En attente' THEN 1 END) AS nb_en_attente
            FROM campagne c
            LEFT JOIN participation p ON c.id_campagne = p.id_campagne
            WHERE 1 = 1
        ";

        $params = [];

        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $sql .= " AND (c.titre LIKE :search1 OR c.lieu LIKE :search2 OR c.objectif LIKE :search3 OR c.description LIKE :search4)";
            $params[':search1'] = $term;
            $params[':search2'] = $term;
            $params[':search3'] = $term;
            $params[':search4'] = $term;
        }

        if ($type !== null && $type !== '' && in_array($type, Campagne::TYPES_VALIDES, true)) {
            $sql .= " AND c.type_campagne = :type";
            $params[':type'] = $type;
        }

        if ($statut !== null && $statut !== '' && in_array($statut, Campagne::STATUTS_VALIDES, true)) {
            $sql .= " AND c.statut = :statut";
            $params[':statut'] = $statut;
        }

        if ($onlyAvailable) {
            $sql .= " AND c.date >= :today AND c.statut IN ('Planifiee', 'En cours')";
            $params[':today'] = date('Y-m-d');
        }

        $sql .= "
            GROUP BY
                c.id_campagne, c.titre, c.description, c.date, c.lieu,
                c.objectif, c.type_campagne, c.capacite_max, c.statut, c.created_at
            ORDER BY c.date ASC, c.id_campagne DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $row['nb_participants'] = (int) $row['nb_participants'];
            $row['nb_en_attente'] = (int) $row['nb_en_attente'];
            $row['capacite_max'] = (int) $row['capacite_max'];
            $row['places_restantes'] = max(0, $row['capacite_max'] - $row['nb_participants']);
        }
        unset($row);

        return $rows;
    }

    /**
     * Récupère une campagne par son ID avec le nombre de participants (Jointure LEFT JOIN).
     *
     * @return array<string, mixed>|null
     */
    public function getById(int $idCampagne): ?array
    {
        $sql = "
            SELECT
                c.id_campagne,
                c.titre,
                c.description,
                c.date,
                c.lieu,
                c.objectif,
                c.type_campagne,
                c.capacite_max,
                c.statut,
                c.created_at,
                COUNT(CASE WHEN p.statut != 'Annulee' THEN 1 END) AS nb_participants,
                COUNT(CASE WHEN p.statut = 'En attente' THEN 1 END) AS nb_en_attente
            FROM campagne c
            LEFT JOIN participation p ON c.id_campagne = p.id_campagne
            WHERE c.id_campagne = :id
            GROUP BY
                c.id_campagne, c.titre, c.description, c.date, c.lieu,
                c.objectif, c.type_campagne, c.capacite_max, c.statut, c.created_at
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $idCampagne]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $row['nb_participants'] = (int) $row['nb_participants'];
        $row['nb_en_attente'] = (int) $row['nb_en_attente'];
        $row['capacite_max'] = (int) $row['capacite_max'];
        $row['places_restantes'] = max(0, $row['capacite_max'] - $row['nb_participants']);

        return $row;
    }

    /**
     * Crée une nouvelle campagne en base via PDO.
     */
    public function create(Campagne $campagne): int
    {
        $sql = "
            INSERT INTO campagne (titre, description, date, lieu, objectif, type_campagne, capacite_max, statut)
            VALUES (:titre, :description, :date, :lieu, :objectif, :type_campagne, :capacite_max, :statut)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':titre'         => $campagne->getTitre(),
            ':description'   => $campagne->getDescription(),
            ':date'          => $campagne->getDate(),
            ':lieu'          => $campagne->getLieu(),
            ':objectif'      => $campagne->getObjectif(),
            ':type_campagne' => $campagne->getTypeCampagne(),
            ':capacite_max'  => $campagne->getCapaciteMax(),
            ':statut'        => $campagne->getStatut(),
        ]);

        $newId = (int) $this->pdo->lastInsertId();
        $campagne->setIdCampagne($newId);

        return $newId;
    }

    /**
     * Met à jour une campagne existante via PDO.
     */
    public function update(Campagne $campagne): bool
    {
        $sql = "
            UPDATE campagne
            SET
                titre = :titre,
                description = :description,
                date = :date,
                lieu = :lieu,
                objectif = :objectif,
                type_campagne = :type_campagne,
                capacite_max = :capacite_max,
                statut = :statut
            WHERE id_campagne = :id_campagne
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titre'         => $campagne->getTitre(),
            ':description'   => $campagne->getDescription(),
            ':date'          => $campagne->getDate(),
            ':lieu'          => $campagne->getLieu(),
            ':objectif'      => $campagne->getObjectif(),
            ':type_campagne' => $campagne->getTypeCampagne(),
            ':capacite_max'  => $campagne->getCapaciteMax(),
            ':statut'        => $campagne->getStatut(),
            ':id_campagne'   => $campagne->getIdCampagne(),
        ]);
    }

    /**
     * Supprime une campagne via PDO (cascade sur les participations liées).
     */
    public function delete(int $idCampagne): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM campagne WHERE id_campagne = :id');
        return $stmt->execute([':id' => $idCampagne]);
    }

    /**
     * Retourne les statistiques globales pour le tableau de bord Back Office.
     *
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        $today = date('Y-m-d');

        $totalCampagnes = (int) $this->pdo->query('SELECT COUNT(*) FROM campagne')->fetchColumn();

        $stmtActives = $this->pdo->prepare(
            "SELECT COUNT(*) FROM campagne WHERE date >= :today AND statut IN ('Planifiee', 'En cours')"
        );
        $stmtActives->execute([':today' => $today]);
        $campagnesActives = (int) $stmtActives->fetchColumn();

        $totalParticipations = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM participation WHERE statut != 'Annulee'")
            ->fetchColumn();

        $enAttente = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM participation WHERE statut = 'En attente'")
            ->fetchColumn();

        $capaciteTotale = (int) $this->pdo
            ->query("SELECT COALESCE(SUM(capacite_max), 0) FROM campagne WHERE statut != 'Annulee'")
            ->fetchColumn();

        $tauxRemplissage = $capaciteTotale > 0
            ? (int) min(100, round(($totalParticipations / $capaciteTotale) * 100))
            : 0;

        $parTypeStmt = $this->pdo->query("
            SELECT
                c.type_campagne,
                COUNT(DISTINCT c.id_campagne) AS total_campagnes,
                COUNT(CASE WHEN p.statut != 'Annulee' THEN 1 END) AS total_participants
            FROM campagne c
            LEFT JOIN participation p ON c.id_campagne = p.id_campagne
            GROUP BY c.type_campagne
        ");

        $repartition = [
            'Nettoyage'       => ['campagnes' => 0, 'participants' => 0],
            'Plantation'      => ['campagnes' => 0, 'participants' => 0],
            'Sensibilisation' => ['campagnes' => 0, 'participants' => 0],
        ];

        foreach ($parTypeStmt->fetchAll() as $row) {
            $type = $row['type_campagne'];
            if (isset($repartition[$type])) {
                $repartition[$type] = [
                    'campagnes'    => (int) $row['total_campagnes'],
                    'participants' => (int) $row['total_participants'],
                ];
            }
        }

        return [
            'total_campagnes'      => $totalCampagnes,
            'campagnes_actives'    => $campagnesActives,
            'total_participations' => $totalParticipations,
            'en_attente'           => $enAttente,
            'capacite_totale'      => $capaciteTotale,
            'taux_remplissage'     => $tauxRemplissage,
            'repartition'          => $repartition,
        ];
    }
}
