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

    public function add($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $produk = $this->productModel->getProductById($id);

        if (!$produk) {
            header('Location: index.php?action=katalog');
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['jumlah']++;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $produk['id'],
                'nama_produk' => $produk['nama_produk'],
                'harga' => $produk['harga'],
                'gambar' => $produk['gambar'] ?? '',
                'jumlah' => 1
            ];
        }

        header('Location: index.php?action=cart');
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