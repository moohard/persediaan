</div> <!-- .container -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <!-- PERUBAHAN: Menambahkan SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>

    <!-- PERUBAHAN: Memanggil JS utama dan JS spesifik per modul -->
    <script src="/js/main.js"></script>
    <?php if (isset($js_module)) : ?>
        <?php $js_file_path = ROOT_PATH . '/public/js/modules/' . $js_module . '.js'; ?>
        <?php if (file_exists($js_file_path)) : ?>
            <script src="/js/modules/<?php echo e($js_module); ?>.js"></script>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>