<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeliLapak - Katalog Produk</title>
    <!-- Ubah path menjadi absolut dengan awalan garis miring -->
    <!-- Ini kode yang benar -->
    <link rel="stylesheet" href="/PPL/BeliLapak/src/static/css/style.css">
</head>
<body>
<div class="app-container">
    <header>
        <!-- Diubah menjadi jalur absolut -->
        <a href="/PPL/BeliLapak/src/index.php?action=katalog" class="logo">BeliLapak</a>
        
        <div class="search-box">
            <input type="text" placeholder="Cari barang yang kamu suka...">
            <button type="button">Cari</button>
        </div>
        
        <div class="nav-links">
            <!-- Diubah menjadi jalur absolut -->
            <a href="/PPL/BeliLapak/src/index.php?action=cart">Keranjang</a>
            <a href="#">Favorit</a>
            <a href="/PPL/BeliLapak/src/index.php?action=login">Masuk / Daftar</a>
        </div>
    </header>