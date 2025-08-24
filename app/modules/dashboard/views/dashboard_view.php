<?php require_once APP_PATH . '/views/templates/header.php'; ?>
<h2>Selamat Datang, <?php echo e($nama_user); ?>!</h2>
<p>Ini adalah halaman dashboard Anda.</p>
<div style="width: 80%; margin: auto;">
    <canvas id="usageChart"></canvas>
</div>
<?php require_once APP_PATH . '/views/templates/footer.php'; ?>