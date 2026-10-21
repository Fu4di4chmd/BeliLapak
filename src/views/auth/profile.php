<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kalau belum login, arahkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: /BeliLapak/src/index.php?action=login");
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, nama, email, role, alamat, no_telp
          FROM users
          WHERE id = :id
          LIMIT 1";

$stmt = $db->prepare($query);
$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Data pengguna tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil Saya - BeliLapak</title>

    <link rel="stylesheet" href="/BeliLapak/src/static/css/style.css">
</head>

<body>

<div class="auth-container">

    <div class="logo">
        BeliLapak
    </div>

    <div class="auth-header">

        <div class="profile-big-icon">
            👤
        </div>

        <h1 class="auth-title">
            Profil Saya
        </h1>

        <p class="auth-subtitle">
            Informasi akun pelanggan BeliLapak.
        </p>

    </div>


    <div class="profile-info">

        <div class="profile-item">
            <span>Nama</span>

            <strong>
                <?= htmlspecialchars(
                    $user['nama'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>
        </div>


        <div class="profile-item">
            <span>Email</span>

            <strong>
                <?= htmlspecialchars(
                    $user['email'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>
        </div>


        <div class="profile-item">
            <span>No. Telepon</span>

            <strong>
                <?= htmlspecialchars(
                    $user['no_telp'] ?? '-',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>
        </div>


        <div class="profile-item">
            <span>Alamat</span>

            <strong>
                <?= htmlspecialchars(
                    $user['alamat'] ?? '-',
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>
        </div>


        <div class="profile-item">
            <span>Status Akun</span>

            <strong>
                <?= htmlspecialchars(
                    ucfirst($user['role']),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>
        </div>

    </div>


    <div class="profile-actions">

        <a
            href="/BeliLapak/src/index.php?action=katalog"
            class="btn-primary"
        >
            Kembali ke Katalog
        </a>

        <a
            href="/BeliLapak/src/index.php?action=logout"
            class="btn-logout"
        >
            Keluar
        </a>

    </div>

</div>

</body>

</html>