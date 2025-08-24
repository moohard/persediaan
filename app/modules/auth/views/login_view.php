<?php require_once APP_PATH . '/views/templates/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Login Sistem ATK</h3>
                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger"><?php echo e($error); ?></div>
                <?php endif; ?>
                <form action="<?php echo BASE_URL; ?>/auth/process_login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" <?php if ($is_locked ?? FALSE) echo 'disabled'; ?>>
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once APP_PATH . '/views/templates/footer.php'; ?>