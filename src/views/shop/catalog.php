<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Konten khusus untuk halaman katalog produk -->
<main>
    <?php if (empty($search) && empty($kategori)): ?>
    <div class="promo-banner">
        <div>
            <h2>Favoritmu, harga seru!</h2>
            <a href="index.php?action=katalog&show=all" class="btn-promo">Lihat Semua Koleksi</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- FILTER KATEGORI -->
    <div class="category-filter-wrapper">
        <div class="category-filter-title">Pilih Kategori Produk:</div>
        <div class="category-filter-bar">
            <a href="index.php?action=katalog" class="category-pill <?= empty($kategori) && empty($search) ? 'active' : ''; ?>">
                Semua
            </a>
            <a href="index.php?action=katalog&kategori=baju" class="category-pill <?= ($kategori ?? '') === 'baju' ? 'active' : ''; ?>">
                 Baju & Kaos
            </a>
            <a href="index.php?action=katalog&kategori=kemeja" class="category-pill <?= ($kategori ?? '') === 'kemeja' ? 'active' : ''; ?>">
                 Kemeja
            </a>
            <a href="index.php?action=katalog&kategori=celana" class="category-pill <?= ($kategori ?? '') === 'celana' ? 'active' : ''; ?>">
                 Celana
            </a>
            <a href="index.php?action=katalog&kategori=jaket" class="category-pill <?= ($kategori ?? '') === 'jaket' ? 'active' : ''; ?>">
                 Jaket & Outer
            </a>
            <a href="index.php?action=katalog&kategori=batik" class="category-pill <?= ($kategori ?? '') === 'batik' ? 'active' : ''; ?>">
                 Batik
            </a>
            <a href="index.php?action=katalog&kategori=sepatu" class="category-pill <?= ($kategori ?? '') === 'sepatu' ? 'active' : ''; ?>">
                 Sepatu
            </a>
        </div>
    </div>

    <!-- HEADER / STATUS SECTION -->
    <div class="catalog-header-bar">
        <div>
            <?php if (!empty($search)): ?>
                <h3>Hasil Pencarian: "<?= htmlspecialchars($search); ?>"</h3>
                <p class="catalog-subtitle">Ditemukan <?= count($produk_list); ?> produk yang cocok</p>
            <?php elseif (!empty($kategori)): ?>
                <h3>Kategori: <?= htmlspecialchars(ucfirst($kategori)); ?></h3>
                <p class="catalog-subtitle">Menampilkan <?= count($produk_list); ?> produk</p>
            <?php elseif (($show ?? '') === 'all'): ?>
                <h3>Semua Produk</h3>
                <p class="catalog-subtitle">Menampilkan seluruh <?= count($produk_list); ?> produk katalog</p>
            <?php else: ?>
                <h3>Rekomendasi Untukmu</h3>
                <p class="catalog-subtitle">Menampilkan <?= count($produk_list); ?> produk pilihan terpopuler</p>
            <?php endif; ?>
        </div>

        <div>
            <?php if (!empty($search) || !empty($kategori)): ?>
                <a href="index.php?action=katalog" class="btn-reset-filter">
                    ✕ Reset Filter / Pencarian
                </a>
            <?php elseif (!empty($is_home_default) && $total_count > 10): ?>
                <a href="index.php?action=katalog&show=all" class="link-show-all">
                    Lihat Semua (<?= $total_count; ?>) &rarr;
                </a>
            <?php elseif (($show ?? '') === 'all'): ?>
                <a href="index.php?action=katalog" class="link-show-all" style="color: #757575;">
                    Tampilkan Lebih Sedikit (10)
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- GRID KATALOG PRODUK -->
    <div class="product-grid">
        <?php if (!empty($produk_list)): ?>
            <?php foreach ($produk_list as $produk): ?>
            <a href="index.php?action=detail&id=<?php echo $produk['id']; ?>" class="product-card">
                <img src="static/uploads/uploads/<?php echo !empty($produk['gambar']) ? $produk['gambar'] : 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="product-img">
                <div class="product-info">
                    <div class="product-title"><?php echo htmlspecialchars($produk['nama_produk']); ?></div>
                    <div class="product-price">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></div>
                    <div class="product-stats">Sisa Stok: <?php echo $produk['stok']; ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-product-state">
                <p>
                    Tidak ada produk yang cocok <?= !empty($search) ? 'dengan kata kunci "<strong>' . htmlspecialchars($search) . '</strong>"' : 'pada kategori ini'; ?>.
                </p>
                <a href="index.php?action=katalog" class="btn-back-katalog">
                    Lihat Semua Produk
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- TOMBOL LIHAT SEMUA PRODUK (JIKA DI BERANDA DEFAULT) -->
    <?php if (!empty($is_home_default) && $total_count > 10): ?>
    <div style="text-align: center; margin-top: 35px; margin-bottom: 20px;">
        <a href="index.php?action=katalog&show=all" class="btn-all-collection">
            Lihat Semua Produk (<?= $total_count; ?> Koleksi Lengkap) &rarr;
        </a>
    </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>