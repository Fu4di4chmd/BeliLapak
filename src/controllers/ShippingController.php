<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../models/ProductModel.php';

class ShippingController
{
    private $productModel;

    public function __construct()
    {
        $db = (new Database())->getConnection();
        $this->productModel = new ProductModel($db);
    }

    private function getApiKey()
    {
        $key = defined('RAJAONGKIR_API_KEY') ? trim((string) RAJAONGKIR_API_KEY) : '';
        if ($key === '' || str_starts_with($key, 'ISI_API_KEY')) {
            throw new RuntimeException('RAJAONGKIR_API_KEY belum diisi pada src/config/api_config.php.');
        }
        return $key;
    }

    private function getBaseUrl()
    {
        return defined('RAJAONGKIR_BASE_URL')
            ? rtrim((string) RAJAONGKIR_BASE_URL, '/')
            : 'https://rajaongkir.komerce.id/api/v1';
    }

    private function request($method, $path, array $params = [])
    {
        $apiKey = $this->getApiKey();
        $url = $this->getBaseUrl() . '/' . ltrim($path, '/');

        $ch = curl_init();
        $headers = [
            'key: ' . $apiKey,
            'Accept: application/json'
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
        ];

        if ($method === 'POST') {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $options[CURLOPT_URL] = $url;
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        } else {
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            $options[CURLOPT_URL] = $url;
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new RuntimeException('Gagal terhubung ke RajaOngkir: ' . $curlError);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Respons RajaOngkir tidak valid (HTTP ' . $httpCode . ').');
        }

        if ($httpCode >= 400 || (($decoded['meta']['code'] ?? 200) >= 400)) {
            $msg = $decoded['meta']['message'] ?? ('RajaOngkir error HTTP ' . $httpCode);
            throw new RuntimeException($msg);
        }

        return $decoded['data'] ?? [];
    }

    public function calculateCartWeight(array $cartItems)
    {
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $productId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
            $qty = max(1, (int) ($item['jumlah'] ?? 1));
            if ($productId <= 0) {
                continue;
            }
            $product = $this->productModel->getProductById($productId);
            $beratPerUnit = (int) ($product['berat_gram'] ?? 0);
            if ($beratPerUnit <= 0) {
                $beratPerUnit = 500;
            }
            $totalWeight += $beratPerUnit * $qty;
        }
        return max(100, $totalWeight);
    }

    public function fetchDestinations($keyword, $limit = 15)
    {
        $keyword = trim((string) $keyword);
        if (mb_strlen($keyword) < 2) {
            return [];
        }

        $rows = $this->request('GET', 'destination/domestic-destination', [
            'search' => $keyword,
            'limit' => $limit,
            'offset' => 0
        ]);

        $results = [];
        foreach ($rows as $row) {
            if (empty($row['id'])) {
                continue;
            }
            $results[] = [
                'id' => (int) $row['id'],
                'label' => (string) ($row['label'] ?? ''),
                'province_name' => (string) ($row['province_name'] ?? ''),
                'city_name' => (string) ($row['city_name'] ?? ''),
                'district_name' => (string) ($row['district_name'] ?? ''),
                'subdistrict_name' => (string) ($row['subdistrict_name'] ?? ''),
                'zip_code' => (string) ($row['zip_code'] ?? ''),
            ];
        }
        return $results;
    }

    public function fetchShippingOptions($destinationId, $weightGram)
    {
        $destinationId = (int) $destinationId;
        $weightGram = max(1, (int) $weightGram);
        if ($destinationId <= 0) {
            return [];
        }

        $originId = defined('RAJAONGKIR_ORIGIN_ID') ? (string) RAJAONGKIR_ORIGIN_ID : '70196';
        $couriers = defined('RAJAONGKIR_COURIERS') ? (string) RAJAONGKIR_COURIERS : 'jne:jnt:sicepat:pos:tiki';

        $rows = $this->request('POST', 'calculate/domestic-cost', [
            'origin' => $originId,
            'destination' => $destinationId,
            'weight' => $weightGram,
            'courier' => $couriers
        ]);

        $options = [];
        foreach ($rows as $row) {
            $code = strtolower(trim((string) ($row['code'] ?? '')));
            $service = trim((string) ($row['service'] ?? ''));
            $cost = (int) round((float) ($row['cost'] ?? 0));
            if ($code === '' || $service === '' || $cost <= 0) {
                continue;
            }
            // Abaikan layanan kargo jumbo (>130kg/>200kg) untuk paket ringan agar pilihan tetap relevan.
            if ($weightGram < 50000 && (str_contains($service, '<130') || str_contains($service, '>130') || str_contains($service, '>200'))) {
                continue;
            }

            $courierShort = strtoupper($code === 'jnt' ? 'J&T' : $code);
            $etd = trim((string) ($row['etd'] ?? ''));
            $desc = trim((string) ($row['description'] ?? ''));
            $key = $code . '|' . $service;

            $labelParts = [$courierShort . ' ' . $service];
            if ($desc !== '' && stripos($desc, $service) === false) {
                $labelParts[0] .= ' (' . $desc . ')';
            }
            if ($etd !== '') {
                $labelParts[] = 'Est. ' . $etd;
            }

            $options[] = [
                'key' => $key,
                'courier_code' => $code,
                'courier_name' => (string) ($row['name'] ?? $courierShort),
                'service' => $service,
                'description' => $desc,
                'etd' => $etd,
                'cost' => $cost,
                'display_name' => mb_substr($courierShort . ' ' . $service, 0, 50),
                'label' => implode(' - ', $labelParts) . ' - Rp' . number_format($cost, 0, ',', '.')
            ];
        }

        usort($options, function ($a, $b) {
            return $a['cost'] <=> $b['cost'];
        });

        return $options;
    }

    public function findShippingOption($destinationId, $weightGram, $selectedKey)
    {
        $options = $this->fetchShippingOptions($destinationId, $weightGram);
        foreach ($options as $opt) {
            if ($opt['key'] === $selectedKey) {
                return $opt;
            }
        }
        return null;
    }

    public function searchDestination()
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

        $q = trim((string) ($_GET['q'] ?? ''));
        if (mb_strlen($q) < 2) {
            echo json_encode(['ok' => true, 'data' => []]);
            exit;
        }

        try {
            $data = $this->fetchDestinations($q);
            echo json_encode(['ok' => true, 'data' => $data]);
        } catch (Throwable $e) {
            error_log('RajaOngkir destination: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function calculateCost()
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

        $destinationId = (int) ($_POST['destination_id'] ?? $_GET['destination_id'] ?? 0);
        if ($destinationId <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Pilih alamat tujuan terlebih dahulu.']);
            exit;
        }

        $cartItems = $_SESSION['cart'] ?? [];
        $weight = $this->calculateCartWeight($cartItems);

        try {
            $options = $this->fetchShippingOptions($destinationId, $weight);
            echo json_encode([
                'ok' => true,
                'weight' => $weight,
                'options' => $options
            ]);
        } catch (Throwable $e) {
            error_log('RajaOngkir cost: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}
