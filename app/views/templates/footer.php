</main>
<?php if (isset($_SESSION['user_id'])) : ?>
    </div> <!-- Penutup .wrapper -->
<?php endif; ?>

<!-- CSRF Token for Axios -->
<meta name="csrf-token" content="<?php echo isset($_SESSION['csrf_token']) ? e($_SESSION['csrf_token']) : ''; ?>">

<script src="<?php echo BASE_URL; ?>/js/plugins/bootstrap.bundle.min.js"
    xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
<script src="<?php echo BASE_URL; ?>/js/plugins/sweetalert2@11.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/plugins/axios.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/main.js"></script>
<script src="<?php echo BASE_URL; ?>/js/helpers.js"></script>
<?php if (has_permission('notifikasi_view')) : ?>
    <script src="<?php echo BASE_URL; ?>/js/modules/notifikasi.js"></script>
<?php endif; ?>
<?php if (isset($js_module)) : ?>
    <script src="<?php echo BASE_URL; ?>/js/modules/<?php echo e($js_module); ?>.js"></script>
<?php endif; ?>

</body>

</html>