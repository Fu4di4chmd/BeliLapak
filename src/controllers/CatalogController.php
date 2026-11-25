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
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
        $show = isset($_GET['show']) ? trim($_GET['show']) : '';

        // Beranda default: jika tidak sedang mencari dan tidak memilih kategori tertentu dan bukan 'show=all'
        // Batasi hanya menampilkan 10 produk rekomendasi
        $is_home_default = empty($search) && empty($kategori) && $show !== 'all';
        $limit = $is_home_default ? 10 : null;

        $produk_list = $this->productModel->getFilteredProducts($search, $kategori, $limit);
        $total_count = $this->productModel->getTotalProductsCount($search, $kategori);
        
        // Memuat file view dan mengirimkan variabel ke view
        require_once __DIR__ . '/../views/shop/catalog.php';
    }

    // Menampilkan detail satu produk secara spesifik (Untuk PPL-05 nanti)
    public function detail($id) {
        $produk = $this->productModel->getProductById($id);
        require_once __DIR__ . '/../views/shop/product_detail.php';
    }
}
?>