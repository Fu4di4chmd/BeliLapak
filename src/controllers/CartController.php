<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class CartController
{
    private $productModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $database = new Database();
        $db = $database->getConnection();

        $this->productModel = new ProductModel($db);
    }

    public function index()
    {
        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['harga'] * $item['jumlah'];
        }

        require_once __DIR__ . '/../views/shop/cart.php';
    }

    public function add($id = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Ambil ID dari POST jika tidak ada di parameter URL (karena kita pakai form)
        if (!$id && isset($_POST['id'])) {
            $id = $_POST['id'];
        }

        $produk = $this->productModel->getProductById($id);

        if (!$produk) {
            header('Location: index.php?action=katalog');
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Ambil jumlah dari form, jika tidak ada default ke 1
        $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['jumlah'] += $jumlah;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $produk['id'],
                'nama_produk' => $produk['nama_produk'],
                'harga' => $produk['harga'],
                'gambar' => $produk['gambar'] ?? '',
                'jumlah' => $jumlah
            ];
        }

        // ----------------------------------------------------
        // LOGIKA PENENTUAN ARAH HALAMAN (REDIRECT)
        // ----------------------------------------------------
        $action_type = $_POST['action_type'] ?? 'cart';

        if ($action_type === 'buy_now') {
            // Jika tombol 'Beli Sekarang' ditekan
            header('Location: index.php?action=checkout');
        } else {
            // Jika tombol '+ Keranjang' ditekan
            header('Location: index.php?action=cart');
        }
        exit;
    }

    // Fungsi untuk menghapus 1 produk dari keranjang
    public function remove($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        header('Location: index.php?action=cart');
        exit;
    }

    // Fungsi untuk menambah jumlah produk (+)
    public function increase($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['jumlah']++;
        }

        header('Location: index.php?action=cart');
        exit;
    }

    // Fungsi untuk mengurangi jumlah produk (-)
    public function decrease($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['jumlah'] > 1) {
                $_SESSION['cart'][$id]['jumlah']--;
            } else {
                // Jika jumlah 1 dan dikurangi, maka produk dihapus dari keranjang
                unset($_SESSION['cart'][$id]);
            }
        }

        header('Location: index.php?action=cart');
        exit;
    }

    // Fungsi untuk menghapus beberapa produk sekaligus
    public function removeSelected($ids)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $ids = explode(',', $ids);

        foreach ($ids as $id) {
            $id = (int)$id;

            if (isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
            }
        }
        
        header('Location: index.php?action=cart');
        exit;
    }
}
?>