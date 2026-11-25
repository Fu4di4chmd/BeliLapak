# Integrasi Midtrans Sandbox - BeliLapak

## 1. Keamanan API Key
Screenshot kredensial sebelumnya sudah dibagikan. Regenerate Server Key/Signature Secret Key dan Client Key di dashboard Midtrans Sandbox. Gunakan key BARU. Jangan commit `src/config/api_config.php` ke GitHub.

## 2. Pasang SDK
Buka terminal pada folder utama `BeliLapak` (folder yang berisi `index.php` dan folder `src`), lalu jalankan:

```bash
composer require midtrans/midtrans-php
```

Pastikan folder `vendor` dan file `vendor/autoload.php` terbentuk di folder utama project.

## 3. Isi API Key
Edit `src/config/api_config.php`:

- `MIDTRANS_SERVER_KEY`: Server Key Sandbox BARU (bukan Client Key dan bukan Signature Secret Key).
- `MIDTRANS_CLIENT_KEY`: Client Key Sandbox BARU.
- `MIDTRANS_IS_PRODUCTION`: tetap `false`.

Server Key digunakan di backend. Client Key dipakai oleh Snap.js di halaman pembayaran. Jangan masukkan Server Key ke HTML/JavaScript.

## 4. Jalankan
Jalankan Apache dan MySQL dari Laragon. Buka:

`http://localhost/BeliLapak/`

Pastikan database `belilapak_db` sudah dibuat dan tabel `users`, `products`, `orders`, dan `order_details` sudah tersedia sesuai project.

## 5. Alur pembayaran
1. Pembeli login dan memasukkan produk ke keranjang.
2. Pembeli checkout, memilih kurir, lalu lanjut.
3. BeliLapak menyimpan pesanan dengan status pembayaran `pending`.
4. Backend meminta Snap Token menggunakan Server Key.
5. Snap Popup dibuka menggunakan Client Key.
6. Midtrans mengirim webhook ke `src/payment_notification.php` untuk memperbarui status pembayaran.
7. Jika popup ditutup, pembeli dapat membuka Pesanan Saya dan klik **Lanjutkan Pembayaran**.

## 6. Webhook saat localhost
Midtrans tidak dapat mengirim webhook ke alamat `localhost` pada komputer pribadi. Untuk pengujian status otomatis, gunakan HTTPS tunnel seperti ngrok, lalu ubah `MIDTRANS_NOTIFICATION_URL` di `src/config/api_config.php` dan atur URL tersebut pada Dashboard Midtrans > Settings > Configuration > Payment Notification URL.

Untuk pengujian lokal tanpa tunnel, popup pembayaran tetap dapat dicoba, tetapi status di database tidak akan otomatis berubah sampai webhook dapat menjangkau aplikasi.

## 7. Catatan URL
Pada `src/controllers/PaymentController.php`, URL finish saat ini memakai `http://localhost/BeliLapak/src/index.php?action=payment_finish`. Sesuaikan jika nama folder atau domain project berbeda.

## Referensi
- Panduan resmi Snap: https://docs.midtrans.com/docs/snap-snap-integration-guide
- SDK PHP resmi: https://github.com/Midtrans/midtrans-php
