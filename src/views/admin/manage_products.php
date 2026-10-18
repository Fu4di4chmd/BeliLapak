<?php

$active_page = 'products';
$page_title = 'Kelola Produk';

require_once __DIR__ . '/../layouts/header_admin.php';

?>

<div class="admin-page-header">
    <h1>Kelola Produk</h1>
    <p>Tambah dan kelola produk yang tersedia di BeliLapak.</p>
</div>


<!-- TAMBAH PRODUK -->
<div class="admin-card">

    <h3>Tambah Produk Baru</h3>

    <form
        action="index.php?action=store_product"
        method="POST"
        enctype="multipart/form-data"
        class="admin-product-form"
    >

        <div class="admin-form-grid">

            <div class="admin-form-group admin-form-full">
                <label>Nama Produk</label>

                <input
                    type="text"
                    name="nama_produk"
                    placeholder="Masukkan nama produk"
                    required
                >
            </div>


            <div class="admin-form-group admin-form-full">
                <label>Deskripsi Produk</label>

                <textarea
                    name="deskripsi"
                    rows="4"
                    placeholder="Masukkan deskripsi produk"
                    required
                ></textarea>
            </div>


            <div class="admin-form-group">
                <label>Harga (Rp)</label>

                <input
                    type="number"
                    name="harga"
                    placeholder="Contoh: 150000"
                    min="0"
                    required
                >
            </div>


            <div class="admin-form-group">
                <label>Stok</label>

                <input
                    type="number"
                    name="stok"
                    placeholder="Masukkan stok"
                    min="0"
                    required
                >
            </div>


            <div class="admin-form-group">
                <label>Berat (Gram)</label>

                <input
                    type="number"
                    name="berat_gram"
                    placeholder="Contoh: 500"
                    min="0"
                    required
                >
            </div>


            <div class="admin-form-group">
                <label>Gambar Produk</label>

                <input
                    type="file"
                    name="gambar"
                    accept="image/*"
                >
            </div>

        </div>


        <button
            type="submit"
            class="admin-btn-primary"
        >
            Simpan Produk
        </button>

    </form>

</div>


<!-- DAFTAR PRODUK -->
<div class="admin-card">

    <h3>Daftar Produk</h3>

    <div class="admin-table-wrapper">

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

                <?php
                $no = 1;
                foreach ($products as $row):
                ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>


                        <td>

                            <?php if (!empty($row['gambar'])): ?>

                                <img
                                    src="/BeliLapak/src/static/uploads/uploads/<?= htmlspecialchars($row['gambar']); ?>"
                                    alt="<?= htmlspecialchars($row['nama_produk']); ?>"
                                    class="admin-product-image"
                                >

                            <?php else: ?>

                                <span class="admin-no-image">
                                    Tanpa Gambar
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>
                            <?= htmlspecialchars($row['nama_produk']); ?>
                        </td>


                        <td>
                            Rp<?= number_format(
                                $row['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($row['stok']); ?>
                        </td>


                        <td>

                            <a
                                href="index.php?action=delete_product&id=<?= $row['id']; ?>"
                                class="admin-btn-danger"
                                onclick="return confirm('Yakin ingin menghapus produk ini?');"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>


            <?php else: ?>

                <tr>
                    <td
                        colspan="6"
                        class="admin-empty"
                    >
                        Belum ada produk yang ditambahkan.
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