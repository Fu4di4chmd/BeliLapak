<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class CheckoutController
{
    private $userModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->userModel = new UserModel($db);
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Ambil data pelanggan
        $user = $this->userModel->getUserById($userId);

        // Ambil keranjang dari SESSION
        $cartItems = $_SESSION['cart'] ?? [];

        // Hitung subtotal
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $harga = (float) ($item['harga'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);

            $subtotal += $harga * $jumlah;
        }

        // Ongkir sementara
        $ongkir = 0;

        // Total
        $total = $subtotal + $ongkir;

        require_once __DIR__ . '/../views/shop/checkout.php';
    }
}