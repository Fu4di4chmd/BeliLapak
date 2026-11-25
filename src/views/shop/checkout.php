
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../layouts/header.php';

$isProduction = defined('MIDTRANS_IS_PRODUCTION') && MIDTRANS_IS_PRODUCTION;
$snapJsUrl = $isProduction
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js';
$clientKey = defined('MIDTRANS_CLIENT_KEY') ? MIDTRANS_CLIENT_KEY : '';

?>

<div class="checkout-page">

    <div class="checkout-container">

        <div class="checkout-actions">
            <a href="index.php?action=cart" class="checkout-back">
                ← Kembali ke Keranjang
            </a>
        </div>

        <h1 class="checkout-title">Checkout</h1>

        <form action="index.php?action=process_checkout" method="POST">

            <div class="checkout-grid">

                <!-- BAGIAN KIRI -->
                <div>

                    <!-- ALAMAT PENGIRIMAN -->
                    <div class="checkout-card">
                        <h2>Alamat Pengiriman</h2>

                        <div class="checkout-form-group">
                            <label for="nama_penerima">Nama Penerima</label>
                            <input
                                type="text"
                                id="nama_penerima"
                                name="nama_penerima"
                                value="<?= htmlspecialchars($user['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required
                            >
                        </div>

                        <div class="checkout-form-group">
                            <label for="no_telp_penerima">Nomor Telepon</label>
                            <input
                                type="tel"
                                id="no_telp_penerima"
                                name="no_telp_penerima"
                                value="<?= htmlspecialchars($user['no_telp'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                required
                            >
                        </div>

                        <div class="checkout-form-group">
                            <label for="alamat_pengiriman">Alamat Lengkap</label>
                            <textarea
                                id="alamat_pengiriman"
                                name="alamat_pengiriman"
                                rows="4"
                                required
                            ><?= htmlspecialchars($user['alamat'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>

                    <!-- PENGIRIMAN -->
                    <div class="checkout-card" style="margin-top: 20px;">
                        <h2>Pengiriman (RajaOngkir)</h2>
                        <p style="font-size:13px; color:#666; margin-bottom:14px;">
                            Dikirim dari: <strong>Bangkalan, Jawa Timur</strong> · Total Berat: <strong><?= number_format((int) ($totalWeight ?? 500), 0, ',', '.'); ?> gram</strong>
                        </p>

                        <div class="checkout-form-group" style="position:relative;">
                            <label for="tujuan-search">Kota / Kecamatan / Kode Pos Tujuan</label>
                            <input
                                type="text"
                                id="tujuan-search"
                                placeholder="Ketik minimal 2 huruf (contoh: Kamal, Surabaya, 69162)..."
                                autocomplete="off"
                            >
                            <input type="hidden" id="destination-id" name="destination_id" value="">
                            <input type="hidden" id="destination-label" name="destination_label" value="">
                            <div id="tujuan-results" style="display:none; position:absolute; left:0; right:0; top:100%; z-index:20; background:#fff; border:1px solid #ddd; border-radius:8px; max-height:220px; overflow-y:auto; box-shadow:0 6px 18px rgba(0,0,0,0.08);"></div>
                            <small id="tujuan-status" style="display:block; margin-top:6px; color:#666;"></small>
                        </div>

                        <div class="checkout-form-group">
                            <label for="pilihan-kurir">Pilih Kurir &amp; Layanan</label>

                            <select
                                name="kurir"
                                id="pilihan-kurir"
                                onchange="hitungTotal()"
                                disabled
                                required
                            >
                                <option value="" data-ongkir="0">Pilih tujuan pengiriman terlebih dahulu</option>
                            </select>
                            <small id="kurir-status" style="display:block; margin-top:6px; color:#666;"></small>
                        </div>
                    </div>

                </div>

                <!-- BAGIAN KANAN -->
                <div>

                    <div class="checkout-card">

                        <h2>Ringkasan Pesanan</h2>

                        <!-- PRODUK -->
                        <?php if (!empty($cartItems)): ?>

                            <?php foreach ($cartItems as $item): ?>

                                <div class="checkout-summary-row">
                                    <span>
                                        <?= htmlspecialchars($item['nama_produk'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        × <?= (int) ($item['jumlah'] ?? 0); ?>
                                    </span>

                                    <strong>
                                        Rp<?= number_format(
                                            (float) ($item['harga'] ?? 0) * (int) ($item['jumlah'] ?? 0),
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </strong>
                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p class="checkout-empty">Keranjang masih kosong.</p>

                        <?php endif; ?>

                        <!-- SUBTOTAL -->
                        <div class="checkout-summary-row" style="margin-top: 15px;">
                            <span>Subtotal</span>

                            <strong
                                id="angka-subtotal"
                                data-subtotal="<?= (float) ($subtotal ?? 0); ?>"
                            >
                                Rp<?= number_format($subtotal ?? 0, 0, ',', '.'); ?>
                            </strong>
                        </div>

                        <!-- ONGKIR -->
                        <div class="checkout-summary-row">
                            <span>Ongkos Kirim</span>
                            <strong id="angka-ongkir">Rp0</strong>
                        </div>

                        <!-- TOTAL -->
                        <div class="checkout-summary-row checkout-summary-total">
                            <span>Total</span>
                            <strong id="angka-total">
                                Rp<?= number_format($subtotal ?? 0, 0, ',', '.'); ?>
                            </strong>
                        </div>

                        <!-- TOMBOL CHECKOUT -->
                        <div id="checkout-error-box" style="display:none; padding:10px 12px; border-radius:8px; margin-bottom:14px; background:#ffebee; color:#c62828; font-size:13px;"></div>
                        <div class="checkout-actions">
                            <button
                                type="submit"
                                id="btn-lanjut-pembayaran"
                                class="checkout-button"
                                <?= empty($cartItems) ? 'disabled' : ''; ?>
                            >
                                Lanjut ke Pembayaran
                            </button>
                        </div>

                        <p class="checkout-note">
                            Pastikan alamat dan nomor telepon penerima
                            sudah benar sebelum melanjutkan pesanan.
                        </p>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- PERHITUNGAN ONGKOS KIRIM -->
<script>
function hitungTotal() {
    const kurirSelect = document.getElementById('pilihan-kurir');
    const opsiTerpilih = kurirSelect.options[kurirSelect.selectedIndex];

    const ongkir = opsiTerpilih ? (parseInt(opsiTerpilih.getAttribute('data-ongkir')) || 0) : 0;

    const subtotalElement = document.getElementById('angka-subtotal');
    const subtotal = parseFloat(subtotalElement.getAttribute('data-subtotal')) || 0;

    const totalAkhir = subtotal + ongkir;

    const formatRupiah = (angka) => {
        return 'Rp' + angka.toLocaleString('id-ID');
    };

    document.getElementById('angka-ongkir').innerText = formatRupiah(ongkir);
    document.getElementById('angka-total').innerText = formatRupiah(totalAkhir);
}

(function () {
    var searchInput = document.getElementById('tujuan-search');
    var resultsBox = document.getElementById('tujuan-results');
    var destIdInput = document.getElementById('destination-id');
    var destLabelInput = document.getElementById('destination-label');
    var tujuanStatus = document.getElementById('tujuan-status');
    var kurirSelect = document.getElementById('pilihan-kurir');
    var kurirStatus = document.getElementById('kurir-status');
    if (!searchInput || !resultsBox || !kurirSelect) return;

    var searchTimer = null;

    function resetKurir(placeholderText) {
        kurirSelect.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.setAttribute('data-ongkir', '0');
        opt.textContent = placeholderText;
        kurirSelect.appendChild(opt);
        kurirSelect.disabled = true;
        hitungTotal();
    }

    function loadShippingRates(destinationId) {
        resetKurir('Menghitung ongkos kirim...');
        kurirStatus.style.color = '#666';
        kurirStatus.textContent = 'Mengambil tarif ongkos kirim dari RajaOngkir...';

        var body = new URLSearchParams();
        body.append('destination_id', destinationId);

        fetch('index.php?action=shipping_cost', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            credentials: 'same-origin',
            body: body.toString()
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || !data.ok || !Array.isArray(data.options) || data.options.length === 0) {
                    resetKurir('Layanan kurir tidak tersedia');
                    kurirStatus.style.color = '#c62828';
                    kurirStatus.textContent = (data && data.error) ? data.error : 'Tidak ada layanan kurir tersedia untuk tujuan ini.';
                    return;
                }

                kurirSelect.innerHTML = '';
                var def = document.createElement('option');
                def.value = '';
                def.setAttribute('data-ongkir', '0');
                def.textContent = 'Pilih layanan kurir';
                kurirSelect.appendChild(def);

                data.options.forEach(function (item) {
                    var o = document.createElement('option');
                    o.value = item.key;
                    o.setAttribute('data-ongkir', item.cost);
                    o.textContent = item.label;
                    kurirSelect.appendChild(o);
                });

                kurirSelect.disabled = false;
                kurirStatus.style.color = '#2e7d32';
                kurirStatus.textContent = 'Ditemukan ' + data.options.length + ' layanan pengiriman (' + data.weight + ' gram).';
                hitungTotal();
            })
            .catch(function () {
                resetKurir('Gagal mengambil tarif kurir');
                kurirStatus.style.color = '#c62828';
                kurirStatus.textContent = 'Gagal menghubungi RajaOngkir. Coba pilih tujuan kembali.';
            });
    }

    function renderDestinations(items) {
        resultsBox.innerHTML = '';
        if (!items || items.length === 0) {
            resultsBox.style.display = 'none';
            tujuanStatus.style.color = '#c62828';
            tujuanStatus.textContent = 'Lokasi tidak ditemukan. Coba kata kunci kecamatan/kota lain.';
            return;
        }
        items.forEach(function (loc) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.style.cssText = 'display:block; width:100%; text-align:left; padding:10px 12px; border:none; border-bottom:1px solid #f1f1f1; background:#fff; cursor:pointer; font-size:13px; color:#333;';
            btn.textContent = loc.label;
            btn.addEventListener('mouseenter', function () { btn.style.background = '#fff0f5'; });
            btn.addEventListener('mouseleave', function () { btn.style.background = '#fff'; });
            btn.addEventListener('click', function () {
                destIdInput.value = loc.id;
                destLabelInput.value = loc.label;
                searchInput.value = loc.label;
                resultsBox.style.display = 'none';
                tujuanStatus.style.color = '#2e7d32';
                tujuanStatus.textContent = 'Tujuan dipilih: ' + loc.label;
                loadShippingRates(loc.id);
            });
            resultsBox.appendChild(btn);
        });
        resultsBox.style.display = 'block';
    }

    searchInput.addEventListener('input', function () {
        var q = searchInput.value.trim();
        destIdInput.value = '';
        destLabelInput.value = '';
        resetKurir('Pilih tujuan pengiriman terlebih dahulu');
        kurirStatus.textContent = '';

        if (searchTimer) clearTimeout(searchTimer);
        if (q.length < 2) {
            resultsBox.style.display = 'none';
            tujuanStatus.textContent = '';
            return;
        }

        tujuanStatus.style.color = '#666';
        tujuanStatus.textContent = 'Mencari tujuan...';

        searchTimer = setTimeout(function () {
            fetch('index.php?action=shipping_destinations&q=' + encodeURIComponent(q), {
                credentials: 'same-origin'
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data || !data.ok) {
                        resultsBox.style.display = 'none';
                        tujuanStatus.style.color = '#c62828';
                        tujuanStatus.textContent = (data && data.error) ? data.error : 'Gagal mencari tujuan.';
                        return;
                    }
                    tujuanStatus.textContent = '';
                    renderDestinations(data.data);
                })
                .catch(function () {
                    resultsBox.style.display = 'none';
                    tujuanStatus.style.color = '#c62828';
                    tujuanStatus.textContent = 'Gagal menghubungi layanan RajaOngkir.';
                });
        }, 350);
    });

    document.addEventListener('click', function (e) {
        if (e.target !== searchInput && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
        }
    });
})();
</script>

<script src="<?= htmlspecialchars($snapJsUrl, ENT_QUOTES, 'UTF-8'); ?>" data-client-key="<?= htmlspecialchars($clientKey, ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="/BeliLapak/src/static/js/midtrans-snap.js?v=1"></script>
<script>
(function () {
    var form = document.querySelector('form[action="index.php?action=process_checkout"]');
    var btn = document.getElementById('btn-lanjut-pembayaran');
    var errorBox = document.getElementById('checkout-error-box');
    if (!form || !btn) return;

    var submitting = false;

    function showError(msg) {
        errorBox.style.display = 'block';
        errorBox.textContent = msg;
    }

    function resetBtn() {
        submitting = false;
        btn.disabled = false;
        btn.textContent = 'Lanjut ke Pembayaran';
    }

    form.addEventListener('submit', function (e) {
        if (typeof window.fetch === 'undefined') return;
        e.preventDefault();
        if (submitting) return;
        if (!document.getElementById('destination-id').value) {
            showError('Silakan cari dan pilih kota/kecamatan tujuan pengiriman terlebih dahulu.');
            document.getElementById('tujuan-search').focus();
            return;
        }
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        submitting = true;
        btn.disabled = true;
        btn.textContent = 'Memproses...';
        errorBox.style.display = 'none';

        fetch('index.php?action=process_checkout', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: new FormData(form)
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data || !data.ok) {
                    showError((data && data.error) ? data.error : 'Gagal memproses checkout.');
                    resetBtn();
                    return;
                }
                if (data.already_paid) {
                    window.location.href = 'index.php?action=my_orders&paid=1';
                    return;
                }
                window.BeliLapakMidtrans.openSnap({
                    orderId: data.order_id,
                    midtransOrderId: data.midtrans_order_id,
                    snapToken: data.snap_token,
                    redirectUrl: data.redirect_url,
                    onNotSettledRedirect: 'index.php?action=my_orders',
                    onError: function () {
                        window.location.href = 'index.php?action=my_orders';
                    }
                });
            })
            .catch(function () {
                showError('Terjadi kesalahan jaringan saat menghubungi server.');
                resetBtn();
            });
    });
})();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
