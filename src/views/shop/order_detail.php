
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../layouts/header.php';

// Mendukung beberapa nama variabel dari controller
$orderData = $order ?? $pesanan ?? [];
$orderItems = $orderDetails ?? $details ?? $items ?? [];

?>

<main class="order-detail-page">

    <!-- TOMBOL KEMBALI -->
    <a href="index.php?action=my_orders" class="order-back-btn">
         Kembali ke Pesanan Saya
    </a>

    <div class="order-detail-header">

        <h1>Detail Pesanan</h1>

        <p>
            Informasi lengkap pesanan kamu di BeliLapak.
        </p>

    </div>

    <?php if (!empty($orderData)): ?>

        <!-- INFORMASI PESANAN -->
        <div class="order-detail-card">

            <h2>Informasi Pesanan</h2>

            <div class="order-detail-row">
                <span>ID Pesanan</span>
                <strong>
                    #<?= (int) $orderData['id']; ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Tanggal Pesanan</span>
                <strong>
                    <?= htmlspecialchars(
                        $orderData['tanggal_pesanan'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Nama Penerima</span>
                <strong>
                    <?= htmlspecialchars(
                        $orderData['nama_penerima'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Nomor Telepon</span>
                <strong>
                    <?= htmlspecialchars(
                        $orderData['no_telp_penerima'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Alamat Pengiriman</span>
                <strong>
                    <?= nl2br(htmlspecialchars(
                        $orderData['alamat_pengiriman'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    )); ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Kurir</span>
                <strong>
                    <?= htmlspecialchars(
                        $orderData['kurir'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Metode Pembayaran</span>
                <strong>
                    <?= htmlspecialchars(
                        $orderData['metode_pembayaran'] ?? '-',
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Status Pembayaran</span>
                <span class="order-detail-status">
                    <?= htmlspecialchars(
                        ucfirst($orderData['status_pembayaran'] ?? '-'),
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </span>
            </div>

            <div class="order-detail-row">
                <span>Status Pesanan</span>
                <span class="order-detail-status">
                    <?= htmlspecialchars(
                        ucfirst($orderData['status_pesanan'] ?? '-'),
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </span>
            </div>

        </div>


        <!-- PRODUK PESANAN -->
        <div class="order-detail-card">

            <h2>Produk yang Dipesan</h2>

            <?php if (!empty($orderItems)): ?>

                <?php foreach ($orderItems as $item): ?>

                    <?php
                    $harga = (float) ($item['harga_satuan'] ?? 0);
                    $jumlah = (int) ($item['jumlah'] ?? 0);
                    $subtotalProduk = $harga * $jumlah;
                    ?>

                    <div class="order-product-row">

                        <div class="order-product-info">

                            <strong>
                                <?= htmlspecialchars(
                                    $item['nama_produk'] ?? 'Produk',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </strong>

                            <p>
                                <?= $jumlah; ?> × Rp<?= number_format(
                                    $harga,
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </p>

                        </div>

                        <strong class="order-product-price">
                            Rp<?= number_format(
                                $subtotalProduk,
                                0,
                                ',',
                                '.'
                            ); ?>
                        </strong>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p>Detail produk belum tersedia.</p>

            <?php endif; ?>

        </div>


        <!-- RINGKASAN PEMBAYARAN -->
        <div class="order-detail-card">

            <h2>Ringkasan Pembayaran</h2>

            <div class="order-detail-row">
                <span>Subtotal Produk</span>

                <strong>
                    Rp<?= number_format(
                        (float) ($orderData['total_belanja'] ?? 0)
                        - (float) ($orderData['ongkos_kirim'] ?? 0),
                        0,
                        ',',
                        '.'
                    ); ?>
                </strong>
            </div>

            <div class="order-detail-row">
                <span>Ongkos Kirim</span>

                <strong>
                    Rp<?= number_format(
                        (float) ($orderData['ongkos_kirim'] ?? 0),
                        0,
                        ',',
                        '.'
                    ); ?>
                </strong>
            </div>

            <div class="order-detail-row order-detail-total">
                <span>Total Pembayaran</span>

                <strong>
                    Rp<?= number_format(
                        (float) ($orderData['total_belanja'] ?? 0),
                        0,
                        ',',
                        '.'
                    ); ?>
                </strong>
            </div>

        </div>

    <?php else: ?>

        <div class="order-detail-card">
            <h2>Pesanan tidak ditemukan.</h2>

           <a href="index.php?action=my_orders" class="order-back-btn">
    Kembali ke Pesanan Saya
</a>
        </div>

    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
