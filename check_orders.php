<?php
$pdo = new PDO('mysql:host=localhost;dbname=belilapak_db', 'root', '');
$stmt = $pdo->query('DESCRIBE orders');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
