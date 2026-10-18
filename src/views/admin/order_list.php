<?php

$active_page = 'orders';
$page_title = 'Daftar Pesanan';

require_once __DIR__ . '/../layouts/header_admin.php';

?>

<div class="admin-page-header">

    <h1>Daftar Pesanan</h1>

    <p>
        Pantau seluruh pesanan pelanggan BeliLapak.
    </p>

</div>


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
                </tr>

            </thead>

            <tbody>

            <?php if (!empty($orders)): ?>

                <?php $no = 1; ?>

                <?php foreach ($orders as $order): ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            #<?= htmlspecialchars($order['id']); ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars($order['nama']); ?>
                            </strong>

                            <br>

                            <small>
                                <?= htmlspecialchars($order['email']); ?>
                            </small>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $order['tanggal_pesanan']
                            ); ?>
                        </td>

                        <td>
                            Rp<?= number_format(
                                $order['total_belanja'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>
                            Rp<?= number_format(
                                $order['ongkos_kirim'],
                                0,
                                ',',
                                '.'
                            ); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $order['kurir'] ?? '-'
                            ); ?>
                        </td>

                        <td>
                            <span class="order-status">
                                <?= htmlspecialchars(
                                    ucfirst(
                                        $order['status_pembayaran']
                                    )
                                ); ?>
                            </span>
                        </td>

                        <td>
                            <span class="order-status">
                                <?= htmlspecialchars(
                                    ucfirst(
                                        $order['status_pesanan']
                                    )
                                ); ?>
                            </span>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td
                        colspan="9"
                        class="admin-empty"
                    >
                        Belum ada pesanan pelanggan.
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