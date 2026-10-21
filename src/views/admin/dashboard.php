<?php

$active_page = 'dashboard';
$page_title = 'Dashboard Admin';

require_once __DIR__ . '/../layouts/header_admin.php';

?>

<div class="dashboard-welcome">

    <h1>
        Selamat Datang, Admin!
    </h1>

    <p>
        Kelola data produk, stok, dan pesanan BeliLapak dari satu tempat.
    </p>

</div>


<div class="dashboard-grid">

    <!-- KELOLA PRODUK -->
    <div class="dashboard-card">

        <div class="dashboard-card-icon">
            📦
        </div>

        <div class="dashboard-card-content">

            <span>
                Produk
            </span>

            <h3>
                Kelola Produk
            </h3>

            <p>
                Tambah, lihat, dan hapus produk BeliLapak.
            </p>

            <a href="index.php?action=manage_products">
                Buka Kelola Produk →
            </a>

        </div>

    </div>


    <!-- KELOLA STOK -->
    <div class="dashboard-card">

        <div class="dashboard-card-icon">
            📊
        </div>

        <div class="dashboard-card-content">

            <span>
                Stok
            </span>

            <h3>
                Kelola Stok
            </h3>

            <p>
                Pantau jumlah stok produk yang tersedia.
            </p>

            <a href="index.php?action=manage_stock">
                Buka Kelola Stok →
            </a>

        </div>

    </div>


    <!-- PESANAN -->
    <div class="dashboard-card">

        <div class="dashboard-card-icon">
            🛒
        </div>

        <div class="dashboard-card-content">

            <span>
                Pesanan
            </span>

            <h3>
                Daftar Pesanan
            </h3>

            <p>
                Lihat dan kelola pesanan pelanggan.
            </p>

            <a href="index.php?action=order_list">
                Lihat Pesanan →
            </a>

        </div>

    </div>

</div>

</div>
</main>
</div>

</body>
</html>