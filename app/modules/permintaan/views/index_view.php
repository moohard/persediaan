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
                        <th>Tipe</th>
                        <th>Pemohon</th>
                        <th>Jumlah Item</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="permintaan-table-body">
                    <!-- Data dimuat oleh AJAX -->
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
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_pembelian">
                        <label class="form-check-label" for="is_pembelian">Ajukan sebagai Permintaan Pembelian (untuk
                            barang baru/habis)</label>
                    </div>
                    <div class="mb-3">
                        <label for="catatan_pemohon" class="form-label">Catatan / Keperluan</label>
                        <textarea class="form-control" id="catatan_pemohon" name="catatan_pemohon" rows="3"
                            required></textarea>
                    </div>
                    <hr>
                    <h6>Item Barang yang Diminta</h6>
                    <div id="item-list"></div>
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

<!-- Modal untuk Detail Permintaan -->
<div class="modal fade" id="detail-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detail-modal-body"></div>
            <div class="modal-footer" id="detail-modal-footer"></div>
        </div>
    </div>
</div>

<template id="item-row-template">
    <div class="row mb-2 item-row align-items-center">
        <div class="col-md-5">
            <select class="form-select item-barang" required>
                <option value="">-- Pilih Barang --</option>
                <?php foreach ($barang_list as $barang): ?>
                <option value="<?php echo e($barang['id_barang']); ?>"
                    data-stok="<?php echo e($barang['stok_saat_ini']); ?>"><?php echo e($barang['nama_barang']); ?>
                    (Stok: <?php echo e($barang['stok_saat_ini']); ?>)</option>
                <?php endforeach; ?>
            </select>
            <input type="text" class="form-control item-barang-custom d-none" placeholder="Nama Barang Baru" required>
        </div>
        <div class="col-md-3">
            <input type="number" class="form-control item-jumlah" placeholder="Jumlah" min="1" required>
        </div>
        <div class="col-md-2">
            <div class="form-check">
                <input class="form-check-input item-is-custom" type="checkbox">
                <label class="form-check-label">Baru</label>
            </div>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove-item"><i class="bi bi-trash"></i></button>
        </div>
    </div>
</template>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>