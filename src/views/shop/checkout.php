<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../layouts/header.php';

?>

<div class="checkout-page">
     <div class="checkout-actions">

                            <a href="index.php?action=cart" class="checkout-back">
    ← Kembali ke Keranjang
</a>

                            
    <div class="checkout-container">

        <h1 class="checkout-title">
            Checkout
        </h1>

        <form
            action="index.php?action=process_checkout"
            method="POST"
        >

            <div class="checkout-grid">

                <!-- BAGIAN KIRI -->
                <div>

                    <!-- ALAMAT PENGIRIMAN -->
                    <div class="checkout-card">

                        <h2>Alamat Pengiriman</h2>

                        <div class="checkout-form-group">

                            <label>
                                Nama Penerima
                            </label>

                            <input
                                type="text"
                                name="nama_penerima"
                                value="<?= htmlspecialchars(
                                    $user['nama'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                required
                            >

                        </div>


                        <div class="checkout-form-group">

                            <label>
                                Nomor Telepon
                            </label>

                            <input
                                type="text"
                                name="no_telp_penerima"
                                value="<?= htmlspecialchars(
                                    $user['no_telp'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>"
                                required
                            >

                        </div>


                        <div class="checkout-form-group">

                            <label>
                                Alamat Lengkap
                            </label>

                            <textarea
                                name="alamat_pengiriman"
                                rows="4"
                                required
                            ><?= htmlspecialchars(
                                $user['alamat'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?></textarea>

                        </div>

                    </div>


                    <!-- PENGIRIMAN -->
                    <div class="checkout-card">

                        <h2>Pengiriman</h2>

                        <div class="checkout-form-group">

                            <label>
                                Pilih Kurir
                            </label>

                            <select
                                name="kurir"
                                required
                            >

                                <option value="">
                                    Pilih kurir
                                </option>

                                <option value="JNE">
                                    JNE
                                </option>

                                <option value="J&T">
                                    J&T
                                </option>

                                <option value="SiCepat">
                                    SiCepat
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <!-- BAGIAN KANAN -->
                <div>

                    <div class="checkout-card">

                        <h2>
                            Ringkasan Pesanan
                        </h2>


                        <!-- PRODUK DI KERANJANG -->
                        <?php if (!empty($cartItems)): ?>

                            <?php foreach ($cartItems as $item): ?>

                                <div class="checkout-summary-row">

                                    <span>
                                        <?= htmlspecialchars(
                                            $item['nama_produk'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>

                                        × <?= (int) ($item['jumlah'] ?? 0); ?>
                                    </span>

                                    <strong>
                                        Rp<?= number_format(
                                            (float) ($item['harga'] ?? 0)
                                            * (int) ($item['jumlah'] ?? 0),
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </strong>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="checkout-empty">
                                Keranjang masih kosong.
                            </p>

                        <?php endif; ?>


                        <!-- SUBTOTAL -->
                        <div class="checkout-summary-row">

                            <span>
                                Subtotal
                            </span>

                            <strong>
                                Rp<?= number_format(
                                    $subtotal ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </strong>

                        </div>


                        <!-- ONGKIR -->
                        <div class="checkout-summary-row">

                            <span>
                                Ongkos Kirim
                            </span>

                            <strong>
                                Rp<?= number_format(
                                    $ongkir ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </strong>

                        </div>


                        <!-- TOTAL -->
                        <div class="checkout-summary-row checkout-summary-total">

                            <span>
                                Total
                            </span>

                            <strong>
                                Rp<?= number_format(
                                    $total ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </strong>

                        </div>


                        <!-- TOMBOL -->
                         
                        <div class="checkout-actions">

                           

                            <button
                                type="submit"
                                class="checkout-button"
                                <?= empty($cartItems) ? 'disabled' : ''; ?>
                            >
                                Lanjut ke Pembayaran
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>