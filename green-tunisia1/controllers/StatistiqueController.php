<?php

declare(strict_types=1);

class StatistiqueController
{
    public function __construct(
        private PDO $pdo,
        private Utilisateur $utilisateurs,
        private Activite $activites
    ) {}

    public function count(string $table): int
    {
        $allowed = [
            'Utilisateur' => 'Utilisateur',
            'Benevole' => 'Benevole',
            'Campagne' => 'Campagne',
            'Participation' => 'Participation',
        ];
        if (!isset($allowed[$table])) {
            throw new InvalidArgumentException('Table non autorisée');
        }
        return (int) $this->pdo->query('SELECT COUNT(*) FROM ' . $allowed[$table])->fetchColumn();
    }

    public function countDonations(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM `Don`')->fetchColumn();
    }

    public function totalDonations(): float
    {
        return (float) $this->pdo->query('SELECT COALESCE(SUM(montant), 0) FROM `Don`')->fetchColumn();
    }

    public function recentUsers(int $limit = 5): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id_user, u.nom, u.email, r.nom_role
             FROM Utilisateur u JOIN Role r ON r.id_role = u.id_role
             ORDER BY u.id_user DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function donationByMonth(): array
    {
        $stmt = $this->pdo->query(
            "SELECT DATE_FORMAT(date_don, '%Y-%m') AS period, SUM(montant) AS total
             FROM `Don`
             GROUP BY period
             ORDER BY period DESC
             LIMIT 12"
        );
        $rows = $stmt->fetchAll();
        return array_reverse($rows);
    }

    public function participationsByCampaign(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.titre, COUNT(p.id_participation) AS total
             FROM Campagne c LEFT JOIN Participation p ON p.id_campagne = c.id_campagne
             GROUP BY c.id_campagne, c.titre
             ORDER BY total DESC
             LIMIT 10'
        );
        return $stmt->fetchAll();
    }

    public function dashboard(): void
    {
        $stats = [
            'users' => $this->count('Utilisateur'),
            'volunteers' => $this->count('Benevole'),
            'campaigns' => $this->count('Campagne'),
            'donations' => $this->countDonations(),
            'donation_total' => $this->totalDonations(),
            'participations' => $this->count('Participation'),
        ];
        $activities = $this->activites->getRecentActivities(8);
        $recentUsers = $this->recentUsers(5);
        $donationsByMonth = $this->donationByMonth();
        $participationsByCampaign = $this->participationsByCampaign();
        require __DIR__ . '/../views/dashboard.php';
    }

    public function statistics(): void
    {
        $stats = [
            'users' => $this->count('Utilisateur'),
            'volunteers' => $this->count('Benevole'),
            'campaigns' => $this->count('Campagne'),
            'donations' => $this->countDonations(),
            'donation_total' => $this->totalDonations(),
            'participations' => $this->count('Participation'),
        ];
        $donationsByMonth = $this->donationByMonth();
        $participationsByCampaign = $this->participationsByCampaign();
        require __DIR__ . '/../views/statistics.php';
    }
}
