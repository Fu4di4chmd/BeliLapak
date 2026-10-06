<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class AdminController {
    private $productModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel = new ProductModel($db);
    }

    // Menampilkan daftar semua produk (Tabel Kelola Produk)
    public function index() {
        $products = $this->productModel->getAllProducts();
        require_once __DIR__ . '/../views/admin/manage_products.php';
    }

    // Memproses data tambah produk baru
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ambil data POST (nama_produk, harga, stok, berat_gram, gambar)
            // Panggil $this->productModel->createProduct(...)
            // Redirect kembali ke index.php?action=admin_produk
        }
    }

    // Memproses update produk (termasuk fitur PPL-03 Kelola Stok nantinya)
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Logika update produk
        }
    }

    // Menghapus produk
    public function delete($id) {
        // Panggil $this->productModel->deleteProduct($id)
        // Redirect kembali ke index.php?action=admin_produk
    }
}
?>