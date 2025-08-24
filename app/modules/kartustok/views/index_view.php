<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<h3><?php echo e($title); ?></h3>
<p>
    <strong>Kode Barang:</strong> <?php echo e($barang['kode_barang']); ?> |
    <strong>Stok Saat Ini:</strong> <span
        class="badge bg-primary fs-6"><?php echo e($barang['stok_saat_ini']); ?></span>
</p>

<div class="card">
    <div class="card-body">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis Transaksi</th>
                    <th>Perubahan</th>
                    <th>Stok Sebelum</th>
                    <th>Stok Sesudah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td><?php echo e(date('d M Y, H:i', strtotime($log['tanggal_log']))); ?></td>
                        <td><?php echo e(ucwords($log['jenis_transaksi'])); ?></td>
                        <td>
                            <?php if ($log['jumlah_ubah'] > 0) : ?>
                                <span class="text-success fw-bold">+<?php echo e($log['jumlah_ubah']); ?></span>
                            <?php else : ?>
                                <span class="text-danger fw-bold"><?php echo e($log['jumlah_ubah']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($log['stok_sebelum']); ?></td>
                        <td><?php echo e($log['stok_sesudah']); ?></td>
                        <td><?php echo e($log['keterangan']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    <a href="/barang" class="btn btn-secondary">Kembali ke Daftar Barang</a>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>