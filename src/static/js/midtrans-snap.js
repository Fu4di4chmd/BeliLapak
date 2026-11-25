window.BeliLapakMidtrans = (function () {
    function buildFinishUrl(orderId, midtransOrderId, result) {
        var resolvedOrderId = (result && result.order_id) ? result.order_id : midtransOrderId;
        var url = 'index.php?action=payment_finish&id=' + encodeURIComponent(orderId)
            + '&order_id=' + encodeURIComponent(resolvedOrderId);
        if (result && result.transaction_id) {
            url += '&transaction_id=' + encodeURIComponent(result.transaction_id);
        }
        return url;
    }

    function openSnap(opts) {
        var orderId = opts.orderId;
        var midtransOrderId = opts.midtransOrderId || '';
        var snapToken = opts.snapToken || '';
        var redirectUrl = opts.redirectUrl || '';
        var onNotSettledRedirect = opts.onNotSettledRedirect || null;
        var onErrorCallback = opts.onError || null;

        if (typeof window.snap === 'undefined' || !snapToken) {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else if (onErrorCallback) {
                onErrorCallback('Popup pembayaran Midtrans tidak tersedia.');
            }
            return;
        }

        var redirecting = false;
        var pollTimer = setInterval(function () {
            if (redirecting) return;
            fetch('index.php?action=poll_payment&id=' + encodeURIComponent(orderId) + '&order_id=' + encodeURIComponent(midtransOrderId), {
                credentials: 'same-origin',
                cache: 'no-store'
            })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    if (d && d.ok && d.status_pembayaran === 'settlement') {
                        redirecting = true;
                        clearInterval(pollTimer);
                        window.location.href = buildFinishUrl(orderId, d.midtrans_order_id || midtransOrderId, null);
                    }
                })
                .catch(function () {});
        }, 3000);

        function goFinish(result) {
            if (redirecting) return;
            redirecting = true;
            clearInterval(pollTimer);
            window.location.href = buildFinishUrl(orderId, midtransOrderId, result);
        }

        window.snap.pay(snapToken, {
            onSuccess: function (result) { goFinish(result); },
            onPending: function (result) { goFinish(result); },
            onError: function () {
                clearInterval(pollTimer);
                if (onErrorCallback) {
                    onErrorCallback('Pembayaran gagal diproses.');
                } else {
                    window.location.href = 'index.php?action=my_orders';
                }
            },
            onClose: function () {
                clearInterval(pollTimer);
                fetch('index.php?action=poll_payment&id=' + encodeURIComponent(orderId) + '&order_id=' + encodeURIComponent(midtransOrderId), {
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                    .then(function (r) { return r.json(); })
                    .then(function (d) {
                        if (d && d.ok && d.status_pembayaran === 'settlement') {
                            goFinish({ order_id: d.midtrans_order_id || midtransOrderId });
                        } else if (onNotSettledRedirect) {
                            window.location.href = onNotSettledRedirect;
                        }
                    })
                    .catch(function () {
                        if (onNotSettledRedirect) {
                            window.location.href = onNotSettledRedirect;
                        }
                    });
            }
        });
    }

    function bindContinuePayButtons() {
        var buttons = document.querySelectorAll('.js-continue-pay');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                if (typeof window.fetch === 'undefined' || typeof window.snap === 'undefined') return;
                e.preventDefault();
                var orderId = btn.getAttribute('data-order-id');
                var originalText = btn.textContent;
                btn.textContent = 'Memuat...';
                btn.style.pointerEvents = 'none';

                fetch('index.php?action=pay_order&id=' + encodeURIComponent(orderId) + '&ajax=1', {
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        btn.textContent = originalText;
                        btn.style.pointerEvents = '';
                        if (!data || !data.ok) {
                            alert((data && data.error) ? data.error : 'Gagal memuat pembayaran.');
                            return;
                        }
                        if (data.already_paid) {
                            window.location.reload();
                            return;
                        }
                        openSnap({
                            orderId: data.order_id,
                            midtransOrderId: data.midtrans_order_id,
                            snapToken: data.snap_token,
                            redirectUrl: data.redirect_url
                        });
                    })
                    .catch(function () {
                        btn.textContent = originalText;
                        btn.style.pointerEvents = '';
                        window.location.href = btn.getAttribute('href');
                    });
            });
        });
    }

    return {
        openSnap: openSnap,
        bindContinuePayButtons: bindContinuePayButtons
    };
})();
