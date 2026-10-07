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
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $alamat = trim($_POST['alamat'] ?? '');
            $no_telp = trim($_POST['no_telp'] ?? '');

            // Validasi data kosong
            if (empty($nama) || empty($email) || empty($password) || empty($alamat) || empty($no_telp)) {
                $error = "Semua data wajib diisi.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Validasi email
                $error = "Format email tidak valid.";
            } elseif (strlen($password) < 6) {
                // Validasi password
                $error = "Kata sandi minimal 6 karakter.";
            } elseif ($this->userModel->findByEmail($email)) {
                // Cek email sudah terdaftar atau belum
                $error = "Email sudah terdaftar.";
            } else {
                // Simpan user
                $hasil = $this->userModel->register($nama, $email, $password, $alamat, $no_telp);

                if ($hasil) {
                    header("Location: index.php?action=login&register=success");
                    exit;
                }

                $error = "Registrasi gagal.";
            }
        }

        // Tampilkan view register dan kirim variabel $error
        require_once __DIR__ . '/../views/auth/register.php';
    }

    // =========================
    // LOGIN
    // =========================
    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Email dan password wajib diisi.";
            } else {
                $user = $this->userModel->login($email, $password);

                if (!$user) {
                    $error = "Email atau password salah.";
                } else {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['role'] = $user['role'];

                    header("Location: index.php?action=katalog");
                    exit;
                }
            }
        }

        // Tampilkan view login dan kirim variabel $error
        require_once __DIR__ . '/../views/auth/login.php';
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

        header("Location: index.php?action=login");
        exit;
    }
}