<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class PaymentController
{
    private $orderModel;
    private $userModel;
    private $midtransLoaded = false;

    public function __construct()
    {
        $db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($db);
        $this->userModel = new UserModel($db);
    }

    private function loadMidtrans()
    {
        if ($this->midtransLoaded) {
            return;
        }

        $autoloadCandidates = [
            dirname(__DIR__, 2) . '/vendor/autoload.php',
            dirname(__DIR__) . '/vendor/autoload.php'
        ];
        foreach ($autoloadCandidates as $autoload) {
            if (file_exists($autoload)) {
                require_once $autoload;
                break;
            }
        }
        if (!class_exists('Midtrans\Config')) {
            throw new Exception('Library Midtrans belum ditemukan. Jalankan composer require midtrans/midtrans-php dari folder BeliLapak.');
        }

        $serverKey = defined('MIDTRANS_SERVER_KEY') ? trim((string) MIDTRANS_SERVER_KEY) : '';
        $clientKey = defined('MIDTRANS_CLIENT_KEY') ? trim((string) MIDTRANS_CLIENT_KEY) : '';
        $isProduction = defined('MIDTRANS_IS_PRODUCTION') ? (bool) MIDTRANS_IS_PRODUCTION : false;

        if ($serverKey === '' || $clientKey === '' || str_starts_with($serverKey, 'ISI_SERVER_KEY') || str_starts_with($clientKey, 'ISI_CLIENT_KEY')) {
            throw new Exception('Isi MIDTRANS_SERVER_KEY pada src/config/api_config.php dengan key dari menu Settings > Access Keys > Server Keys di Midtrans Dashboard.');
        }
        if (preg_match('/\s/', $serverKey) || preg_match('/\s/', $clientKey)) {
            throw new Exception('Server Key atau Client Key Midtrans mengandung spasi. Periksa kembali src/config/api_config.php.');
        }
        if (str_contains($serverKey, '-client-') || str_contains($clientKey, '-server-')) {
            throw new Exception('MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY tertukar di src/config/api_config.php.');
        }
        if ($isProduction && (str_starts_with($serverKey, 'SB-') || str_starts_with($clientKey, 'SB-'))) {
            throw new Exception('MIDTRANS_IS_PRODUCTION bernilai true tetapi API key masih menggunakan key Sandbox (SB-).');
        }

        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$clientKey = $clientKey;
        \Midtrans\Config::$isProduction = $isProduction;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        if (defined('MIDTRANS_NOTIFICATION_URL')) {
            $notifUrl = trim((string) MIDTRANS_NOTIFICATION_URL);
            $host = parse_url($notifUrl, PHP_URL_HOST);
            if ($notifUrl !== '' && str_starts_with($notifUrl, 'https://') && $host && !in_array($host, ['localhost', '127.0.0.1'], true)) {
                \Midtrans\Config::$overrideNotifUrl = $notifUrl;
            }
        }

        $this->midtransLoaded = true;
    }

    private function getSrcBaseUrl()
    {
        if (empty($_SERVER['HTTP_HOST'])) {
            return 'http://localhost/BeliLapak/src';
        }
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $scheme = $isHttps ? 'https' : 'http';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/BeliLapak/src/index.php'));
        $scriptDir = rtrim($scriptDir, '/');
        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $scriptDir;
    }

    private function extractLocalOrderId($externalOrderId)
    {
        if (preg_match('/^BELILAPAK-(\d+)(?:-[A-Za-z0-9]+)?$/', (string) $externalOrderId, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    private function parsePaymentToken($order)
    {
        $raw = trim((string) ($order['payment_token'] ?? ''));
        if ($raw === '') {
            return ['order_id' => null, 'snap_token' => null];
        }
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && !empty($decoded['order_id'])) {
            return [
                'order_id' => (string) $decoded['order_id'],
                'snap_token' => !empty($decoded['snap_token']) ? (string) $decoded['snap_token'] : null
            ];
        }
        if (str_starts_with($raw, 'BELILAPAK-')) {
            return ['order_id' => $raw, 'snap_token' => null];
        }
        return ['order_id' => null, 'snap_token' => null];
    }

    private function getCandidateMidtransOrderIds($order)
    {
        $parsed = $this->parsePaymentToken($order);
        $candidates = [];
        if ($parsed['order_id']) {
            $candidates[] = $parsed['order_id'];
        }
        $localId = (int) ($order['id'] ?? 0);
        if ($localId > 0 && !empty($_SESSION['midtrans_last_order'][$localId])) {
            $candidates[] = (string) $_SESSION['midtrans_last_order'][$localId];
        }
        if ($localId > 0) {
            $candidates[] = 'BELILAPAK-' . $localId;
        }
        return array_values(array_unique($candidates));
    }

    private function mapMidtransStatus($transactionStatus, $fraudStatus = '')
    {
        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus === 'challenge') {
                    return 'pending';
                }
                if ($fraudStatus === 'accept' || $fraudStatus === '') {
                    return 'settlement';
                }
                return 'cancel';
            case 'settlement':
                return 'settlement';
            case 'pending':
                return 'pending';
            case 'expire':
                return 'expire';
            case 'cancel':
            case 'deny':
            case 'failure':
                return 'cancel';
            default:
                return null;
        }
    }

    private function fetchMidtransStatus($lookupId, $snapToken = null)
    {
        if (!empty($snapToken)) {
            try {
                $snapStatusUrl = \Midtrans\Config::getSnapBaseUrl() . '/transactions/' . rawurlencode($snapToken) . '/status';
                $snapResponse = \Midtrans\ApiRequestor::get($snapStatusUrl, \Midtrans\Config::$serverKey, false);
                if (is_object($snapResponse) && !empty($snapResponse->transaction_status)) {
                    return $snapResponse;
                }
            } catch (Throwable $ignored) {
                // Lanjut fallback ke Core API v2 status.
            }
        }

        return \Midtrans\Transaction::status($lookupId);
    }

    private function syncOrderStatusFromMidtrans($order, $midtransOrderId, $snapToken = null)
    {
        $statusResponse = $this->fetchMidtransStatus($midtransOrderId, $snapToken);
        $localId = $this->extractLocalOrderId($statusResponse->order_id ?? $midtransOrderId);
        if ($localId !== (int) $order['id']) {
            return null;
        }
        $grossAmount = (float) ($statusResponse->gross_amount ?? 0);
        if (abs($grossAmount - (float) $order['total_belanja']) > 0.01) {
            return null;
        }
        $mapped = $this->mapMidtransStatus(
            (string) ($statusResponse->transaction_status ?? ''),
            (string) ($statusResponse->fraud_status ?? '')
        );
        if ($mapped !== null) {
            $this->orderModel->updateStatusPembayaran((int) $order['id'], $mapped);
        }
        return $mapped;
    }

    public function syncSingleOrder($order)
    {
        if (empty($order['id']) || ($order['status_pembayaran'] ?? '') === 'settlement') {
            return $order['status_pembayaran'] ?? null;
        }

        try {
            $this->loadMidtrans();
            $parsed = $this->parsePaymentToken($order);
            $snapToken = $parsed['snap_token'] ?? null;

            foreach ($this->getCandidateMidtransOrderIds($order) as $candidateId) {
                try {
                    $tokenForCandidate = ($snapToken && $candidateId === ($parsed['order_id'] ?? '')) ? $snapToken : null;
                    $mapped = $this->syncOrderStatusFromMidtrans($order, $candidateId, $tokenForCandidate);
                    if ($mapped !== null) {
                        return $mapped;
                    }
                } catch (Throwable $ignored) {
                    // Transaksi belum terbentuk di Midtrans atau bukan ID percobaan ini.
                }
            }
        } catch (Throwable $e) {
            error_log('Midtrans syncSingleOrder: ' . $e->getMessage());
        }

        return null;
    }

    public function syncPendingOrders(array $orders, $maxCheck = 10)
    {
        $checked = 0;
        foreach ($orders as $idx => $order) {
            if ($checked >= $maxCheck) {
                break;
            }
            if (($order['metode_pembayaran'] ?? '') !== 'midtrans' || ($order['status_pembayaran'] ?? '') !== 'pending') {
                continue;
            }
            $parsed = $this->parsePaymentToken($order);
            $localId = (int) ($order['id'] ?? 0);
            if (!$parsed['order_id'] && empty($_SESSION['midtrans_last_order'][$localId])) {
                continue;
            }
            $checked++;
            $synced = $this->syncSingleOrder($order);
            if ($synced !== null) {
                $orders[$idx]['status_pembayaran'] = ($synced === 'cancel') ? 'expire' : $synced;
                $refreshed = $this->orderModel->getOrderById($localId);
                if ($refreshed && !empty($refreshed['status_pembayaran'])) {
                    $orders[$idx]['status_pembayaran'] = $refreshed['status_pembayaran'];
                }
            }
        }
        return $orders;
    }

    private function buildItemDetails($order)
    {
        $details = $this->orderModel->getOrderDetails((int) $order['id']);
        if (empty($details)) {
            return [];
        }

        $items = [];
        $sum = 0;
        foreach ($details as $item) {
            $price = (int) round((float) ($item['harga_satuan'] ?? 0));
            $qty = (int) ($item['jumlah'] ?? 0);
            if ($price <= 0 || $qty <= 0) {
                continue;
            }
            $name = trim((string) ($item['nama_produk'] ?? 'Produk BeliLapak'));
            $items[] = [
                'id' => 'PROD-' . (int) ($item['product_id'] ?? 0),
                'price' => $price,
                'quantity' => $qty,
                'name' => mb_substr($name, 0, 50)
            ];
            $sum += $price * $qty;
        }

        $ongkir = (int) round((float) ($order['ongkos_kirim'] ?? 0));
        if ($ongkir > 0) {
            $kurir = trim((string) ($order['kurir'] ?? 'Pengiriman'));
            $items[] = [
                'id' => 'SHIPPING',
                'price' => $ongkir,
                'quantity' => 1,
                'name' => mb_substr('Ongkos Kirim (' . $kurir . ')', 0, 50)
            ];
            $sum += $ongkir;
        }

        if ($sum !== (int) round((float) $order['total_belanja'])) {
            return [];
        }
        return $items;
    }

    private function formatMidtransError(Throwable $e)
    {
        $msg = $e->getMessage();
        if (str_contains($msg, '401') || stripos($msg, 'Unauthorized') !== false) {
            return 'Server Key Midtrans tidak valid atau sudah tidak aktif (401 Unauthorized). Perbarui MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di src/config/api_config.php dari Midtrans Dashboard (Settings > Access Keys).';
        }
        return $msg;
    }

    private function getSnapRedirectBaseUrl()
    {
        $isProduction = defined('MIDTRANS_IS_PRODUCTION') && MIDTRANS_IS_PRODUCTION;
        return $isProduction
            ? 'https://app.midtrans.com/snap/v4/redirection/'
            : 'https://app.sandbox.midtrans.com/snap/v4/redirection/';
    }

    /**
     * Memastikan 1 pesanan hanya memiliki maksimal 1 transaksi pembayaran aktif di Midtrans.
     */
    public function prepareOrderPayment($orderId, $userId)
    {
        $order = $this->orderModel->getOrderById((int) $orderId);
        if (!$order || (int) $order['user_id'] !== (int) $userId) {
            throw new RuntimeException('Pesanan tidak ditemukan.', 404);
        }

        if (($order['status_pembayaran'] ?? '') === 'settlement') {
            return [
                'already_paid' => true,
                'order' => $order,
                'snap_token' => null,
                'midtrans_order_id' => null,
                'redirect_url' => null
            ];
        }

        $this->loadMidtrans();

        $synced = $this->syncSingleOrder($order);
        if ($synced === 'settlement') {
            return [
                'already_paid' => true,
                'order' => $this->orderModel->getOrderById((int) $order['id']),
                'snap_token' => null,
                'midtrans_order_id' => null,
                'redirect_url' => null
            ];
        }

        $parsed = $this->parsePaymentToken($order);
        if (!empty($parsed['order_id']) && !empty($parsed['snap_token']) && !in_array($synced, ['expire', 'cancel'], true)) {
            $midtransOrderId = $parsed['order_id'];
            $snapToken = $parsed['snap_token'];
        } else {
            $midtransOrderId = 'BELILAPAK-' . (int) $order['id'] . '-' . time() . random_int(10, 99);
            $customerDetails = [
                'first_name' => trim((string) ($order['nama_penerima'] ?: $order['nama'] ?: 'Pelanggan')),
            ];
            if (!empty($order['email']) && filter_var($order['email'], FILTER_VALIDATE_EMAIL)) {
                $customerDetails['email'] = $order['email'];
            }
            if (!empty($order['no_telp_penerima'])) {
                $customerDetails['phone'] = trim((string) $order['no_telp_penerima']);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $midtransOrderId,
                    'gross_amount' => (int) round((float) $order['total_belanja'])
                ],
                'customer_details' => $customerDetails,
                'callbacks' => [
                    'finish' => $this->getSrcBaseUrl() . '/index.php?action=payment_finish'
                ]
            ];

            $itemDetails = $this->buildItemDetails($order);
            if (!empty($itemDetails)) {
                $params['item_details'] = $itemDetails;
            }

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $this->orderModel->savePaymentToken((int) $order['id'], $midtransOrderId, $snapToken);
        }

        $_SESSION['midtrans_last_order'][(int) $order['id']] = $midtransOrderId;

        return [
            'already_paid' => false,
            'order' => $order,
            'snap_token' => $snapToken,
            'midtrans_order_id' => $midtransOrderId,
            'redirect_url' => $this->getSnapRedirectBaseUrl() . $snapToken
        ];
    }

    public function pay($orderId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $isAjax = (($_GET['ajax'] ?? '') === '1')
            || (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest');

        if (!isset($_SESSION['user_id'])) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
                http_response_code(401);
                echo json_encode(['ok' => false, 'error' => 'Silakan login terlebih dahulu.']);
                exit;
            }
            header('Location: index.php?action=login');
            exit;
        }

        try {
            $payment = $this->prepareOrderPayment((int) $orderId, (int) $_SESSION['user_id']);
            unset($_SESSION['cart'], $_SESSION['checkout_pending_order']);

            if ($payment['already_paid']) {
                if ($isAjax) {
                    header('Content-Type: application/json; charset=UTF-8');
                    echo json_encode(['ok' => true, 'already_paid' => true, 'order_id' => (int) $orderId]);
                    exit;
                }
                header('Location: index.php?action=my_orders&paid=1');
                exit;
            }

            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'ok' => true,
                    'already_paid' => false,
                    'order_id' => (int) $payment['order']['id'],
                    'midtrans_order_id' => $payment['midtrans_order_id'],
                    'snap_token' => $payment['snap_token'],
                    'redirect_url' => $payment['redirect_url']
                ]);
                exit;
            }

            // Langsung arahkan ke halaman pemilihan metode pembayaran Midtrans tanpa halaman perantara.
            header('Location: ' . $payment['redirect_url']);
            exit;
        } catch (Throwable $e) {
            error_log('Midtrans create token: ' . $e->getMessage());
            $statusCode = ($e->getCode() === 404) ? 404 : 500;
            http_response_code($statusCode);
            $paymentError = $this->formatMidtransError($e);

            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['ok' => false, 'error' => $paymentError]);
                exit;
            }

            $order = $this->orderModel->getOrderById((int) $orderId);
            require __DIR__ . '/../views/shop/payment.php';
        }
    }

    public function retry($orderId)
    {
        $this->pay($orderId);
    }

    public function finish()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $midtransOrderId = trim((string) ($_GET['order_id'] ?? $_GET['midtrans_order_id'] ?? ''));
        $orderId = $this->extractLocalOrderId($midtransOrderId);
        if (!$orderId && isset($_GET['id'])) {
            $orderId = (int) $_GET['id'];
        }

        $order = null;
        $syncWarning = null;

        if ($orderId > 0) {
            $fetched = $this->orderModel->getOrderById($orderId);
            if ($fetched && (!isset($_SESSION['user_id']) || (int) $fetched['user_id'] === (int) $_SESSION['user_id'])) {
                $order = $fetched;
                $parsed = $this->parsePaymentToken($order);
                $snapToken = ($parsed['order_id'] === $midtransOrderId || $midtransOrderId === '') ? ($parsed['snap_token'] ?? null) : null;
                $lookupId = trim((string) ($_GET['transaction_id'] ?? $midtransOrderId));

                if ($lookupId !== '') {
                    try {
                        $this->loadMidtrans();
                        $synced = $this->syncOrderStatusFromMidtrans($order, $lookupId, $snapToken);
                        if ($synced === null) {
                            $this->syncSingleOrder($order);
                        }
                    } catch (Throwable $e) {
                        $this->syncSingleOrder($order);
                    }
                } else {
                    $this->syncSingleOrder($order);
                }
                $order = $this->orderModel->getOrderById($orderId);
                $parsed = $this->parsePaymentToken($order);
                if ($midtransOrderId === '' && !empty($parsed['order_id'])) {
                    $midtransOrderId = $parsed['order_id'];
                }
            }
        }

        require __DIR__ . '/../views/shop/payment_finish.php';
    }

    public function checkStatus($orderId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $order = $this->orderModel->getOrderById((int) $orderId);
        if (!$order || (int) $order['user_id'] !== (int) $_SESSION['user_id']) {
            http_response_code(404);
            exit('Pesanan tidak ditemukan.');
        }

        $this->syncSingleOrder($order);

        $redirect = (($_GET['from'] ?? '') === 'detail')
            ? 'index.php?action=order_detail&id=' . (int) $order['id'] . '&checked=1'
            : 'index.php?action=my_orders&checked=1';
        header('Location: ' . $redirect);
        exit;
    }

    public function pollStatus($orderId)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json; charset=UTF-8');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $order = $this->orderModel->getOrderById((int) $orderId);
        if (!$order || (int) $order['user_id'] !== (int) $_SESSION['user_id']) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Order not found']);
            exit;
        }

        $requestedMidtransId = trim((string) ($_GET['order_id'] ?? ''));
        if ($order['status_pembayaran'] !== 'settlement') {
            $parsed = $this->parsePaymentToken($order);
            if ($requestedMidtransId !== '' && $this->extractLocalOrderId($requestedMidtransId) === (int) $order['id']) {
                try {
                    $this->loadMidtrans();
                    $snapToken = ($parsed['order_id'] === $requestedMidtransId) ? ($parsed['snap_token'] ?? null) : null;
                    $synced = $this->syncOrderStatusFromMidtrans($order, $requestedMidtransId, $snapToken);
                    if ($synced === null) {
                        $this->syncSingleOrder($order);
                    }
                } catch (Throwable $ignored) {
                    $this->syncSingleOrder($order);
                }
            } else {
                $this->syncSingleOrder($order);
            }
            $order = $this->orderModel->getOrderById((int) $order['id']);
        }

        $parsed = $this->parsePaymentToken($order);
        echo json_encode([
            'ok' => true,
            'order_id' => (int) $order['id'],
            'status_pembayaran' => (string) ($order['status_pembayaran'] ?? 'pending'),
            'midtrans_order_id' => $parsed['order_id'] ?? ($requestedMidtransId ?: '')
        ]);
        exit;
    }

    // Dipanggil dari endpoint webhook oleh Midtrans.
    public function notification()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $rawBody = file_get_contents('php://input');
        $raw = json_decode($rawBody ?: '', true);
        if (!is_array($raw) || empty($raw['order_id'])) {
            http_response_code(400);
            exit('Invalid notification payload');
        }

        $this->loadMidtrans();

        $signatureKey = defined('MIDTRANS_SIGNATURE_KEY') ? trim((string) MIDTRANS_SIGNATURE_KEY) : '';
        if (isset($raw['signature_key']) && $signatureKey !== '') {
            $expectedWithSigKey = hash(
                'sha512',
                (string) ($raw['order_id'] ?? '')
                . (string) ($raw['status_code'] ?? '')
                . (string) ($raw['gross_amount'] ?? '')
                . $signatureKey
            );
            $expectedWithServerKey = hash(
                'sha512',
                (string) ($raw['order_id'] ?? '')
                . (string) ($raw['status_code'] ?? '')
                . (string) ($raw['gross_amount'] ?? '')
                . MIDTRANS_SERVER_KEY
            );
            $incomingSig = (string) $raw['signature_key'];
            if (!hash_equals($expectedWithSigKey, $incomingSig) && !hash_equals($expectedWithServerKey, $incomingSig)) {
                http_response_code(403);
                exit('Invalid signature');
            }
        }

        $lookupId = !empty($raw['transaction_id']) ? (string) $raw['transaction_id'] : (string) $raw['order_id'];
        $statusResponse = \Midtrans\Transaction::status($lookupId);

        $externalOrderId = (string) ($statusResponse->order_id ?? $raw['order_id']);
        $orderId = $this->extractLocalOrderId($externalOrderId);
        if (!$orderId) {
            http_response_code(400);
            exit('Invalid order id');
        }

        $order = $this->orderModel->getOrderById($orderId);
        if (!$order || abs((float) ($statusResponse->gross_amount ?? 0) - (float) $order['total_belanja']) > 0.01) {
            http_response_code(400);
            exit('Order validation failed');
        }

        $status = $this->mapMidtransStatus(
            (string) ($statusResponse->transaction_status ?? ''),
            (string) ($statusResponse->fraud_status ?? '')
        );
        if ($status === null) {
            http_response_code(200);
            exit('Ignored');
        }

        $this->orderModel->updateStatusPembayaran($orderId, $status);
        http_response_code(200);
        echo 'OK';
    }
}
