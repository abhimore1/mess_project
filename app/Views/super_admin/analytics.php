<?php $pageTitle='Analytics & Reports'; ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="panel h-100">
            <div class="panel-header"><h6>Revenue Last 12 Months</h6></div>
            <div class="panel-body">
                <div id="revenueChart" style="height:300px"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="panel h-100">
            <div class="panel-header"><h6>Top 10 Messes by Revenue</h6></div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Mess Name</th><th>Active Students</th><th>Total Revenue</th></tr></thead>
                    <tbody>
                    <?php foreach($tenantStats as $ts): ?>
                    <tr>
                        <td class="fw-600"><?= e($ts['name']) ?></td>
                        <td><?= $ts['students'] ?></td>
                        <td class="text-success fw-700">₹<?= number_format($ts['revenue']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var monthlyData = <?= json_encode(array_reverse($monthly)) ?>;
    var cats = monthlyData.map(m => m.month);
    var data = monthlyData.map(m => parseFloat(m.total));
    
    var options = {
        series: [{ name: 'Revenue', data: data }],
        chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        colors: ['#06b6d4'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { categories: cats },
        yaxis: { labels: { formatter: function(val) { return '₹' + val.toLocaleString(); } } }
    };
    new ApexCharts(document.querySelector("#revenueChart"), options).render();
});
</script>
