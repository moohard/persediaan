<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <?php if (has_permission('permintaan_create')) : ?>
        <button class="btn btn-primary" id="btn-add-permintaan"><i class="bi bi-plus-circle"></i> Buat Permintaan</button>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        Daftar Permintaan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Pemohon</th>
                        <th>Tipe</th>
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

<!-- Modal Detail -->
<div class="modal fade" id="detail-modal" tabindex="-1" aria-labelledby="detail-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail-modal-label">Detail Permintaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detail-content"></div>
                <hr>
                <div id="approval-section" class="mt-3 d-none">
                    <h5>Proses Persetujuan</h5>
                    <div class="mb-3">
                        <label for="approval-catatan" class="form-label">Catatan Persetujuan/Penolakan</label>
                        <textarea class="form-control" id="approval-catatan" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="approval-buttons" class="d-none">
                    <button type="button" class="btn btn-danger" id="btn-reject">Tolak</button>
                    <button type="button" class="btn btn-success" id="btn-approve">Setujui</button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="form-modal" tabindex="-1" aria-labelledby="form-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="form-modal-label">Form Permintaan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="permintaan-form">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="tipe-permintaan-switch">
                        <label class="form-check-label" for="tipe-permintaan-switch">Ajukan sebagai Permintaan Pembelian
                            (untuk stok habis/barang baru)</label>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Baru?</th>
                                <th>Nama Barang</th>
                                <th style="width: 15%;">Jumlah</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="item-list">
                            <!-- Baris item akan ditambahkan di sini -->
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                        <i class="bi bi-plus"></i> Tambah Item
                    </button>
                    <hr>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan Permintaan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-permintaan">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>