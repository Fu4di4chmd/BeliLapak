<?php
$pdo = new PDO('mysql:host=localhost;dbname=belilapak_db', 'root', '');
$stmt = $pdo->query('SELECT id, nama_produk, harga, stok, gambar FROM products');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
