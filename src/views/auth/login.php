<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - BeliLapak</title>
    <link rel="stylesheet" href="static/css/style.css?v=<?= time(); ?>">
</head>
<body class="auth-body">

<div class="auth-container">
    <div class="auth-header">
        <div class="brand-logo">BeliLapak</div>
        <h1 class="auth-title">Selamat datang kembali!</h1>
        <p class="auth-subtitle">Masuk untuk melanjutkan aktivitas belanjamu.</p>
    </div>

    <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
        <div class="alert alert-success">
            Registrasi berhasil. Silakan masuk menggunakan akunmu.
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" 
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                   required autocomplete="email">
        </div>

        <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" 
                   required autocomplete="current-password">
        </div>

        <button type="submit" class="btn-primary">Masuk</button>
    </form>

    <div class="auth-footer">
        <span>Belum punya akun?</span>
        <a href="index.php?action=register">Daftar sekarang</a>
    </div>
</div>

</body>
</html>