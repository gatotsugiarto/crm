<?php

/** @var yii\web\View $this */

$this->title = 'Payroll Dashboard';
?>

<?php
$labels_1 = [];
$dataAmount = [];

foreach ($bebanPerusahaan as $row) {
    $labels_1[] = $row['period_code'];
    $dataAmount_1[] = (float)$row['amount'];
}
?>

<?php
// Siapkan data untuk Chart.js
$labels_2 = [];
$dataProbation = [];
$dataPKWT = [];
$dataPermanent = [];

foreach ($statusPegawai as $row) {
    if (!in_array($row['period_code'], $labels_2)) {
        $labels_2[] = $row['period_code'];
    }
    switch ($row['label']) {
        case 'Probation':
            $dataProbation[] = (float)$row['amount'];
            break;
        case 'PKWT':
            $dataPKWT[] = (float)$row['amount'];
            break;
        case 'Permanent':
            $dataPermanent[] = (float)$row['amount'];
            break;
    }
}
?>

<?php
$labels_3 = [];
$dataAmount_3 = [];

foreach ($overtime as $row) {
    $labels_3[] = $row['period_code'];
    $dataAmount_3[] = (float)$row['amount'];
}
?>

<?php
$labels_4 = [];
$dataAmount_4 = [];

foreach ($thp as $row) {
    $labels_4[] = $row['period_code'];
    $dataAmount_4[] = (float)$row['amount'];
}
?>

<?php
$labels_5 = [];
$dataAmount_5 = [];

foreach ($late as $row) {
    $labels_5[] = $row['period_code'];
    $dataAmount_5[] = (float)$row['amount'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="site-index">
    <div class="content">
        <div class="container-fluid">
                    
            <div class="row">
                <!-- Beban Perusahaan -->
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Employer Payroll Cost</h4>
                            <p class="card-category">Summary of employer payroll costs</p>
                        </div>
                        <div class="card-body ">
                            <canvas id="bebanPerusahaanChart"></canvas>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <!-- <div class="stats">
                                <i class="fa fa-history"></i> Updated 3 minutes ago
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- Status Pegawai -->
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Employee Status</h4>
                            <p class="card-category">Employee status overview</p>
                        </div>
                        <div class="card-body ">
                            <canvas id="statusPegawaiChart"></canvas>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <!-- <div class="stats">
                                <i class="fa fa-history"></i> Updated 3 minutes ago
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Overtime -->
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Overtime</h4>
                            <p class="card-category">Overtime hours and compensation overview</p>
                        </div>
                        <div class="card-body ">
                            <canvas id="overtimeChart"></canvas>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <!-- <div class="stats">
                                <i class="fa fa-history"></i> Updated 3 minutes ago
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- THP -->
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Take Home Pay</h4>
                            <p class="card-category">Final salary after deductions</p>
                        </div>
                        <div class="card-body ">
                            <canvas id="thpChart"></canvas>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <!-- <div class="stats">
                                <i class="fa fa-history"></i> Updated 3 minutes ago
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Potongan Keterlambatan -->
                <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Attendance Deduction</h4>
                            <p class="card-category">Deduction due to late attendance</p>
                        </div>
                        <div class="card-body ">
                            <canvas id="lateChart"></canvas>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <!-- <div class="stats">
                                <i class="fa fa-history"></i> Updated 3 minutes ago
                            </div> -->
                        </div>
                    </div>
                </div>

                <!-- THP -->
                <!-- <div class="col-md-6">
                    <div class="card ">
                        <div class="card-header ">
                            <h4 class="card-title">Take Home Pay</h4>
                            <p class="card-category">Final salary after deductions</p>
                        </div>
                        <div class="card-body ">
                            <canvas id="thpChart"></canvas>
                        </div>
                        <div class="card-footer ">
                            <hr>
                            <div class="stats">
                                <i class="fa fa-history"></i> Updated 3 minutes ago
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

        </div>
    </div>
</div>
<?php if(isset($dataAmount_1)){ ?>
<script>
const ctx_1 = document.getElementById('bebanPerusahaanChart').getContext('2d');
const bebanPerusahaanChart = new Chart(ctx_1, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_1) ?>,
        datasets: [{
            label: 'Employer Payroll Cost',
            data: <?= json_encode($dataAmount_1) ?>,
            borderColor: '#ff8d72',
            backgroundColor: 'rgba(255,141,114,0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php } ?>

<script>
const ctx = document.getElementById('statusPegawaiChart').getContext('2d');
const statusPegawaiChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels_2) ?>,
        datasets: [
            {
                label: 'Probation',
                data: <?= json_encode($dataProbation) ?>,
                backgroundColor: '#1d8cf8'
            },
            {
                label: 'PKWT',
                data: <?= json_encode($dataPKWT) ?>,
                backgroundColor: '#e14eca'
            },
            {
                label: 'Permanent',
                data: <?= json_encode($dataPermanent) ?>,
                backgroundColor: '#00f2c3'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php if(isset($dataAmount_3)){ ?>
<script>
const ctx_3 = document.getElementById('overtimeChart').getContext('2d');
const overtimeChart = new Chart(ctx_3, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_3) ?>,
        datasets: [{
            label: 'Employer Overtime',
            data: <?= json_encode($dataAmount_3) ?>,
            borderColor: '#1d8cf8',
            backgroundColor: 'rgba(29,140,248,0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php } ?>
<?php if(isset($dataAmount_4)){ ?>
<script>
const ctx_4 = document.getElementById('thpChart').getContext('2d');
const thpChart = new Chart(ctx_4, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_4) ?>,
        datasets: [{
            label: 'Employee Net Pay Summary',
            data: <?= json_encode($dataAmount_4) ?>,
            borderColor: '#00f2c3',
            backgroundColor: 'rgba(0,242,195,0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php } ?>
<?php if(isset($dataAmount_5)){ ?>
<script>
const ctx_5 = document.getElementById('lateChart').getContext('2d');
const lateChart = new Chart(ctx_5, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels_5) ?>,
        datasets: [{
            label: 'Deduction for Late Arrival',
            data: <?= json_encode($dataAmount_5) ?>,
            borderColor: '#e14eca',
            backgroundColor: 'rgba(225,78,202,0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
<?php } ?>
