<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BeliLapak - Katalog Produk</title>

    <link rel="stylesheet" href="/BeliLapak/src/static/css/style.css?v=<?= time(); ?>">
</head>

<body>

<div class="app-container">

    <header>

        <!-- LOGO -->
        <a
            href="/BeliLapak/src/index.php?action=katalog"
            class="logo"
        >
            BeliLapak
        </a>

        <!-- SEARCH -->
        <form action="/BeliLapak/src/index.php" method="GET" class="search-box">
            <input type="hidden" name="action" value="katalog">
            <input
                type="text"
                name="q"
                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                placeholder="Cari baju, celana, jaket, sepatu..."
            >
            <button type="submit">
                Cari
            </button>
        </form>

        <!-- NAVIGATION -->
        <div class="nav-links">

            <a href="/BeliLapak/src/index.php?action=cart">
                Keranjang
            </a>

            <a href="/BeliLapak/src/index.php?action=my_orders">
                Pesanan
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>

                <!-- SUDAH LOGIN -->
                <a
                    href="/BeliLapak/src/index.php?action=profile"
                    class="profile-link"
                    title="Profil Saya"
                >
                    <span class="profile-icon">👤</span>
                </a>

            <?php else: ?>

                <!-- BELUM LOGIN -->
                <a href="/BeliLapak/src/index.php?action=login">
                    Masuk
                </a>

                <a href="/BeliLapak/src/index.php?action=register">
                    Daftar
                </a>

            <?php endif; ?>

        </div>

    </header>