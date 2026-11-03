
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

class CheckoutController
{
    private $userModel;
    private $orderModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();

        $this->userModel = new UserModel($db);
        $this->orderModel = new OrderModel($db);
    }

    // =================================
    // MENAMPILKAN HALAMAN CHECKOUT
    // =================================
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

        $user = $this->userModel->getUserById($userId);

        $cartItems = $_SESSION['cart'] ?? [];

        if (empty($cartItems)) {
            header("Location: index.php?action=cart");
            exit;
        }

        $subtotal = 0;

        foreach ($cartItems as $item) {
            $harga = (float) ($item['harga'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);

            $subtotal += $harga * $jumlah;
        }

        $ongkir = 0;
        $total = $subtotal + $ongkir;

        require_once __DIR__ . '/../views/shop/checkout.php';
    }

    // =================================
    // MEMPROSES PESANAN
    // =================================
    public function process()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=checkout");
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $cartItems = $_SESSION['cart'] ?? [];

        if (empty($cartItems)) {
            header("Location: index.php?action=cart");
            exit;
        }

        $kurir = trim($_POST['kurir'] ?? '');

        if ($kurir === '') {
            header("Location: index.php?action=checkout&error=kurir");
            exit;
        }

        $details = [];
        $subtotal = 0;

        foreach ($cartItems as $item) {

            $productId = (int) (
                $item['product_id'] ?? $item['id'] ?? 0
            );

            $harga = (float) ($item['harga'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);

            if ($productId <= 0 || $harga < 0 || $jumlah <= 0) {
                exit("Data keranjang tidak valid. Silakan periksa kembali keranjang.");
            }

            $details[] = [
                'product_id' => $productId,
                'harga' => $harga,
                'jumlah' => $jumlah
            ];

            $subtotal += $harga * $jumlah;
        }

        // Ongkos kirim sementara
        $ongkir = 0;

        $total = $subtotal + $ongkir;

        $tanggal = date('Y-m-d H:i:s');

        try {

            $orderId = $this->orderModel->createOrder(
                $userId,
                $tanggal,
                $total,
                $ongkir,
                $kurir,
                $details
            );

            // Kosongkan keranjang setelah pesanan tersimpan
            unset($_SESSION['cart']);

            header(
                "Location: index.php?action=my_orders&success=1"
            );
            exit;

        } catch (Exception $e) {

            error_log("Gagal menyimpan pesanan: " . $e->getMessage());

            exit(
                "Pesanan gagal disimpan. Silakan periksa kembali data pesanan."
            );
        }
    }
}
?>
