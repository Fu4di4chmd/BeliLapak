<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - BeliLapak</title>
    <link rel="stylesheet" href="static/css/style.css?v=<?= time(); ?>">
</head>
<body class="auth-body" style="padding: 20px 0;">

<div class="auth-container">
    <div class="auth-header">
        <div class="brand-logo">BeliLapak</div>
        <h1 class="auth-title">Buat akun baru</h1>
        <p class="auth-subtitle">Daftar untuk mulai berbelanja produk favoritmu.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=register" class="auth-form">
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" 
                   value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                   required autocomplete="name">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" 
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                   required autocomplete="email">
        </div>

        <div class="form-group">
            <label for="no_telp">No. Telepon</label>
            <input type="tel" id="no_telp" name="no_telp" placeholder="Contoh: 081234567890" 
                   value="<?= isset($_POST['no_telp']) ? htmlspecialchars($_POST['no_telp'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                   required autocomplete="tel">
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" placeholder="Masukkan alamat lengkap" required><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi (min. 6 karakter)" minlength="6" 
                   required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-primary">Daftar</button>
    </form>

    <div class="auth-footer">
        <span>Sudah punya akun?</span>
        <a href="index.php?action=login">Masuk</a>
    </div>
</div>

</body>
</html>