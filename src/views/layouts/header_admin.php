<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?action=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard Admin - BeliLapak'; ?></title>
    <!-- Path CSS untuk admin (sesuaikan jika file style.css ada di folder static/css/) -->
    <link rel="stylesheet" href="static/css/style.css?v=<?= time(); ?>">
</head>
<body class="admin-body">

<div class="admin-container">
    <!-- SIDEBAR KHUSUS ADMIN -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">BeliLapak</div>
        <ul class="sidebar-menu">
            <li><a href="index.php?action=admin_dashboard" class="<?= (isset($active_page) && $active_page === 'dashboard') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="index.php?action=manage_products" class="<?= (isset($active_page) && $active_page === 'products') ? 'active' : ''; ?>">Kelola Produk</a></li>
            <li><a href="index.php?action=manage_stock" class="<?= (isset($active_page) && $active_page === 'stock') ? 'active' : ''; ?>">Kelola Stok</a></li>
            <li><a href="index.php?action=order_list" class="<?= (isset($active_page) && $active_page === 'orders') ? 'active' : ''; ?>">Daftar Pesanan</a></li>
            <li class="logout-item"><a href="index.php?action=logout">Keluar</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h2><?= $page_title ?? 'Dashboard'; ?></h2>
            <div class="admin-profile">
                <span>Halo, <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin'); ?></strong>!</span>
            </div>
        </header>

        <div class="admin-content">
            <!-- Batas akhir header admin. Konten halaman akan dilanjutkan di bawah file ini -->