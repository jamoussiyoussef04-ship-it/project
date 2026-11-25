<?php
$pageTitle = 'Tableau de bord';
$activePage = 'dashboard';
require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/layout/navbar.php'; ?>
<main class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div><h1 class="page-title">Tableau de bord</h1><p class="text-muted">Vue globale de GREEN TUNISIA</p></div>
    </div>
    <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <div class="row g-3 mb-4">
        <?php
        $cards = [
            ['Utilisateurs', $stats['users'], 'bi-people-fill', '#14532D'],
            ['Bénévoles', $stats['volunteers'], 'bi-person-heart', '#86EFAC'],
            ['Campagnes', $stats['campaigns'], 'bi-tree-fill', '#14532D'],
            ['Dons', $stats['donations'], 'bi-cash-coin', '#FACC15'],
            ['Montant total', number_format($stats['donation_total'], 2, ',', ' ') . ' TND', 'bi-wallet2', '#14532D'],
            ['Participations', $stats['participations'], 'bi-calendar-check-fill', '#86EFAC'],
        ];
        foreach ($cards as [$label, $value, $icon, $color]):
        ?>
        <div class="col-sm-6 col-xl-4">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div><div class="text-muted mb-1"><?= e($label) ?></div><div class="h3 mb-0"><?= e($value) ?></div></div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:<?= e($color) ?>20;color:<?= e($color) ?>">
                        <i class="bi <?= e($icon) ?> fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100"><div class="card-body">
                <h2 class="card-title h5">Évolution des dons</h2>
                <div id="donations-chart"></div>
            </div></div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100"><div class="card-body">
                <h2 class="card-title h5">Participations par campagne</h2>
                <div id="participations-chart"></div>
            </div></div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card"><div class="card-body">
                <h2 class="card-title h5 mb-3">Dernières activités</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Utilisateur</th><th>Action</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php foreach ($activities as $activity): ?>
                        <tr><td><?= e($activity['nom']) ?></td><td><?= e($activity['action']) ?></td><td><?= e(date('d/m/Y H:i', strtotime($activity['date_action']))) ?></td></tr>
                        <?php endforeach; ?>
                        <?php if (!$activities): ?><tr><td colspan="3" class="text-center text-muted">Aucune activité</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div></div>
        </div>
        <div class="col-lg-5">
            <div class="card"><div class="card-body">
                <h2 class="card-title h5 mb-3">Derniers utilisateurs</h2>
                <div class="list-group list-group-flush">
                <?php foreach ($recentUsers as $user): ?>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between" href="/?page=user_show&id=<?= (int) $user['id_user'] ?>">
                        <span><strong><?= e($user['nom']) ?></strong><br><small class="text-muted"><?= e($user['email']) ?></small></span>
                        <span class="badge bg-success align-self-center"><?= e($user['nom_role']) ?></span>
                    </a>
                <?php endforeach; ?>
                <?php if (!$recentUsers): ?><div class="text-muted">Aucun utilisateur</div><?php endif; ?>
                </div>
            </div></div>
        </div>
    </div>
</main>
</div>
<script>
window.greenTunisiaDashboard = {
    donations: <?= json_encode(array_map(fn($r) => (float) $r['total'], $donationsByMonth), JSON_UNESCAPED_UNICODE) ?>,
    donationLabels: <?= json_encode(array_map(fn($r) => $r['period'], $donationsByMonth), JSON_UNESCAPED_UNICODE) ?>,
    participations: <?= json_encode(array_map(fn($r) => (int) $r['total'], $participationsByCampaign), JSON_UNESCAPED_UNICODE) ?>,
    participationLabels: <?= json_encode(array_map(fn($r) => $r['titre'], $participationsByCampaign), JSON_UNESCAPED_UNICODE) ?>
};
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const data = window.greenTunisiaDashboard;
    if (document.querySelector('#donations-chart')) {
        new ApexCharts(document.querySelector('#donations-chart'), {
            chart: {type: 'area', height: 280, toolbar: {show: false}},
            colors: ['#14532D'], series: [{name: 'Dons (TND)', data: data.donations}],
            categories: data.donationLabels, xaxis: {categories: data.donationLabels},
            stroke: {curve: 'smooth', width: 3}, dataLabels: {enabled: false},
            fill: {colors: ['#86EFAC'], type: 'gradient', gradient: {opacityFrom: .45, opacityTo: .05}}
        }).render();
    }
    if (document.querySelector('#participations-chart')) {
        new ApexCharts(document.querySelector('#participations-chart'), {
            chart: {type: 'donut', height: 280},
            colors: ['#14532D', '#86EFAC', '#FACC15', '#22C55E', '#4ADE80'],
            labels: data.participationLabels, series: data.participations,
            legend: {position: 'bottom'}
        }).render();
    }
});
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>
