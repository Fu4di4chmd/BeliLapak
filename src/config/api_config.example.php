<?php
// Salin file ini menjadi src/config/api_config.php lalu isi dengan API Key Sandbox Anda.
// Jangan pernah mengunggah src/config/api_config.php ke GitHub.

define('MIDTRANS_SERVER_KEY', 'ISI_SERVER_KEY_DARI_BAGIAN_SERVER_KEYS');
define('MIDTRANS_CLIENT_KEY', 'ISI_CLIENT_KEY_SANDBOX');
define('MIDTRANS_SIGNATURE_KEY', 'ISI_SIGNATURE_SECRET_KEY_OPSIONAL');
define('MIDTRANS_IS_PRODUCTION', false);

// Sesuaikan bila folder project berbeda.
define('MIDTRANS_NOTIFICATION_URL', 'http://localhost/BeliLapak/src/payment_notification.php');

// Konfigurasi RajaOngkir (Komerce API v1)
define('RAJAONGKIR_API_KEY', 'ISI_API_KEY_RAJAONGKIR');
define('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1');
define('RAJAONGKIR_ORIGIN_ID', '70196'); // Bangkalan, Jawa Timur
define('RAJAONGKIR_COURIERS', 'jne:jnt:sicepat:pos:tiki');
