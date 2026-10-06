<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class CatalogController {
    private $productModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel = new ProductModel($db);
    }

    // Menampilkan halaman utama katalog (Grid produk)
    public function index() {
        // Mengambil semua data produk aktif dari database
        $produk_list = $this->productModel->getAllProducts();
        
        // Memuat file view dan mengirimkan variabel $produk_list secara otomatis
        require_once __DIR__ . '/../views/shop/catalog.php';
    }

    // Menampilkan detail satu produk secara spesifik (Untuk PPL-05 nanti)
    public function detail($id) {
        $produk = $this->productModel->getProductById($id);
        require_once __DIR__ . '/../views/shop/product_detail.php';
    }
}
?>