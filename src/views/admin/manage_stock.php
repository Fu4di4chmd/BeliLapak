<?php

$active_page = 'stock';
$page_title = 'Kelola Stok';

require_once __DIR__ . '/../layouts/header_admin.php';

?>

<div class="admin-page-header">
    <h1>Kelola Stok</h1>
    <p>Pantau dan perbarui jumlah stok produk BeliLapak.</p>
</div>


<?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>

    <div class="admin-alert-success">
        Stok produk berhasil diperbarui.
    </div>

<?php endif; ?>


<div class="admin-card">

    <h3>Daftar Stok Produk</h3>

    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>gambar</th>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Stok Saat Ini</th>
                    <th>Ubah Stok</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($products)): ?>

                <?php $no = 1; ?>

                <?php foreach ($products as $produk): ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>
                        <td>
                        <?php if (!empty($produk['gambar'])): ?>

                            <img
                                src="/BeliLapak/src/static/uploads/uploads/<?= htmlspecialchars($produk['gambar']); ?>"
                                alt="<?= htmlspecialchars($produk['nama_produk']); ?>"
                                class="admin-product-image"
                            >

                            <?php else: ?>

                                <span class="admin-no-image">
                                    Tanpa Gambar
                                </span>

                            <?php endif; ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars($produk['nama_produk']); ?>
                            </strong>
                        </td>

                        <td>
                            Rp<?= number_format(
                                $produk['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>
                            <?= (int) $produk['stok']; ?>
                        </td>

                        <td>

                            <form
                                action="index.php?action=update_stock"
                                method="POST"
                                class="stock-form"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= $produk['id']; ?>"
                                >

                                <input
                                    type="number"
                                    name="stok"
                                    min="0"
                                    value="<?= (int) $produk['stok']; ?>"
                                    required
                                >

                                <button
                                    type="submit"
                                    class="admin-btn-primary stock-button"
                                >
                                    Simpan
                                </button>

                            </form>

                        </td>

                        <td>

                            <?php if ((int)$produk['stok'] <= 0): ?>

                                <span class="stock-status stock-empty">
                                    Habis
                                </span>

                            <?php elseif ((int)$produk['stok'] <= 5): ?>

                                <span class="stock-status stock-low">
                                    Menipis
                                </span>

                            <?php else: ?>

                                <span class="stock-status stock-safe">
                                    Tersedia
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" class="admin-empty">
                        Belum ada produk.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


</div>
</main>
</div>

</body>
</html>