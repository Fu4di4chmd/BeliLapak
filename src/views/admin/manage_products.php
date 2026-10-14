<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - BeliLapak</title>
    <link rel="stylesheet" href="/PPL/BeliLapak/src/static/css/style.css?v=<?= time(); ?>">
</head>
<body class="admin-body">

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">BeliLapak</div>
        <ul class="sidebar-menu">
            <li><a href="index.php?action=admin_dashboard">Dashboard</a></li>
            <li><a href="index.php?action=manage_products" class="active">Kelola Produk</a></li>
            <li><a href="index.php?action=manage_stock">Kelola Stok</a></li>
            <li><a href="index.php?action=order_list">Daftar Pesanan</a></li>
            <li class="logout-item"><a href="index.php?action=logout">Keluar</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h2>Kelola Produk</h2>
            <div class="admin-profile"><span>Halo, <strong>Admin</strong>!</span></div>
        </header>

        <div class="admin-content">
            <div class="admin-card" style="margin-bottom: 20px;">
                <h3 style="margin-top: 0;">Tambah Produk Baru</h3>
                <form action="index.php?action=store_product" method="POST" enctype="multipart/form-data" class="auth-form" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" required>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Deskripsi Produk</label>
                        <textarea name="deskripsi" rows="3" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" required>
                    </div>
                    <div class="form-group">
                        <label>Berat (Gram)</label>
                        <input type="number" name="berat_gram" required>
                    </div>
                    <div class="form-group">
                        <label>Gambar Produk</label>
                        <input type="file" name="gambar" accept="image/*" style="padding: 5px;">
                    </div>
                    <button type="submit" class="btn-primary" style="grid-column: span 2; width: auto; justify-self: start; padding: 10px 20px;">Simpan Produk</button>
                </form>
            </div>

            <div class="admin-card">
                <h3 style="margin-top: 0;">Daftar Produk</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php $no = 1; foreach ($products as $row): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?php if ($row['gambar']): ?>
                                        <img src="/PPL/BeliLapak/src/static/uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="Gambar" width="50" style="border-radius: 4px;">
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 12px;">Tanpa Gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($row['stok']); ?></td>
                                <td>
                                    <a href="index.php?action=delete_product&id=<?= $row['id']; ?>" 
                                       onclick="return confirm('Yakin ingin menghapus produk ini?');"
                                       style="color: #e74c3c; text-decoration: none; font-weight: bold; font-size: 13px;">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #666;">Belum ada produk yang ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>