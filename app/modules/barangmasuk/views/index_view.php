<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
</div>

<div class="card">
    <div class="card-header">
        Daftar Permintaan Pembelian (Siap Diterima)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Pemohon</th>
                        <th class="text-center">Jumlah Item</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="penerimaan-table-body">
                    <!-- Data dimuat oleh AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Proses Penerimaan -->
<div class="modal fade" id="receipt-modal" tabindex="-1" aria-labelledby="receipt-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receipt-modal-label">Proses Penerimaan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Alokasikan jumlah barang yang diterima ke lokasi penyimpanan:</p>
                <form id="receipt-form">
                    <input type="hidden" id="id_permintaan_encrypted">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Jumlah Diterima</th>
                                    <th class="text-center">Jumlah Umum</th>
                                    <th class="text-center">Jumlah Perkara</th>
                                </tr>
                            </thead>
                            <tbody id="receipt-item-list">
                                <!-- Item list dimuat oleh AJAX -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-receipt">Simpan Penerimaan</button>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>