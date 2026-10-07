<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->userModel = new UserModel($db);
    }

    // =========================
    // REGISTER
    // =========================
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        }

        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $alamat = trim($_POST['alamat'] ?? '');
        $no_telp = trim($_POST['no_telp'] ?? '');

        // Validasi data kosong
        if (
            empty($nama) ||
            empty($email) ||
            empty($password) ||
            empty($alamat) ||
            empty($no_telp)
        ) {
            return "Semua data wajib diisi.";
        }

        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Format email tidak valid.";
        }

        // Validasi password
        if (strlen($password) < 6) {
            return "Kata sandi minimal 6 karakter.";
        }

        // Cek email sudah terdaftar atau belum
        if ($this->userModel->findByEmail($email)) {
            return "Email sudah terdaftar.";
        }

        // Simpan user
        $hasil = $this->userModel->register(
            $nama,
            $email,
            $password,
            $alamat,
            $no_telp
        );

        if ($hasil) {
            header("Location: /src/views/auth/login.php?register=success");
            exit;
        }

        return "Registrasi gagal.";
    }

    // =========================
    // LOGIN
    // =========================
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return "Email dan password wajib diisi.";
        }

        $user = $this->userModel->login($email, $password);

        if (!$user) {
            return "Email atau password salah.";
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        header("Location: /src/index.php");
        exit;
    }

    // =========================
    // LOGOUT
    // =========================
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        header("Location: /src/views/auth/login.php");
        exit;
    }
}