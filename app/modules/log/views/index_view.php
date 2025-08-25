<?php require_once APP_PATH . '/views/templates/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?php echo e($title); ?></h3>
    <form action="/log/clear" method="POST"
        onsubmit="return confirm('Anda yakin ingin membersihkan file log hari ini?');">
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Bersihkan Log</button>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($log_content)): ?>
            <p class="text-center text-muted">Belum ada query yang tercatat hari ini.</p>
        <?php else: ?>
            <pre
                style="white-space: pre-wrap; word-wrap: break-word; background-color: #f8f9fa; padding: 15px; border-radius: 5px;"><?php echo e($log_content); ?></pre>
        <?php endif; ?>
    </div>
</div>

<?php require_once APP_PATH . '/views/templates/footer.php'; ?>