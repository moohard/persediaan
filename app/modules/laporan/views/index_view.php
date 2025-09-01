<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
</div>

<!-- Navigasi Tab -->
<ul class="nav nav-tabs" id="laporanTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="stok-barang-tab" data-bs-toggle="tab" data-bs-target="#stok-barang-pane"
            type="button" role="tab">Laporan Stok</button>
    </li>
    <?php if (has_permission('laporan_kartu_stok_view')) : ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="kartu-stok-tab" data-bs-toggle="tab" data-bs-target="#kartu-stok-pane"
                type="button" role="tab">Kartu Stok</button>
        </li>
    <?php endif; ?>
</ul>

<!-- Konten Tab -->
<div class="tab-content" id="laporanTabContent">

    <!-- Tab Laporan Stok -->
    <div class="tab-pane fade show active" id="stok-barang-pane" role="tabpanel">
        <div class="card card-tab">
            <div class="card-header d-flex justify-content-between align-items-center">
                Laporan Stok Barang Keseluruhan
                <?php if (has_permission('laporan_stok_print')) : ?>
                    <button class="btn btn-sm btn-outline-danger" id="btn-print-stok">
                        <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="laporan-stok-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th class="text-center">Stok Umum</th>
                                <th class="text-center">Stok Perkara</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="laporan-stok-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Kartu Stok -->
    <?php if (has_permission('laporan_kartu_stok_view')) : ?>
        <div class="tab-pane fade" id="kartu-stok-pane" role="tabpanel">
            <div class="card card-tab">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Laporan Kartu Stok per Barang
                    <button class="btn btn-sm btn-outline-danger d-none" id="btn-print-kartu-stok">
                        <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="barang-select" class="form-label">Pilih Barang:</label>
                            <select class="form-select" id="barang-select">
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($barang_list as $barang) : ?>
                                    <option value="<?php echo e($barang['id_barang']); ?>">
                                        <?php echo e($barang['nama_barang']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="kartu-stok-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis Transaksi</th>
                                    <th>Keterangan</th>
                                    <th class="text-center">Jumlah Ubah</th>
                                    <th class="text-center">Stok Awal</th>
                                    <th class="text-center">Stok Akhir</th>
                                    <th>Pengguna</th>
                                </tr>
                            </thead>
                            <tbody id="kartu-stok-body">
                                <tr>
                                    <td colspan="7" class="text-center">Silakan pilih barang untuk melihat riwayat.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<style>
    .card-tab {
        border-top-left-radius: 0;
    }
</style>
<script src="<?php echo BASE_URL; ?>/js/plugins/jspdf.umd.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/plugins/jspdf.plugin.autotable.min.js"></script>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>