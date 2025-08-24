<?php require_once APP_PATH . '/views/templates/header.php'; ?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Login Sistem ATK</h3>

                <!-- Form sekarang tidak memiliki action, akan ditangani oleh JS -->
                <form id="login-form">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once APP_PATH . '/views/templates/footer.php'; ?>