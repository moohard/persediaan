<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <div>
        <a href="/barang/trash" class="btn btn-outline-secondary"><i class="bi bi-trash3"></i> Lihat Sampah</a>
        <button class="btn btn-primary" id="btn-add-barang"><i class="bi bi-plus-circle"></i> Tambah Barang</button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Daftar Barang Aktif
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-center">Total Stok</th>
                        <th>Detail Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="barang-table-body">
                    <!-- Data dimuat oleh AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="barang-modal" tabindex="-1" aria-labelledby="barang-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barang-modal-label">Form Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="barang-form">
                    <input type="hidden" name="id_barang_encrypted" id="id_barang_encrypted">
                    <div class="mb-3">
                        <label for="kode_barang" class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                            <option value="">Pilih Kategori...</option>
                            <!-- Opsi dimuat oleh AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_satuan" class="form-label">Satuan</label>
                        <select class="form-select" id="id_satuan" name="id_satuan" required>
                            <option value="">Pilih Satuan...</option>
                            <!-- Opsi dimuat oleh AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_barang" class="form-label">Jenis Barang</label>
                        <select class="form-select" id="jenis_barang" name="jenis_barang" required>
                            <option value="habis_pakai">Barang Habis Pakai</option>
                            <option value="aset">Aset</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-barang">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>