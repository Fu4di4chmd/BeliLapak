
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

class AdminController
{
    private $productModel;
    private $orderModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->productModel = new ProductModel($db);
        $this->orderModel = new OrderModel($db);
    }

    // =========================
    // KELOLA PRODUK
    // =========================
    public function index()
    {
        $products = $this->productModel->getAllProducts();

        require_once __DIR__ . '/../views/admin/manage_products.php';
    }

    // =========================
    // TAMBAH PRODUK
    // =========================
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=manage_products");
            exit;
        }

        $nama_produk = trim($_POST['nama_produk'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = (int) ($_POST['harga'] ?? 0);
        $stok = (int) ($_POST['stok'] ?? 0);
        $berat_gram = (int) ($_POST['berat_gram'] ?? 0);

        $gambar = '';

        if (
            isset($_FILES['gambar']) &&
            $_FILES['gambar']['error'] === UPLOAD_ERR_OK
        ) {
            $fileName = time() . '_' .
                basename($_FILES['gambar']['name']);

            $uploadDir = __DIR__ . '/../static/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (
                move_uploaded_file(
                    $_FILES['gambar']['tmp_name'],
                    $uploadDir . $fileName
                )
            ) {
                $gambar = $fileName;
            }
        }

        $this->productModel->createProduct(
            $nama_produk,
            $deskripsi,
            $harga,
            $stok,
            $berat_gram,
            $gambar
        );

        header("Location: index.php?action=manage_products&status=success");
        exit;
    }

    // =========================
    // UPDATE PRODUK
    // =========================
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=manage_products");
            exit;
        }

        $nama_produk = trim($_POST['nama_produk'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = (int) ($_POST['harga'] ?? 0);
        $stok = (int) ($_POST['stok'] ?? 0);
        $berat_gram = (int) ($_POST['berat_gram'] ?? 0);

        $gambar_baru = null;

        if (
            isset($_FILES['gambar']) &&
            $_FILES['gambar']['error'] === UPLOAD_ERR_OK
        ) {
            $fileName = time() . '_' .
                basename($_FILES['gambar']['name']);

            $uploadDir = __DIR__ . '/../static/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (
                move_uploaded_file(
                    $_FILES['gambar']['tmp_name'],
                    $uploadDir . $fileName
                )
            ) {
                $gambar_baru = $fileName;
            }
        }

        $this->productModel->updateProduct(
            $id,
            $nama_produk,
            $deskripsi,
            $harga,
            $stok,
            $berat_gram,
            $gambar_baru
        );

        header("Location: index.php?action=manage_products&status=updated");
        exit;
    }

    // =========================
    // HAPUS PRODUK
    // =========================
    public function delete($id)
    {
        if ($id > 0) {
            $this->productModel->deleteProduct($id);
        }

        header("Location: index.php?action=manage_products&status=deleted");
        exit;
    }

    // =========================
    // KELOLA STOK
    // =========================
    public function stock()
    {
        $products = $this->productModel->getAllProducts();

        require_once __DIR__ . '/../views/admin/manage_stock.php';
    }

    // =========================
    // UPDATE STOK
    // =========================
    public function updateStock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=manage_stock");
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $stok = (int) ($_POST['stok'] ?? 0);

        if ($id > 0 && $stok >= 0) {
            $this->productModel->updateStock($id, $stok);
        }

        header("Location: index.php?action=manage_stock&status=updated");
        exit;
    }

    // =========================
    // DAFTAR PESANAN
    // =========================
    public function orders()
    {
        $orders = $this->orderModel->getAllOrders();

        require_once __DIR__ . '/../views/admin/order_list.php';
    }

    // =========================
    // UPDATE STATUS PESANAN
    // =========================
    public function updateOrderStatus()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=order_list");
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);

        $status = trim(
            $_POST['status_pesanan'] ?? ''
        );

        $allowedStatus = [
            'diproses',
            'dikirim',
            'selesai'
        ];

        if (
            $id <= 0 ||
            !in_array($status, $allowedStatus, true)
        ) {
            header("Location: index.php?action=order_list&error=invalid");
            exit;
        }

        try {

            $berhasil = $this->orderModel->updateStatusPesanan(
                $id,
                $status
            );

            if ($berhasil) {
                header("Location: index.php?action=order_list&status=updated");
                exit;
            }

            header("Location: index.php?action=order_list&error=failed");
            exit;

        } catch (PDOException $e) {

            error_log(
                "Gagal update status pesanan: " . $e->getMessage()
            );

            header("Location: index.php?action=order_list&error=failed");
            exit;
        }
    }
}

?>
