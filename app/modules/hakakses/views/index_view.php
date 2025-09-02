<?php

require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Pilih Peran (Role)
            </div>
            <div class="list-group list-group-flush" id="role-list">
                <?php foreach ($roles as $role) : ?>
                    <a href="#" class="list-group-item list-group-item-action"
                        data-role-id="<?php echo e($role['id_role']); ?>">
                        <?php echo e($role['nama_role']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Daftar Izin (Permissions) untuk <strong id="selected-role-name">...</strong>
            </div>
            <div class="card-body" id="permission-container" style="max-height: 60vh; overflow-y: auto;">
                <p class="text-muted text-center mt-5">Pilih peran di sebelah kiri untuk melihat dan mengelola izin.</p>
            </div>
            <div class="card-footer text-end">
                <?php if (has_permission('role_management_update')) : ?>
                    <button class="btn btn-primary d-none" id="btn-save-permissions">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>