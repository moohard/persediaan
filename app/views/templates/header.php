<?php

regenerate_session_periodically();
generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e($_SESSION['csrf_token'] ?? ''); ?>">
    <title><?php echo e($title ?? 'Sistem Persediaan ATK'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/dashboard"><i class="bi bi-box-seam-fill"></i> ATK PA Penajam</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/dashboard">Dashboard</a>
                        </li>
                        <?php if (in_array($_SESSION['role'], [ 'admin', 'developer' ])) : ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    Master Data
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/barang">Data Barang</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/pembelian">Proses Pembelian</a>
                            </li>
                            <!-- PERUBAHAN: Tambahkan link menu baru ini -->
                            <li class="nav-item">
                                <a class="nav-link" href="/barangmasuk">Barang Masuk</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/permintaan">Permintaan</a>
                        </li>
                        <?php if ($_SESSION['role'] === 'developer' && ENVIRONMENT === 'development') : ?>
                            <li class="nav-item">
                                <a class="nav-link text-warning" href="/log">Query Log</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo e($_SESSION['nama_lengkap']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/auth/logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php display_flash_message(); ?>