<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <div>
        <a href="/barang/trash" class="btn btn-outline-secondary"><i class="bi bi-trash3"></i> Lihat Sampah</a>
        <button id="btn-add-barang" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah Barang</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
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
                <tbody id="barang-table-body">
                    <!-- Data akan dimuat oleh AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Edit Barang -->
<div class="modal fade" id="barang-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Form Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="barang-form">
                <div class="modal-body">
                    <input type="hidden" name="id" id="barang-id">
                    <div class="mb-3">
                        <label for="kode_barang" class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_barang" class="form-label">Jenis Barang</label>
                        <select class="form-select" id="jenis_barang" name="jenis_barang" required>
                            <option value="habis_pakai">Barang Habis Pakai</option>
                            <option value="aset">Aset</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>