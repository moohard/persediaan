<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <?php if (has_permission('stock_opname_create')) : ?>
        <button class="btn btn-primary" id="btn-start-opname"><i class="bi bi-clipboard2-plus"></i> Mulai Stock Opname
            Baru</button>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">Riwayat Stock Opname</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Penanggung Jawab</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="history-table-body">
                    <!-- Data dimuat oleh AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Stock Opname -->
<div class="modal fade" id="opname-modal" tabindex="-1" aria-labelledby="opname-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="opname-modal-label">Form Stock Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="opname-form">
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" rows="2"></textarea>
                    </div>
                    <div class="table-responsive" style="max-height: 50vh;">
                        <table class="table table-bordered">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th rowspan="2" class="align-middle">Nama Barang</th>
                                    <th colspan="2" class="text-center">Stok Sistem</th>
                                    <th colspan="2" class="text-center">Stok Fisik</th>
                                    <th rowspan="2" class="align-middle">Catatan</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Umum</th>
                                    <th class="text-center">Perkara</th>
                                    <th class="text-center">Umum</th>
                                    <th class="text-center">Perkara</th>
                                </tr>
                            </thead>
                            <tbody id="opname-item-list"></tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-opname">Simpan Hasil Opname</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Stock Opname -->
<div class="modal fade" id="detail-opname-modal" tabindex="-1" aria-labelledby="detail-opname-modal-label"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail-opname-modal-label">Detail Hasil Stock Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detail-opname-info" class="mb-3"></div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="detail-opname-table">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle">Nama Barang</th>
                                <th colspan="2" class="text-center">Stok Sistem</th>
                                <th colspan="2" class="text-center">Stok Fisik</th>
                                <th colspan="2" class="text-center">Selisih</th>
                                <th rowspan="2" class="align-middle">Catatan</th>
                            </tr>
                            <tr>
                                <th class="text-center">Umum</th>
                                <th class="text-center">Perkara</th>
                                <th class="text-center">Umum</th>
                                <th class="text-center">Perkara</th>
                                <th class="text-center">Umum</th>
                                <th class="text-center">Perkara</th>
                            </tr>
                        </thead>
                        <tbody id="detail-opname-item-list"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <?php if (has_permission('stock_opname_print')) : ?>
                    <button type="button" class="btn btn-danger" id="btn-print-opname"><i
                            class="bi bi-file-earmark-pdf"></i> Cetak PDF</button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/js/plugins/jspdf.umd.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/plugins/jspdf.plugin.autotable.min.js"></script>
<?php require_once APP_PATH . '/views/templates/footer.php'; ?>