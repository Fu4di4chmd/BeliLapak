<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../layouts/header.php';

$isProduction = defined('MIDTRANS_IS_PRODUCTION') && MIDTRANS_IS_PRODUCTION;
$snapJsUrl = $isProduction
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js';
$clientKey = defined('MIDTRANS_CLIENT_KEY') ? MIDTRANS_CLIENT_KEY : '';
?>
<div class="checkout-page"><div class="checkout-container">
<h1 class="checkout-title">Pembayaran BeliLapak</h1>

<div class="checkout-card">
<?php if (!empty($paymentError)): ?>
    <h2>Pembayaran Midtrans Gagal Dimuat</h2>
    <p style="color:#c62828; margin-bottom:14px; line-height:1.6;">
        <?= htmlspecialchars($paymentError, ENT_QUOTES, 'UTF-8'); ?>
    </p>
    <?php if (!empty($order)): ?>
        <div class="checkout-summary-row">
            <span>ID Pesanan</span>
            <strong>#<?= (int) $order['id']; ?></strong>
        </div>
        <div class="checkout-summary-row checkout-summary-total">
            <span>Total Tagihan</span>
            <strong>Rp<?= number_format((float) ($order['total_belanja'] ?? 0), 0, ',', '.'); ?></strong>
        </div>
    <?php endif; ?>
    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:16px;">
        <a class="checkout-button" style="text-align:center; text-decoration:none; flex:1;" href="index.php?action=my_orders">Lihat Pesanan Saya</a>
        <a class="checkout-button" style="text-align:center; text-decoration:none; flex:1; background:#555;" href="index.php?action=cart">Kembali ke Keranjang</a>
    </div>
<?php else: ?>
    <h2>Ringkasan Tagihan</h2>
    <div class="checkout-summary-row">
        <span>ID Pesanan</span>
        <strong>#<?= (int) $order['id']; ?></strong>
    </div>
    <div class="checkout-summary-row">
        <span>Nama Penerima</span>
        <strong><?= htmlspecialchars($order['nama_penerima'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong>
    </div>
    <div class="checkout-summary-row checkout-summary-total" style="margin-bottom:20px;">
        <span>Total Pembayaran</span>
        <strong>Rp<?= number_format((float) ($order['total_belanja'] ?? 0), 0, ',', '.'); ?></strong>
    </div>

    <p style="margin-bottom:16px;">Klik tombol di bawah untuk memilih metode pembayaran melalui Midtrans<?= $isProduction ? '' : ' Sandbox'; ?>.</p>
    <div id="payment-status-box" style="display:none; padding:12px 14px; border-radius:8px; margin-bottom:16px; background:#fff8e1; color:#795548;"></div>

    <button type="button" id="pay-button" class="checkout-button">Bayar Sekarang</button>
    <div style="margin-top:14px; text-align:center;">
        <a href="index.php?action=my_orders" class="checkout-back" style="margin-bottom:0;">Lihat Pesanan Saya</a>
    </div>
    <p class="checkout-note" style="margin-top:12px;">Status pembayaran akan otomatis diperbarui begitu pembayaran diterima Midtrans.</p>
<?php endif; ?>
</div>
</div></div>

<?php if (empty($paymentError) && !empty($snapToken)): ?>
<script src="<?= htmlspecialchars($snapJsUrl, ENT_QUOTES, 'UTF-8'); ?>" data-client-key="<?= htmlspecialchars($clientKey, ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
(function () {
  var payBtn = document.getElementById('pay-button');
  var statusBox = document.getElementById('payment-status-box');
  var localOrderId = <?= (int) $order['id']; ?>;
  var midtransOrderId = <?= json_encode($midtransOrderId); ?>;
  var pollTimer = null;
  var redirecting = false;

  function showStatus(msg, isError) {
    statusBox.style.display = 'block';
    statusBox.style.background = isError ? '#ffebee' : '#fff8e1';
    statusBox.style.color = isError ? '#c62828' : '#795548';
    statusBox.textContent = msg;
  }

  function goFinish(result) {
    if (redirecting) return;
    redirecting = true;
    if (pollTimer) clearInterval(pollTimer);
    var resolvedOrderId = (result && result.order_id) ? result.order_id : midtransOrderId;
    var url = 'index.php?action=payment_finish&id=' + encodeURIComponent(localOrderId)
      + '&order_id=' + encodeURIComponent(resolvedOrderId);
    if (result && result.transaction_id) {
      url += '&transaction_id=' + encodeURIComponent(result.transaction_id);
    }
    window.location.href = url;
  }

  function pollMidtransStatus(notifyIfPending) {
    if (redirecting) return;
    fetch('index.php?action=poll_payment&id=' + encodeURIComponent(localOrderId) + '&order_id=' + encodeURIComponent(midtransOrderId), {
      credentials: 'same-origin',
      cache: 'no-store'
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data || !data.ok) return;
        if (data.status_pembayaran === 'settlement') {
          goFinish({ order_id: data.midtrans_order_id || midtransOrderId });
        } else if (data.status_pembayaran === 'expire' || data.status_pembayaran === 'cancel') {
          if (pollTimer) clearInterval(pollTimer);
          showStatus('Transaksi telah kedaluwarsa/dibatalkan. Muat ulang halaman untuk membuat pembayaran baru.', true);
        } else if (notifyIfPending) {
          showStatus('Menunggu konfirmasi pembayaran dari Midtrans. Jika kamu sudah membayar, status akan otomatis berubah dalam beberapa detik...', false);
        }
      })
      .catch(function () {});
  }

  function startPolling() {
    if (pollTimer) return;
    pollTimer = setInterval(function () {
      pollMidtransStatus(false);
    }, 3000);
  }

  payBtn.addEventListener('click', function () {
    if (typeof window.snap === 'undefined') {
      showStatus('Snap.js gagal dimuat. Periksa koneksi internet atau MIDTRANS_CLIENT_KEY di src/config/api_config.php.', true);
      return;
    }
    startPolling();
    window.snap.pay(<?= json_encode($snapToken); ?>, {
      onSuccess: function (result) { goFinish(result); },
      onPending: function (result) { goFinish(result); },
      onError: function () {
        showStatus('Pembayaran gagal diproses. Silakan coba kembali.', true);
      },
      onClose: function () {
        pollMidtransStatus(true);
      }
    });
  });
})();
</script>
<?php endif; ?>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
