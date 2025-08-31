<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <?php if (has_permission('user_management_create')) : ?>
        <button class="btn btn-primary" id="btn-add-pengguna"><i class="bi bi-plus-circle"></i> Tambah Pengguna</button>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">Daftar Pengguna Sistem</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Peran (Role)</th>
                        <th>Bagian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pengguna-table-body">
                    <!-- Data dimuat oleh AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pengguna -->
<div class="modal fade" id="pengguna-modal" tabindex="-1" aria-labelledby="pengguna-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pengguna-modal-label">Form Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pengguna-form">
                    <input type="hidden" id="id_pengguna_encrypted" name="id_pengguna_encrypted">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                    </div>
                    <div class="mb-3">
                        <label for="id_role" class="form-label">Peran (Role)</label>
                        <select class="form-select" id="id_role" name="id_role" required>
                            <!-- Opsi dimuat oleh AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_bagian" class="form-label">Bagian</label>
                        <select class="form-select" id="id_bagian" name="id_bagian" required>
                            <!-- Opsi dimuat oleh AJAX -->
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                            checked>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btn-save-pengguna">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>