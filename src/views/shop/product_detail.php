<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .btn-outline-pink {
        flex: 1;
        text-align: center;
        padding: 12px;
        border: 2px solid #e0145c;
        color: #e0145c;
        background: white;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-outline-pink:hover {
        background: #fff0f5;
    }
    .btn-solid-pink {
        flex: 1;
        text-align: center;
        padding: 12px;
        background: #e0145c;
        color: white;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        transition: 0.2s;
    }
    .btn-solid-pink:hover {
        background: #c2104d;
    }
</style>

<main>
    <div class="product-detail-container">
        
        <div class="detail-header">
            <a href="index.php?action=katalog" class="btn-back-elegant" style="display: inline-flex; align-items: center; gap: 8px; color: #555; text-decoration: none; margin-bottom: 20px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Katalog
            </a>
        </div>

        <?php if (!empty($produk)): ?>
            <!-- PERBAIKAN: grid-template-columns diubah dari 1fr 1fr menjadi 400px 1fr (gambar max 400px, sisanya info) -->
            <div class="product-detail-layout" style="display: grid; grid-template-columns: 400px 1fr; gap: 40px; align-items: start; background: white; padding: 30px; border-radius: 12px; border: 1px solid #ddd;">
                
                <!-- PERBAIKAN: Tambahan padding & background pada frame gambar, dan max-width pada img -->
                <div class="product-image-gallery" style="background: #fdfdfd; padding: 20px; border-radius: 12px; border: 1px solid #eee; text-align: center;">
                    <img src="static/uploads/uploads/<?php echo !empty($produk['gambar']) ? htmlspecialchars($produk['gambar']) : 'default.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" 
                         style="width: 100%; max-width: 320px; height: auto; object-fit: contain; border-radius: 8px;">
                </div>
                
                <div class="product-info-section">
                    <h1 style="font-size: 28px; margin-bottom: 15px;"><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>
                    
                    <div style="font-size: 24px; color: #e0145c; font-weight: 700; margin-bottom: 20px;">
                        Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                    </div>
                    
                    <div style="margin-bottom: 25px; color: #777; font-size: 15px;">
                        Stok Tersedia: <strong style="color: #333;"><?php echo (int)$produk['stok']; ?></strong>
                    </div>

                    <div class="product-description" style="margin-bottom: 30px; line-height: 1.6; color: #444;">
                        <h3 style="font-size: 16px; margin-bottom: 10px;">Deskripsi Produk</h3>
                        <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'] ?? 'Deskripsi produk belum tersedia.')); ?></p>
                    </div>

                    <div class="product-actions" style="display: flex; gap: 15px;">
                        <a href="index.php?action=cart_add&id=<?php echo $produk['id']; ?>" class="btn-outline-pink">
                            + Keranjang
                        </a>
                        <a href="index.php?action=checkout&id=<?php echo $produk['id']; ?>" class="btn-solid-pink">
                            Beli Sekarang
                        </a>
                    </div>
                </div>

            </div>
        <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 50px 0;">
                <h2>Produk tidak ditemukan</h2>
                <p style="color: #777; margin-top: 10px;">Maaf, produk yang Anda cari mungkin sudah dihapus atau tidak tersedia.</p>
            </div>
        <?php endif; ?>
        
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>