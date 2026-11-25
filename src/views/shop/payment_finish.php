<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../layouts/header.php';

$statusPembayaran = strtolower((string) ($order['status_pembayaran'] ?? 'pending'));
$labelMap = [
    'settlement' => ['judul' => 'Pembayaran Berhasil!', 'pesan' => 'Pembayaran Anda telah diterima dan dikonfirmasi oleh Midtrans.', 'bg' => '#e8f5e9', 'color' => '#2e7d32'],
    'pending'    => ['judul' => 'Menunggu Pembayaran', 'pesan' => 'Silakan selesaikan pembayaran sesuai instruksi yang dipilih di Midtrans. Halaman ini akan otomatis memperbarui status setelah pembayaran diterima.', 'bg' => '#fff8e1', 'color' => '#f57f17'],
    'expire'     => ['judul' => 'Pembayaran Kedaluwarsa', 'pesan' => 'Batas waktu pembayaran telah habis. Anda dapat membuat pembayaran baru.', 'bg' => '#ffebee', 'color' => '#c62828'],
    'cancel'     => ['judul' => 'Pembayaran Dibatalkan', 'pesan' => 'Transaksi dibatalkan atau ditolak. Anda dapat mencoba membayar ulang.', 'bg' => '#ffebee', 'color' => '#c62828'],
];
$info = $labelMap[$statusPembayaran] ?? $labelMap['pending'];
?>
<div class="checkout-page"><div class="checkout-container">
<h1 class="checkout-title"><?= htmlspecialchars($info['judul'], ENT_QUOTES, 'UTF-8'); ?></h1>
<div class="checkout-card">
    <div style="padding:12px 14px; border-radius:8px; margin-bottom:18px; background:<?= $info['bg']; ?>; color:<?= $info['color']; ?>; font-weight:600;">
        Status Pembayaran: <?= htmlspecialchars(ucfirst($statusPembayaran), ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <p style="margin-bottom:14px;"><?= htmlspecialchars($info['pesan'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php if (!empty($syncWarning)): ?>
        <p style="color:#795548; font-size:13px; margin-bottom:14px;"><?= htmlspecialchars($syncWarning, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if (!empty($order)): ?>
        <div class="checkout-summary-row">
            <span>ID Pesanan</span>
            <strong>#<?= (int) $order['id']; ?></strong>
        </div>
        <div class="checkout-summary-row checkout-summary-total" style="margin-bottom:20px;">
            <span>Total Pembayaran</span>
            <strong>Rp<?= number_format((float) ($order['total_belanja'] ?? 0), 0, ',', '.'); ?></strong>
        </div>
    <?php endif; ?>

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a class="checkout-button" style="text-align:center; text-decoration:none; flex:1;" href="index.php?action=my_orders">Lihat Pesanan Saya</a>
        <?php if (!empty($order) && $statusPembayaran !== 'settlement'): ?>
            <a class="checkout-button" style="text-align:center; text-decoration:none; flex:1; background:#555;" href="index.php?action=pay_order&id=<?= (int) $order['id']; ?>">Lanjutkan Pembayaran</a>
        <?php endif; ?>
    </div>
</div>
</div></div>

<?php if (!empty($order) && $statusPembayaran === 'pending'): ?>
<script>
(function () {
  var orderId = <?= (int) $order['id']; ?>;
  var midtransOrderId = <?= json_encode($midtransOrderId ?? ''); ?>;
  var attempts = 0;
  var timer = setInterval(function () {
    attempts++;
    if (attempts > 100) {
      clearInterval(timer);
      return;
    }
    fetch('index.php?action=poll_payment&id=' + encodeURIComponent(orderId) + '&order_id=' + encodeURIComponent(midtransOrderId), {
      credentials: 'same-origin',
      cache: 'no-store'
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data && data.ok && data.status_pembayaran && data.status_pembayaran !== 'pending') {
          clearInterval(timer);
          window.location.reload();
        }
      })
      .catch(function () {});
  }, 3000);
})();
</script>
<?php endif; ?>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
