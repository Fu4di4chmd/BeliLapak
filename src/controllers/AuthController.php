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

    public function register()
    {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $alamat = trim($_POST['alamat'] ?? '');
            $no_telp = trim($_POST['no_telp'] ?? '');

            if (empty($nama) || empty($email) || empty($password) || empty($alamat) || empty($no_telp)) {
                $error = "Semua data wajib diisi.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Format email tidak valid.";
            } elseif (strlen($password) < 6) {
                $error = "Kata sandi minimal 6 karakter.";
            } elseif ($this->userModel->findByEmail($email)) {
                $error = "Email sudah terdaftar.";
            } else {
                $hasil = $this->userModel->register($nama, $email, $password, $alamat, $no_telp);
                if ($hasil) {
                    header("Location: index.php?action=login&register=success");
                    exit;
                }
                $error = "Registrasi gagal.";
            }
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Email dan password wajib diisi.";
            } else {
                // Cek ke database
                $user = $this->userModel->login($email, $password);

                if (!$user) {
                    $error = "Email atau password salah.";
                } else {
                    // Simpan sesi login
                    if (session_status() === PHP_SESSION_NONE) { session_start(); }
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['nama'] = $user['nama'];
                    $_SESSION['role'] = $user['role'];

                    // Arahkan otomatis berdasarkan status akun
                    if ($user['role'] === 'admin') {
                        header("Location: index.php?action=admin_dashboard");
                        exit;
                    } else {
                        header("Location: index.php?action=katalog");
                        exit;
                    }
                }
            }
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

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
?>