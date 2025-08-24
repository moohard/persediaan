<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <a href="/barang" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Tanggal Dihapus</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($barang)) : ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data di sampah.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($barang as $item) : ?>
                        <tr>
                            <td><?php echo e($item['nama_barang']); ?></td>
                            <td><span
                                    class="badge bg-secondary"><?php echo e(ucwords(str_replace('_', ' ', $item['jenis_barang']))); ?></span>
                            </td>
                            <td><?php echo e(date('d M Y, H:i', strtotime($item['deleted_at']))); ?></td>
                            <td>
                                <form action="/barang/restore" method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="id"
                                        value="<?php echo e($encryption->encrypt($item['id_barang'])); ?>">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>