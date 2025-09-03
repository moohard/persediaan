<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<h3><?php echo e($title); ?></h3>

<!-- Konfigurasi Sistem -->
<div class="card mt-3">
    <div class="card-header">
        Konfigurasi Sistem
    </div>
    <div class="card-body">
        <form id="pengaturan-form">
            <div id="settings-container">
                <p class="text-center text-muted">Memuat pengaturan...</p>
            </div>
        </form>
    </div>
    <div class="card-footer text-end">
        <?php if (has_permission('pengaturan_update')) : ?>
            <button class="btn btn-primary" id="btn-save-settings">
                <i class="bi bi-save"></i> Simpan Pengaturan
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Tindakan Berbahaya -->
<div class="card border-danger mt-4">
    <div class="card-header bg-danger text-white">
        Tindakan Berbahaya
    </div>
    <div class="card-body">
        <h5 class="card-title">Hapus Semua Data Transaksi</h5>
        <p class="card-text">
            Tindakan ini akan menghapus semua data yang berkaitan dengan alur kerja persediaan.
        <ul class="mb-3">
            <li>Semua riwayat permintaan, penerimaan, pengeluaran, dan stock opname akan hilang.</li>
            <li>Stok semua barang akan di-reset menjadi 0.</li>
        </ul>
        Data master seperti daftar barang, pengguna, dan peran tidak akan terhapus.
        <strong>Tindakan ini tidak dapat dibatalkan.</strong>
        </p>
        <?php if (has_permission('pengaturan_clear_transactions')) : ?>
            <button class="btn btn-danger" id="btn-clear-transactions">
                <i class="bi bi-exclamation-triangle-fill"></i> Kosongkan Data Transaksi
            </button>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>