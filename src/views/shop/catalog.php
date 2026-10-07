<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Konten khusus untuk halaman katalog produk -->
<main>
    <div class="promo-banner">
        <div>
            <h2>Favoritmu, harga seru!</h2>
            <a href="#" class="btn-promo">Lihat Promo</a>
        </div>
    </div>

    <div class="section-title">
        <h3>Rekomendasi Untukmu</h3>
    </div>

    <div class="product-grid">
        <?php if (!empty($produk_list)): ?>
            <?php foreach ($produk_list as $produk): ?>
            <a href="index.php?action=detail&id=<?php echo $produk['id']; ?>" class="product-card">
                <img src="static/uploads/<?php echo !empty($produk['gambar']) ? $produk['gambar'] : 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="product-img">
                <div class="product-info">
                    <div class="product-title"><?php echo htmlspecialchars($produk['nama_produk']); ?></div>
                    <div class="product-price">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></div>
                    <div class="product-stats">Sisa Stok: <?php echo $produk['stok']; ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: span 5; font-size: 15px; color: #757575; padding: 40px 0;">Belum ada produk yang tersedia saat ini.</p>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>