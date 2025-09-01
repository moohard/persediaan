<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? e($title) . ' - Sistem Persediaan' : 'Sistem Persediaan'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background-color: #f8f9fa;
        }

        .wrapper {
            display: flex;
            width: 100%;
            flex: 1;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #f8f9fa;
            transition: all 0.3s;
            box-shadow: 1px 0 5px rgba(0, 0, 0, 0.1);
        }

        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .main-header {
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body data-user-role="<?php echo isset($_SESSION['nama_role']) ? e($_SESSION['nama_role']) : ''; ?>"
    data-permissions='<?php echo isset($_SESSION['permissions']) ? json_encode($_SESSION['permissions']) : '[]'; ?>'>

    <?php if (isset($_SESSION['user_id'])) : ?>
        <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow main-header">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="#">Sistem Persediaan ATK</a>
            <div class="navbar-nav">
                <div class="nav-item text-nowrap d-flex align-items-center">
                    <span class="navbar-text me-3 text-white">
                        Selamat datang, <?php echo e($_SESSION['nama']); ?>!
                    </span>
                    <a class="nav-link px-3" href="/auth/logout">Logout</a>
                </div>
            </div>
        </header>

        <div class="wrapper">
            <nav id="sidebar" class="bg-light">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-house-door"></i>
                                Dashboard</a></li>

                        <?php if (has_permission('barang_view')) : ?>
                            <li class="nav-item"><a class="nav-link" href="/barang"><i class="bi bi-box-seam"></i> Manajemen
                                    Barang</a></li>
                        <?php endif; ?>

                        <?php if (has_permission('permintaan_view_own') || has_permission('permintaan_view_all')) : ?>
                            <li class="nav-item"><a class="nav-link" href="/permintaan"><i class="bi bi-file-earmark-text"></i>
                                    Permintaan Barang</a></li>
                        <?php endif; ?>

                        <?php if (has_permission('pembelian_process')) : ?>
                            <li class="nav-item"><a class="nav-link" href="/pembelian"><i class="bi bi-cart-check"></i> Proses
                                    Pembelian</a></li>
                        <?php endif; ?>

                        <?php if (has_permission('barangmasuk_process')) : ?>
                            <li class="nav-item"><a class="nav-link" href="/barangmasuk"><i class="bi bi-box-arrow-in-down"></i>
                                    Penerimaan Barang</a></li>
                        <?php endif; ?>
                        <?php if (has_permission('laporan_view')) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/laporan">
                                    <i class="bi bi-graph-up"></i> Laporan
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (has_permission('user_management_view')) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/pengguna">
                                    <i class="bi bi-people"></i> Manajemen Pengguna
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (has_permission('log_view')) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/log">
                                    <i class="bi bi-journal-text"></i> Query Log
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (has_permission('pengaturan_view')) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/pengaturan">
                                    <i class="bi bi-gear"></i> Pengaturan
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <main id="content">
                <?php

                $flash_message = get_flash_message();
                if ($flash_message) : ?>
                    <div class="alert alert-<?php echo e($flash_message['type']); ?> alert-dismissible fade show" role="alert">
                        <?php echo e($flash_message['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <main class="container mt-5">
                <?php endif; ?>