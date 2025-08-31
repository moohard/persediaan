<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Jenis Barang</h5>
                <p class="card-text fs-3">12</p> <!-- Data statis untuk contoh -->
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Permintaan Bulan Ini</h5>
                <p class="card-text fs-3">8</p> <!-- Data statis untuk contoh -->
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Stok Akan Habis</h5>
                <p class="card-text fs-3">2</p> <!-- Data statis untuk contoh -->
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Grafik Penggunaan Barang (6 Bulan Terakhir)
    </div>
    <div class="card-body" style="height: 300px;">
        <canvas id="usageChart"></canvas>
    </div>
</div>

<!-- Memuat Chart.js langsung di view, sebelum footer.php -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>