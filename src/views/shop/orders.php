
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../layouts/header.php';

?>

<main class="my-orders-page">

    <!-- HEADER HALAMAN -->
    <div class="my-orders-header">

        <h1 class="my-orders-title">
            Status Pesanan Saya
        </h1>

        <p class="my-orders-desc">
            Pantau pesanan dan pembayaran belanja kamu di BeliLapak.
        </p>

        <a href="index.php?action=my_orders" class="order-back-btn">
     Kembali ke Pesanan Saya
            </a>

    </div>


    <!-- NOTIFIKASI BERHASIL -->
    <?php if (isset($_GET['success'])): ?>

        <div class="orders-success">
            Pesanan berhasil dibuat! Terima kasih sudah berbelanja di BeliLapak.
        </div>

    <?php endif; ?>


    <!-- DAFTAR PESANAN -->
    <?php if (!empty($orders)): ?>

        <?php foreach ($orders as $order): ?>

            <div class="orders-card">

                <!-- HEADER PESANAN -->
                <div class="orders-card-header">

                    <div>

                        <div class="orders-id">
                            Pesanan #<?= (int) $order['id']; ?>
                        </div>

                        <small class="orders-date">
                            <?= htmlspecialchars(
                                $order['tanggal_pesanan'] ?? '-',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </small>

                    </div>

                    <span class="orders-status">
                        <?= htmlspecialchars(
                            ucfirst($order['status_pesanan'] ?? '-'),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </span>

                </div>


                <!-- INFORMASI PESANAN -->
                <div class="orders-info">

                    <span>Nama Penerima</span>

                    <strong>
                        <?= htmlspecialchars(
                            $order['nama_penerima'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </strong>

                </div>


                <div class="orders-info">

                    <span>Kurir</span>

                    <strong>
                        <?= htmlspecialchars(
                            $order['kurir'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </strong>

                </div>


                <div class="orders-info">

                    <span>Metode Pembayaran</span>

                    <strong>
                        <?= htmlspecialchars(
                            $order['metode_pembayaran'] ?? '-',
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </strong>

                </div>


                <div class="orders-info">

                    <span>Status Pembayaran</span>

                    <span class="orders-payment-status">
                        <?= htmlspecialchars(
                            ucfirst($order['status_pembayaran'] ?? '-'),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </span>

                </div>


                <div class="orders-info">

                    <span>Ongkos Kirim</span>

                    <strong>
                        Rp<?= number_format(
                            (float) ($order['ongkos_kirim'] ?? 0),
                            0,
                            ',',
                            '.'
                        ); ?>
                    </strong>

                </div>


                <!-- TOTAL -->
                <div class="orders-info orders-total-row">

                    <span>Total Pembayaran</span>

                    <strong class="orders-total">
                        Rp<?= number_format(
                            (float) ($order['total_belanja'] ?? 0),
                            0,
                            ',',
                            '.'
                        ); ?>
                    </strong>

                </div>


                <!-- TOMBOL DETAIL -->
                <div class="orders-card-actions">

                    <a
                        href="index.php?action=order_detail&id=<?= (int) $order['id']; ?>"
                        class="orders-detail-btn"
                    >
                        Lihat Detail Pesanan 
                    </a>

                </div>

            </div>

        <?php endforeach; ?>


    <?php else: ?>

        <!-- KERANJANG PESANAN KOSONG -->
        <div class="orders-empty">

            <div class="orders-empty-icon">
                📦
            </div>

            <h2>Belum Ada Pesanan</h2>

            <p>
                Kamu belum memiliki riwayat pesanan.
                Yuk, temukan produk favoritmu!
            </p>

            <a href="index.php?action=katalog" class="orders-shop-btn">
                Mulai Belanja
            </a>

        </div>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
