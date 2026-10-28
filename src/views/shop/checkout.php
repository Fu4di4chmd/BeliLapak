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
    </div>

    <div class="checkout-container">

        <h1 class="checkout-title">
            Checkout
        </h1>

        <form action="index.php?action=process_checkout" method="POST">

            <div class="checkout-grid">

                <!-- BAGIAN KIRI -->
                <div>

                    <!-- ALAMAT PENGIRIMAN -->
                    <div class="checkout-card">
                        <h2>Alamat Pengiriman</h2>

                        <div class="checkout-form-group">
                            <label>Nama Penerima</label>
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
                            <label>Nomor Telepon</label>
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
                            <label>Alamat Lengkap</label>
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
                    <div class="checkout-card" style="margin-top: 20px;">
                        <h2>Pengiriman</h2>

                        <div class="checkout-form-group">
                            <label>Pilih Kurir</label>
                            <!-- Tambahan ID, onchange, dan data-ongkir -->
                            <select name="kurir" id="pilihan-kurir" onchange="hitungTotal()" required>
                                <option value="" data-ongkir="0">Pilih kurir</option>
                                <option value="JNE" data-ongkir="18000">JNE Reguler (Rp18.000)</option>
                                <option value="J&T" data-ongkir="15000">J&T Express (Rp15.000)</option>
                                <option value="SiCepat" data-ongkir="20000">SiCepat (Rp20.000)</option>
                                <option value="Lokal_Bangkalan" data-ongkir="5000">Kurir Lokal Area Bangkalan (Rp5.000)</option>
                            </select>
                        </div>
                    </div>

                    <!-- METODE PEMBAYARAN -->
                    <div class="checkout-card" style="margin-top: 20px;">
                        <h2>Metode Pembayaran</h2>

                        <div class="checkout-form-group">
                            <label>Pilih Pembayaran</label>
                            <select name="metode_pembayaran" required>
                                <option value="">Pilih metode pembayaran</option>
                                <option value="transfer_bca">Transfer Bank BCA</option>
                                <option value="transfer_mandiri">Transfer Bank Mandiri</option>
                                <option value="qris">QRIS (Gopay/OVO/Dana)</option>
                                <option value="cod">Bayar di Tempat (COD)</option>
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
                                            (float) ($item['harga'] ?? 0) * (int) ($item['jumlah'] ?? 0),
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </strong>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="checkout-empty">Keranjang masih kosong.</p>
                        <?php endif; ?>


                        <!-- SUBTOTAL -->
                        <div class="checkout-summary-row" style="margin-top: 15px;">
                            <span>Subtotal</span>
                            <!-- Tambahan ID dan data-subtotal -->
                            <strong id="angka-subtotal" data-subtotal="<?= $subtotal ?? 0 ?>">
                                Rp<?= number_format($subtotal ?? 0, 0, ',', '.'); ?>
                            </strong>
                        </div>


                        <!-- ONGKIR -->
                        <div class="checkout-summary-row">
                            <span>Ongkos Kirim</span>
                            <!-- Tambahan ID -->
                            <strong id="angka-ongkir">
                                Rp0
                            </strong>
                        </div>


                        <!-- TOTAL -->
                        <div class="checkout-summary-row checkout-summary-total">
                            <span>Total</span>
                            <!-- Tambahan ID -->
                            <strong id="angka-total">
                                Rp<?= number_format($subtotal ?? 0, 0, ',', '.'); ?>
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

<!-- SCRIPT UNTUK MENGHITUNG TOTAL SECARA DINAMIS -->
<script>
    function hitungTotal() {
        // Ambil elemen select kurir dan opsi yang dipilih
        const kurirSelect = document.getElementById('pilihan-kurir');
        const opsiTerpilih = kurirSelect.options[kurirSelect.selectedIndex];
        
        // Dapatkan angka ongkir dari atribut data-ongkir, pastikan format integer
        const ongkir = parseInt(opsiTerpilih.getAttribute('data-ongkir')) || 0;
        
        // Ambil data subtotal awal dari atribut HTML
        const subtotalElement = document.getElementById('angka-subtotal');
        const subtotal = parseInt(subtotalElement.getAttribute('data-subtotal')) || 0;
        
        // Lakukan penjumlahan
        const totalAkhir = subtotal + ongkir;
        
        // Format kembali ke format Rupiah (IDR)
        const formatRupiah = (angka) => {
            return "Rp" + angka.toLocaleString('id-ID');
        };
        
        // Tampilkan hasilnya langsung ke layar
        document.getElementById('angka-ongkir').innerText = formatRupiah(ongkir);
        document.getElementById('angka-total').innerText = formatRupiah(totalAkhir);
    }
</script>

<?php 
// Jika Anda punya file footer, panggil di sini
// require_once __DIR__ . '/../layouts/footer.php'; 
?>