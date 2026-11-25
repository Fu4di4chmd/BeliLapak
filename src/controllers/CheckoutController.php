<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/ShippingController.php';

class CheckoutController
{
    private $userModel;
    private $orderModel;
    private $shippingController;

    public function __construct()
    {
        $db = (new Database())->getConnection();
        $this->userModel = new UserModel($db);
        $this->orderModel = new OrderModel($db);
        $this->shippingController = new ShippingController();
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login'); exit;
        }
        $user = $this->userModel->getUserById((int) $_SESSION['user_id']);
        $cartItems = $_SESSION['cart'] ?? [];
        if (!$cartItems) { header('Location: index.php?action=cart'); exit; }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += (float)($item['harga'] ?? 0) * (int)($item['jumlah'] ?? 0);
        }
        $totalWeight = $this->shippingController->calculateCartWeight($cartItems);
        require __DIR__ . '/../views/shop/checkout.php';
    }

    private function sendJsonOrExit($isAjax, $statusCode, array $payload, $fallbackMessage)
    {
        http_response_code($statusCode);
        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($payload);
            exit;
        }
        exit($fallbackMessage);
    }

    public function process()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $isAjax = (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest')
            || str_contains(strtolower($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');

        if (!isset($_SESSION['user_id'])) {
            if ($isAjax) {
                $this->sendJsonOrExit(true, 401, ['ok' => false, 'error' => 'Silakan login terlebih dahulu.'], '');
            }
            header('Location: index.php?action=login'); exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=checkout'); exit;
        }

        $cartItems = $_SESSION['cart'] ?? [];
        if (!$cartItems) {
            if ($isAjax) {
                $this->sendJsonOrExit(true, 400, ['ok' => false, 'error' => 'Keranjang belanja kosong.'], '');
            }
            header('Location: index.php?action=cart'); exit;
        }

        $nama = trim($_POST['nama_penerima'] ?? '');
        $telp = trim($_POST['no_telp_penerima'] ?? '');
        $alamat = trim($_POST['alamat_pengiriman'] ?? '');
        $destinationId = (int) ($_POST['destination_id'] ?? 0);
        $kurirKey = trim($_POST['kurir'] ?? '');
        if ($nama === '' || $telp === '' || $alamat === '') {
            $this->sendJsonOrExit($isAjax, 400, ['ok' => false, 'error' => 'Data penerima belum lengkap.'], 'Data penerima belum lengkap.');
        }
        if ($destinationId <= 0) {
            $this->sendJsonOrExit($isAjax, 400, ['ok' => false, 'error' => 'Pilih kota/kecamatan tujuan pengiriman terlebih dahulu.'], 'Tujuan pengiriman belum dipilih.');
        }
        if ($kurirKey === '') {
            $this->sendJsonOrExit($isAjax, 400, ['ok' => false, 'error' => 'Pilih layanan kurir pengiriman terlebih dahulu.'], 'Kurir belum dipilih.');
        }

        $details = [];
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $productId = (int)($item['product_id'] ?? $item['id'] ?? 0);
            $harga = (int)($item['harga'] ?? 0);
            $jumlah = (int)($item['jumlah'] ?? 0);
            if ($productId <= 0 || $harga <= 0 || $jumlah <= 0) {
                $this->sendJsonOrExit($isAjax, 400, ['ok' => false, 'error' => 'Data keranjang tidak valid.'], 'Data keranjang tidak valid.');
            }
            $details[] = ['product_id'=>$productId, 'harga'=>$harga, 'jumlah'=>$jumlah];
            $subtotal += $harga * $jumlah;
        }

        try {
            $totalWeight = $this->shippingController->calculateCartWeight($cartItems);
            $selectedShipping = $this->shippingController->findShippingOption($destinationId, $totalWeight, $kurirKey);
            if (!$selectedShipping) {
                $this->sendJsonOrExit($isAjax, 400, ['ok' => false, 'error' => 'Layanan kurir tidak valid atau tidak tersedia untuk tujuan ini.'], 'Layanan kurir tidak valid.');
            }

            $ongkir = (int) $selectedShipping['cost'];
            $kurirNama = $selectedShipping['display_name'];
            $total = $subtotal + $ongkir;
            $userId = (int) $_SESSION['user_id'];
            $checkoutSignature = md5(json_encode([$userId, $nama, $telp, $alamat, $destinationId, $kurirKey, $total, $details]));

            $orderId = null;
            if (!empty($_SESSION['checkout_pending_order']['sig'])
                && $_SESSION['checkout_pending_order']['sig'] === $checkoutSignature
                && !empty($_SESSION['checkout_pending_order']['order_id'])) {
                $existing = $this->orderModel->getOrderById((int) $_SESSION['checkout_pending_order']['order_id']);
                if ($existing && (int) $existing['user_id'] === $userId && ($existing['status_pembayaran'] ?? '') !== 'settlement') {
                    $orderId = (int) $existing['id'];
                }
            }

            if (!$orderId) {
                $orderId = (int) $this->orderModel->createOrder(
                    $userId, date('Y-m-d H:i:s'), $total, $ongkir,
                    $kurirNama, 'midtrans', $nama, $telp, $alamat, $details
                );
                $_SESSION['checkout_pending_order'] = [
                    'sig' => $checkoutSignature,
                    'order_id' => $orderId
                ];
            }

            require_once __DIR__ . '/PaymentController.php';
            $payment = (new PaymentController())->prepareOrderPayment($orderId, $userId);
            unset($_SESSION['cart'], $_SESSION['checkout_pending_order']);

            if ($payment['already_paid']) {
                if ($isAjax) {
                    $this->sendJsonOrExit(true, 200, [
                        'ok' => true,
                        'already_paid' => true,
                        'order_id' => $orderId
                    ], '');
                }
                header('Location: index.php?action=my_orders&paid=1');
                exit;
            }

            if ($isAjax) {
                $this->sendJsonOrExit(true, 200, [
                    'ok' => true,
                    'already_paid' => false,
                    'order_id' => $orderId,
                    'midtrans_order_id' => $payment['midtrans_order_id'],
                    'snap_token' => $payment['snap_token'],
                    'redirect_url' => $payment['redirect_url']
                ], '');
            }

            header('Location: ' . $payment['redirect_url']);
            exit;
        } catch (Throwable $e) {
            error_log('Checkout BeliLapak: '.$e->getMessage());
            $this->sendJsonOrExit(
                $isAjax,
                500,
                ['ok' => false, 'error' => 'Gagal memproses checkout: ' . $e->getMessage()],
                'Checkout gagal. Periksa konfigurasi RajaOngkir/Midtrans dan log PHP.'
            );
        }
    }
}
