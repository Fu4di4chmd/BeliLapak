
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../layouts/header.php';

?>

<div class="checkout-page">

    <div class="checkout-container">

        <div class="checkout-actions">
            <a href="index.php?action=cart" class="checkout-back">
                ← Kembali ke Keranjang
            </a>
        </div>

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

                            <label for="nama_penerima">
                                Nama Penerima
                            </label>

                            <input
                                type="text"
                                id="nama_penerima"
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

                            <label for="no_telp_penerima">
                                Nomor Telepon
                            </label>

                            <input
                                type="tel"
                                id="no_telp_penerima"
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

                            <label for="alamat_pengiriman">
                                Alamat Lengkap
                            </label>

                            <textarea
                                id="alamat_pengiriman"
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

                            <label for="kurir">
                                Pilih Kurir
                            </label>

                            <select
                                id="kurir"
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

                        <h2>Ringkasan Pesanan</h2>

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

                            <span>Subtotal</span>

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

                            <span>Ongkos Kirim</span>

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

                            <span>Total</span>

                            <strong>
                                Rp<?= number_format(
                                    $total ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ); ?>
                            </strong>

                        </div>


                        <!-- TOMBOL CHECKOUT -->
                        <div class="checkout-actions">

                            <button
                                type="submit"
                                class="checkout-button"
                                <?= empty($cartItems) ? 'disabled' : ''; ?>
                            >
                                Lanjut ke Pembayaran
                            </button>

                        </div>

                        <p class="checkout-note">
                            Pastikan alamat dan nomor telepon penerima
                            sudah benar sebelum melanjutkan pesanan.
                        </p>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
