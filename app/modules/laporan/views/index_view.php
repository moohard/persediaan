<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
</div>
<div id="signature-data" data-nama="<?php echo e($nama_penandatangan); ?>"
    data-nip="<?php echo e($nip_penandatangan); ?>" style="display: none;">
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
    <?php if (has_permission('laporan_permintaan_view')) : ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="permintaan-tab" data-bs-toggle="tab" data-bs-target="#permintaan-pane"
                type="button" role="tab">Lap. Permintaan</button>
        </li>
    <?php endif; ?>
    <?php if (has_permission('laporan_pembelian_view')) : ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pembelian-tab" data-bs-toggle="tab" data-bs-target="#pembelian-pane" type="button"
                role="tab">Lap. Pembelian</button>
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
                    <?php if (has_permission('laporan_kartu_stok_print')) : ?>
                        <button class="btn btn-sm btn-outline-danger d-none" id="btn-print-kartu-stok">
                            <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="barang-select" class="form-label">Pilih Barang:</label>
                            <select class="form-select" id="barang-select">
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($barang_list as $barang) : ?>
                                    <option value="<?php echo e($barang['id_barang']); ?>">
                                        <?php echo e($barang['nama_barang']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="kartu-stok-table">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">Tanggal</th>
                                    <th rowspan="2" class="align-middle">Keterangan</th>
                                    <th rowspan="2" class="align-middle text-center">Jumlah Ubah</th>
                                    <th colspan="2" class="text-center">Stok Awal</th>
                                    <th colspan="2" class="text-center">Stok Akhir</th>
                                    <th rowspan="2" class="align-middle">Pengguna</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Umum</th>
                                    <th class="text-center">Perkara</th>
                                    <th class="text-center">Umum</th>
                                    <th class="text-center">Perkara</th>
                                </tr>
                            </thead>
                            <tbody id="kartu-stok-body">
                                <tr>
                                    <td colspan="8" class="text-center">Silakan pilih barang untuk melihat riwayat.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Tab Laporan Permintaan -->
    <?php if (has_permission('laporan_permintaan_view')) : ?>
        <div class="tab-pane fade" id="permintaan-pane" role="tabpanel">
            <div class="card card-tab">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Laporan Semua Permintaan
                    <button class="btn btn-sm btn-outline-danger" id="btn-print-permintaan"><i
                            class="bi bi-file-earmark-pdf"></i> Cetak PDF</button>
                </div>
                <div class="card-body">
                    <form id="filter-permintaan-form" class="row g-3 mb-3">
                        <div class="col-md-4"><label>Dari Tanggal:</label><input type="date" class="form-control"
                                id="start-date-permintaan"></div>
                        <div class="col-md-4"><label>Sampai Tanggal:</label><input type="date" class="form-control"
                                id="end-date-permintaan"></div>
                        <div class="col-md-3"><label>Status:</label><select class="form-select" id="status-permintaan">
                                <option value="semua">Semua Status</option>
                                <option>Diajukan</option>
                                <option>Disetujui</option>
                                <option>Ditolak</option>
                                <option>Selesai</option>
                                <option>Diproses Pembelian</option>
                                <option>Sudah Dibeli</option>
                            </select></div>
                        <div class="col-md-1 d-flex align-items-end"><button type="submit"
                                class="btn btn-primary w-100">Filter</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="laporan-permintaan-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Pemohon</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Penyetuju</th>
                                </tr>
                            </thead>
                            <tbody id="laporan-permintaan-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tab Laporan Pembelian -->
    <?php if (has_permission('laporan_pembelian_view')) : ?>
        <div class="tab-pane fade" id="pembelian-pane" role="tabpanel">
            <div class="card card-tab">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Laporan Permintaan Pembelian
                    <button class="btn btn-sm btn-outline-danger" id="btn-print-pembelian"><i
                            class="bi bi-file-earmark-pdf"></i> Cetak PDF</button>
                </div>
                <div class="card-body">
                    <form id="filter-pembelian-form" class="row g-3 mb-3">
                        <div class="col-md-4"><label>Dari Tanggal:</label><input type="date" class="form-control"
                                id="start-date-pembelian"></div>
                        <div class="col-md-4"><label>Sampai Tanggal:</label><input type="date" class="form-control"
                                id="end-date-pembelian"></div>
                        <div class="col-md-3"><label>Status:</label><select class="form-select" id="status-pembelian">
                                <option value="semua">Semua Status</option>
                                <option>Diajukan</option>
                                <option>Disetujui</option>
                                <option>Ditolak</option>
                                <option>Selesai</option>
                                <option>Diproses Pembelian</option>
                                <option>Sudah Dibeli</option>
                            </select></div>
                        <div class="col-md-1 d-flex align-items-end"><button type="submit"
                                class="btn btn-primary w-100">Filter</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="laporan-pembelian-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Pemohon</th>
                                    <th>Status</th>
                                    <th>Penyetuju</th>
                                </tr>
                            </thead>
                            <tbody id="laporan-pembelian-body"></tbody>
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