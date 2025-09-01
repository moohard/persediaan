<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<h3><?php echo e($title); ?></h3>

<div class="card border-danger">
    <div class="card-header bg-danger text-white">
        Tindakan Berbahaya
    </div>
    <div class="card-body">
        <h5 class="card-title">Hapus Semua Data Transaksi</h5>
        <p class="card-text">
            Tindakan ini akan menghapus semua data yang berkaitan dengan alur kerja persediaan, termasuk:
        <ul class="mb-3">
            <li>Semua riwayat permintaan barang</li>
            <li>Semua riwayat penerimaan barang</li>
            <li>Semua riwayat pengeluaran barang</li>
            <li>Semua log pergerakan stok (Kartu Stok)</li>
        </ul>
        Stok semua barang akan di-reset menjadi 0. Data master seperti daftar barang dan pengguna tidak akan terhapus.
        <strong>Tindakan ini tidak dapat dibatalkan.</strong>
        </p>
        <?php if (has_permission('pengaturan_clear_transactions')) : ?>
            <button class="btn btn-danger" id="btn-clear-transactions">
                <i class="bi bi-exclamation-triangle-fill"></i> Kosongkan Data Transaksi Sekarang
            </button>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>