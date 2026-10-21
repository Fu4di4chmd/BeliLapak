<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Styling tombol kembali */
    .btn-back-elegant {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #555;
        text-decoration: none;
        margin-bottom: 25px;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-back-elegant:hover { color: #e0145c; transform: translateX(-4px); }
    
    /* Styling khusus keranjang */
    .cart-card-pink {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 20px;
        background: white;
        border: 1px solid #e0145c;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .cart-checkbox {
        accent-color: #e0145c;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .qty-box {
        display: inline-flex;
        align-items: center;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
        margin-top: 10px;
    }
    .qty-btn {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #555;
        background: #f9f9f9;
        font-weight: bold;
    }
    .qty-btn:hover { background: #eee; }
    .qty-val {
        width: 35px;
        text-align: center;
        font-size: 14px;
        border-left: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        line-height: 30px;
        background: white;
    }
    .text-pink { color: #e0145c; }
    .btn-checkout-pink {
        display: block;
        width: 100%;
        text-align: center;
        padding: 12px;
        background: #e0145c;
        color: white;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        margin-top: 15px;
        border: none;
        cursor: pointer;
    }
    .btn-checkout-pink:hover { background: #c2104d; }
</style>

<main>
    <div class="cart-page">
        
        <!-- Tombol Kembali ke Katalog -->
        <a href="index.php?action=katalog" class="btn-back-elegant">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Katalog
        </a>

        <h1 style="margin-bottom: 25px; font-size: 28px;">Keranjang Belanja</h1>
        
        <?php if (!empty($cart)): ?>
            <div class="cart-layout" style="display: grid; grid-template-columns: 1fr 350px; gap: 25px; align-items: start;">
                
                <div class="cart-list">
                    <?php 
                    $total_belanja = 0;
                    $jumlah_produk = 0;
                    foreach ($cart as $id => $item): 
                        $subtotal = $item['harga'] * $item['jumlah']; 
                        $total_belanja += $subtotal;
                        $jumlah_produk++;
                    ?>
                        <div class="cart-card-pink">
                            <!-- Checkbox -->
                            <input type="checkbox" class="cart-checkbox" checked>
                            
                            <!-- Gambar (Gunakan fallback jika gambar tidak ada) -->
                            <img src="static/uploads/uploads/<?php echo !empty($item['gambar']) ? htmlspecialchars($item['gambar']) : 'default.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['nama_produk']); ?>" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #eee;">
                            
                            <!-- Info Produk -->
                            <div class="cart-product-info" style="flex: 1;">
                                <h3 style="margin: 0 0 5px 0; font-size: 15px; font-weight: 500; color: #222;">
                                    <?php echo htmlspecialchars($item['nama_produk']); ?>
                                </h3>
                                <div class="text-pink" style="font-weight: 700; font-size: 14px;">
                                    Rp<?php echo number_format($item['harga'], 0, ',', '.'); ?>
                                </div>
                                
                                <!-- Kontrol Kuantitas -->
                                <div class="qty-box">
                                    <a href="index.php?action=cart_decrease&id=<?php echo $id; ?>" class="qty-btn">−</a>
                                    <span class="qty-val"><?php echo (int)$item['jumlah']; ?></span>
                                    <a href="index.php?action=cart_increase&id=<?php echo $id; ?>" class="qty-btn">+</a>
                                </div>
                            </div>

                            <!-- Subtotal & Hapus -->
                            <div style="text-align: right;">
                                <div style="font-weight: 700; font-size: 15px; color: #222; margin-bottom: 15px;">
                                    Rp<?php echo number_format($subtotal, 0, ',', '.'); ?>
                                </div>
                                <a href="index.php?action=cart_remove&id=<?php echo $id; ?>" style="color: #999; font-size: 12px; text-decoration: none;" onclick="return confirm('Hapus produk dari keranjang?')">
                                    Hapus
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Ringkasan Belanja -->
                <aside class="cart-summary" style="padding: 25px; background: white; border: 1px solid #eee; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <h2 style="margin-bottom: 20px; font-size: 18px; color: #222;">Ringkasan Belanja</h2>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #555;">
                        <span>Produk dipilih</span>
                        <span><?php echo $jumlah_produk; ?> produk</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #555;">
                        <span>Subtotal</span>
                        <span>Rp<?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px; color: #555;">
                        <span>Ongkos Kirim</span>
                        <span>Rp0</span>
                    </div>
                    
                    <div style="height: 1px; background: #eee; margin-bottom: 20px;"></div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 16px; font-weight: 700;">
                        <span style="color: #222;">Total</span>
                        <span class="text-pink">Rp<?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                    </div>
                        
                    <a href="index.php?action=checkout" class="btn-checkout-pink">Checkout</a>
                </aside>
            </div>
            
        <?php else: ?>
            <div class="empty-cart" style="text-align: center; padding: 80px 20px; background: white; border: 1px solid #ddd; border-radius: 12px;">
                <div style="font-size: 60px; margin-bottom: 15px;">🛒</div>
                <h2 style="margin-bottom: 10px; font-size: 20px;">Keranjang Anda Kosong</h2>
                <p style="color: #777; margin-bottom: 20px;">Yuk, cari produk favorit Anda sekarang!</p>
                <a href="index.php?action=katalog" style="display: inline-block; padding: 12px 25px; background: #e0145c; color: white; font-weight: 600; border-radius: 8px; text-decoration: none;">Mulai Belanja</a>
            </div>
        <?php endif; ?>
        
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>