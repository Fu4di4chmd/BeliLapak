
<?php

$active_page = 'orders';
$page_title = 'Daftar Pesanan';

require_once __DIR__ . '/../layouts/header_admin.php';

?>

<div class="admin-page-header">

    <h1>Daftar Pesanan</h1>

    <p>
        Pantau dan kelola seluruh pesanan pelanggan BeliLapak.
    </p>

</div>

<?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
    <div class="admin-alert success">
        Status pesanan berhasil diperbarui!
    </div>
<?php endif; ?>

<div class="admin-card">

    <h3>Pesanan Pelanggan</h3>

    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Total Belanja</th>
                    <th>Ongkir</th>
                    <th>Kurir</th>
                    <th>Pembayaran</th>
                    <th>Status Pesanan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($orders)): ?>

                <?php $no = 1; ?>

                <?php foreach ($orders as $order): ?>

                    <?php
                    $statusSekarang = strtolower(
                        trim($order['status_pesanan'] ?? '')
                    );
                    ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            #<?= (int) $order['id']; ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars(
                                    $order['nama'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </strong>
                            <br>
                            <small>
                                <?= htmlspecialchars(
                                    $order['email'] ?? '-',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </small>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $order['tanggal_pesanan'] ?? '-',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </td>

                        <td>
                            Rp<?= number_format(
                                (float) $order['total_belanja'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>
                            Rp<?= number_format(
                                (float) $order['ongkos_kirim'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $order['kurir'] ?? '-',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </td>

                        <td>
                            <span class="order-status">
                                <?= htmlspecialchars(
                                    ucfirst(
                                        $order['status_pembayaran'] ?? '-'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </span>
                        </td>

                        <td>
                            <span class="order-status">
                                <?= htmlspecialchars(
                                    ucfirst($statusSekarang),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </span>
                        </td>

                        <td>

                            <form
                                action="index.php?action=update_order_status"
                                method="POST"
                                class="order-update-form"
                            >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $order['id']; ?>"
                                >

                                <select
                                    name="status_pesanan"
                                    required
                                >

                                    <option value="menunggu konfirmasi"
                                        <?= $statusSekarang === 'menunggu konfirmasi' ? 'selected' : ''; ?>>
                                        Menunggu Konfirmasi
                                    </option>

                                    <option value="diproses"
                                        <?= $statusSekarang === 'diproses' ? 'selected' : ''; ?>>
                                        Diproses
                                    </option>

                                    <option value="dikirim"
                                        <?= $statusSekarang === 'dikirim' ? 'selected' : ''; ?>>
                                        Dikirim
                                    </option>

                                    <option value="selesai"
                                        <?= $statusSekarang === 'selesai' ? 'selected' : ''; ?>>
                                        Selesai
                                    </option>

                                    <option value="dibatalkan"
                                        <?= $statusSekarang === 'dibatalkan' ? 'selected' : ''; ?>>
                                        Dibatalkan
                                    </option>

                                </select>

                                <button type="submit" class="admin-btn-primary">
                                    Simpan
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="10" class="admin-empty">
                        Belum ada pesanan pelanggan.
                    </td>
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
