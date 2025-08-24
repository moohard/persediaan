<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<h3><?php echo e($title); ?></h3>
<div class="card">
    <div class="card-body">
        <form action="/barang/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
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
                <label for="stok" class="form-label">Stok Awal</label>
                <input type="number" class="form-control" id="stok" name="stok" required min="0">
            </div>
            <a href="/barang" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>