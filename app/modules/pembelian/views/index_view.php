<?php

require_once APP_PATH . '/views/templates/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
</div>
<div class="card">
    <div class="card-header">
        Daftar Permintaan Pembelian (Disetujui Pimpinan)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Pemohon</th>
                        <th>Nama Barang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pembelian-table-body">
                    <!-- Data dimuat oleh AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once APP_PATH . '/views/templates/footer.php'; ?>