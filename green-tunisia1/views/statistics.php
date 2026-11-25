<?php
$pageTitle = 'Statistiques';
$activePage = 'statistics';
require __DIR__ . '/layout/header.php';
require __DIR__ . '/layout/sidebar.php';
?>
<div class="main-wrapper">
<?php require __DIR__ . '/layout/navbar.php'; ?>
<main class="content-wrapper">
    <div class="page-header"><h1 class="page-title">Statistiques globales</h1></div>
    <div class="row g-3 mb-4">
        <?php
        $cards = [
            ['Bénévoles', $stats['volunteers'], 'bi-person-heart'],
            ['Campagnes', $stats['campaigns'], 'bi-tree-fill'],
            ['Dons', $stats['donations'], 'bi-cash-coin'],
            ['Montant total', number_format($stats['donation_total'], 2, ',', ' ') . ' TND', 'bi-wallet2'],
            ['Participations', $stats['participations'], 'bi-calendar-check-fill'],
        ];
        foreach ($cards as [$label, $value, $icon]):
        ?>
        <div class="col-sm-6 col-xl-4"><div class="card h-100"><div class="card-body d-flex align-items-center justify-content-between"><div><div class="text-muted"><?= e($label) ?></div><div class="h3 mb-0"><?= e($value) ?></div></div><i class="bi <?= e($icon) ?> fs-2 text-success"></i></div></div></div>
        <?php endforeach; ?>
    </div>
    <div class="row g-3">
        <div class="col-lg-7"><div class="card h-100"><div class="card-body"><h2 class="h5">Dons par mois</h2><div id="statistics-donations"></div></div></div></div>
        <div class="col-lg-5"><div class="card h-100"><div class="card-body"><h2 class="h5">Répartition des participations</h2><div id="statistics-participations"></div></div></div></div>
    </div>
</main>
</div>
<script>
window.greenTunisiaStatistics = {
    donationLabels: <?= json_encode(array_map(fn($r) => $r['period'], $donationsByMonth), JSON_UNESCAPED_UNICODE) ?>,
    donations: <?= json_encode(array_map(fn($r) => (float) $r['total'], $donationsByMonth), JSON_UNESCAPED_UNICODE) ?>,
    participationLabels: <?= json_encode(array_map(fn($r) => $r['titre'], $participationsByCampaign), JSON_UNESCAPED_UNICODE) ?>,
    participations: <?= json_encode(array_map(fn($r) => (int) $r['total'], $participationsByCampaign), JSON_UNESCAPED_UNICODE) ?>
};
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const data = window.greenTunisiaStatistics;
    new ApexCharts(document.querySelector('#statistics-donations'), {
        chart: {type: 'bar', height: 320, toolbar: {show: false}},
        colors: ['#14532D'], series: [{name: 'Dons (TND)', data: data.donations}],
        xaxis: {categories: data.donationLabels}, dataLabels: {enabled: false}
    }).render();
    new ApexCharts(document.querySelector('#statistics-participations'), {
        chart: {type: 'donut', height: 320},
        colors: ['#14532D', '#86EFAC', '#FACC15', '#22C55E', '#4ADE80'],
        labels: data.participationLabels, series: data.participations,
        legend: {position: 'bottom'}
    }).render();
});
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>
