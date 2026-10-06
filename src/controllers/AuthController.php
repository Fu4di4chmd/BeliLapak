<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Logika validasi login
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $user = $this->userModel->login($email, $password);
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php?action=katalog");
                exit();
            } else {
                $error = "Email atau password salah.";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ambil data POST (nama, email, password)
            // Panggil fungsi register di UserModel
            // Arahkan kembali ke halaman login jika sukses
        } else {
            require_once __DIR__ . '/../views/auth/register.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>