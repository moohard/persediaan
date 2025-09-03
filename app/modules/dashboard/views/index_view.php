<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
</div>

<?php if (has_permission('dashboard_view_stats')) : ?>
    <div class="row" id="summary-cards">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Jenis Barang</h5>
                    <p class="card-text fs-3" id="total-barang">...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Permintaan Bulan Ini</h5>
                    <p class="card-text fs-3" id="permintaan-bulan-ini">...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Stok Kritis </h5>
                            <p class="card-text fs-3" id="stok-kritis">...</p>
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
<?php else : ?>
    <div class="alert alert-info">
        Selamat datang di Sistem Persediaan ATK.
    </div>
<?php endif; ?>

<!-- Memuat Chart.js langsung di view -->
<script src="<?php echo BASE_URL; ?>/js/plugins/chart.js"></script>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>