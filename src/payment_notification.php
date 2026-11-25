<?php
// Webhook Midtrans: endpoint ini harus bisa diakses publik melalui HTTPS.
require_once __DIR__ . '/controllers/PaymentController.php';
try {
    (new PaymentController())->notification();
} catch (Throwable $e) {
    error_log('Midtrans notification: ' . $e->getMessage());
    http_response_code(500);
    echo 'Notification handler error';
}
