<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <button id="btn-create-permintaan" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Permintaan
        Baru</button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode Permintaan</th>
                        <th>Tanggal</th>
                        <th>Pemohon</th>
                        <th>Jumlah Item</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($permintaan)) : ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data permintaan.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($permintaan as $item) : ?>
                            <tr>
                                <td><?php echo e($item['kode_permintaan']); ?></td>
                                <td><?php echo e(date('d M Y', strtotime($item['tanggal_permintaan']))); ?></td>
                                <td><?php echo e($item['nama_pemohon']); ?></td>
                                <td><?php echo e($item['jumlah_item']); ?></td>
                                <td>
                                    <?php

                                    $status_class = 'bg-secondary';
                                    if ($item['status_permintaan'] == 'Disetujui') $status_class = 'bg-success';
                                    if ($item['status_permintaan'] == 'Ditolak') $status_class = 'bg-danger';
                                    if ($item['status_permintaan'] == 'Diajukan') $status_class = 'bg-warning text-dark';
                                    ?>
                                    <span
                                        class="badge <?php echo $status_class; ?>"><?php echo e($item['status_permintaan']); ?></span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Membuat Permintaan -->
<div class="modal fade" id="permintaan-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Permintaan Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-permintaan">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="catatan_pemohon" class="form-label">Catatan / Keperluan</label>
                        <textarea class="form-control" id="catatan_pemohon" name="catatan_pemohon" rows="3"
                            required></textarea>
                    </div>
                    <hr>
                    <h6>Item Barang yang Diminta</h6>
                    <div id="item-list">
                        <!-- Baris item akan ditambahkan oleh JavaScript -->
                    </div>
                    <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-success mt-2"><i
                            class="bi bi-plus"></i> Tambah Baris</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Ajukan Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template untuk baris item baru (disembunyikan) -->
<template id="item-row-template">
    <div class="row mb-2 item-row align-items-center">
        <div class="col-md-7">
            <select class="form-select item-barang" name="items[][id_barang]" required>
                <option value="">-- Pilih Barang --</option>
                <?php foreach ($barang_list as $barang) : ?>
                    <option value="<?php echo e($barang['id_barang']); ?>"><?php echo e($barang['nama_barang']); ?> (Stok:
                        <?php echo e($barang['stok_saat_ini']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control item-jumlah" name="items[][jumlah]" placeholder="Jumlah" min="1"
                required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i></button>
        </div>
    </div>
</template>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>