<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hanya admin yang boleh mengakses halaman admin
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: index.php?action=login");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars(
            $page_title ?? 'Dashboard Admin - BeliLapak'
        ); ?>
    </title>

    <link
        rel="stylesheet"
        href="/BeliLapak/src/static/css/style.css?v=<?= time(); ?>"
    >

</head>

<body class="admin-body">

<div class="admin-container">

    <!-- SIDEBAR ADMIN -->
    <aside class="admin-sidebar">

        <div class="sidebar-brand">
            BeliLapak
        </div>

        <div class="sidebar-label">
            ADMIN PANEL
        </div>

        <ul class="sidebar-menu">

            <li>
                <a
                    href="index.php?action=admin_dashboard"
                    class="<?= (
                        isset($active_page) &&
                        $active_page === 'dashboard'
                    ) ? 'active' : ''; ?>"
                >
                    Dashboard
                </a>
            </li>

            <li>
                <a
                    href="index.php?action=manage_products"
                    class="<?= (
                        isset($active_page) &&
                        $active_page === 'products'
                    ) ? 'active' : ''; ?>"
                >
                    Kelola Produk
                </a>
            </li>

            <li>
                <a
                    href="index.php?action=manage_stock"
                    class="<?= (
                        isset($active_page) &&
                        $active_page === 'stock'
                    ) ? 'active' : ''; ?>"
                >
                    Kelola Stok
                </a>
            </li>

            <li>
                <a
                    href="index.php?action=order_list"
                    class="<?= (
                        isset($active_page) &&
                        $active_page === 'orders'
                    ) ? 'active' : ''; ?>"
                >
                    Daftar Pesanan
                </a>
            </li>

            <li class="logout-item">

                <a href="index.php?action=logout">
                    Keluar
                </a>

            </li>

        </ul>

    </aside>


    <!-- AREA UTAMA ADMIN -->
    <main class="admin-main">

        <header class="admin-header">

            <h2>
                <?= htmlspecialchars(
                    $page_title ?? 'Dashboard'
                ); ?>
            </h2>

            <div class="admin-profile">

                <div class="admin-avatar">
                    A
                </div>

                <div class="admin-profile-text">

                    <span>Halo,</span>

                    <strong>
                        <?= htmlspecialchars(
                            $_SESSION['nama'] ?? 'Admin'
                        ); ?>
                    </strong>

                </div>

            </div>

        </header>


        <div class="admin-content">