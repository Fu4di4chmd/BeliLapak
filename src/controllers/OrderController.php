
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/OrderModel.php';

class OrderController
{
    private $orderModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->orderModel = new OrderModel($db);
    }

    // Daftar pesanan pembeli
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $userId = (int) $_SESSION['user_id'];

        $orders = $this->orderModel->getOrdersByUser($userId);

        require_once __DIR__ . '/../views/shop/orders.php';
    }

    // Detail pesanan pembeli
    public function detail($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $userId = (int) $_SESSION['user_id'];

        $order = $this->orderModel->getOrderById($id);

        // Pastikan pesanan milik pembeli yang sedang login
        if (!$order || (int) $order['user_id'] !== $userId) {
            http_response_code(404);
            exit("Pesanan tidak ditemukan.");
        }

        $details = $this->orderModel->getOrderDetails($id);

        require_once __DIR__ . '/../views/shop/order_detail.php';
    }
}
?>
