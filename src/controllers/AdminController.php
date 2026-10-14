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

    public function index() {
        $products = $this->productModel->getAllProducts();
        require_once __DIR__ . '/../views/admin/manage_products.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_produk = trim($_POST['nama_produk'] ?? '');
            $deskripsi   = trim($_POST['deskripsi'] ?? ''); 
            $harga       = trim($_POST['harga'] ?? 0);
            $stok        = trim($_POST['stok'] ?? 0);
            $berat_gram  = trim($_POST['berat_gram'] ?? 0);
            
            $gambar = '';
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['gambar']['tmp_name'];
                $fileName    = time() . '_' . $_FILES['gambar']['name']; 
                $uploadDir   = __DIR__ . '/../static/uploads/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $destPath = $uploadDir . $fileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $gambar = $fileName;
                }
            }

            $this->productModel->createProduct($nama_produk, $deskripsi, $harga, $stok, $berat_gram, $gambar);
            header("Location: index.php?action=manage_products&status=success");
            exit;
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_produk = trim($_POST['nama_produk'] ?? '');
            $deskripsi   = trim($_POST['deskripsi'] ?? ''); 
            $harga       = trim($_POST['harga'] ?? 0);
            $stok        = trim($_POST['stok'] ?? 0);
            $berat_gram  = trim($_POST['berat_gram'] ?? 0);
            
            $gambar_baru = null;
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $fileName = time() . '_' . $_FILES['gambar']['name'];
                $uploadDir = __DIR__ . '/../static/uploads/';
                $destPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $destPath)) {
                    $gambar_baru = $fileName;
                }
            }

            $this->productModel->updateProduct($id, $nama_produk, $deskripsi, $harga, $stok, $berat_gram, $gambar_baru);
            header("Location: index.php?action=manage_products&status=updated");
            exit;
        }
    }

    public function delete($id) {
        $this->productModel->deleteProduct($id);
        header("Location: index.php?action=manage_products&status=deleted");
        exit;
    }
}
?>