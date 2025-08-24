<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <a href="/barang/create" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Barang</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Stok</th>
                    <th style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($barang)) : ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data barang.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($barang as $item) : ?>
                        <tr>
                            <td><?php echo e($item['kode_barang']); ?></td>
                            <td><?php echo e($item['nama_barang']); ?></td>
                            <td><span
                                    class="badge bg-secondary"><?php echo e(ucwords(str_replace('_', ' ', $item['jenis_barang']))); ?></span>
                            </td>
                            <td><?php echo e($item['stok_saat_ini']); ?></td>
                            <td>
                                <a href="/kartustok/index/<?php echo e($encryption->encrypt($item['id_barang'])); ?>"
                                    class="btn btn-sm btn-success">
                                    <i class="bi bi-card-list"></i> Log
                                </a>
                                <a href="/barang/edit/<?php echo e($encryption->encrypt($item['id_barang'])); ?>"
                                    class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <!-- PERUBAHAN: Mengganti onsubmit dengan class untuk SweetAlert -->
                                <form action="/barang/destroy" method="POST" class="d-inline form-delete">
                                    <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="id"
                                        value="<?php echo e($encryption->encrypt($item['id_barang'])); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger btn-delete">
                                        <i class="bi bi-trash"></i> Hapus
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