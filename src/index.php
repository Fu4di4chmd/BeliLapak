
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = isset($_GET['action']) ? $_GET['action'] : 'katalog';

switch ($action) {

    // =========================
    // KATALOG DAN DETAIL PRODUK
    // =========================

    case 'katalog':
        require_once __DIR__ . '/controllers/CatalogController.php';
        $controller = new CatalogController();
        $controller->index();
        break;

    case 'detail':
        require_once __DIR__ . '/controllers/CatalogController.php';
        $controller = new CatalogController();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $controller->detail($id);
        break;


    // =========================
    // AUTENTIKASI
    // =========================

    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;


    // =========================
    // ADMIN
    // =========================

    case 'admin_dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;

    case 'manage_products':
        require_once __DIR__ . '/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->index();
        break;

    case 'store_product':
        require_once __DIR__ . '/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->store();
        break;

    case 'delete_product':
        require_once __DIR__ . '/controllers/AdminController.php';
        $admin = new AdminController();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $admin->delete($id);
        break;

    case 'manage_stock':
        require_once __DIR__ . '/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->stock();
        break;

    case 'update_stock':
        require_once __DIR__ . '/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateStock();
        break;

    case 'order_list':
        require_once __DIR__ . '/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->orders();
        break;

    case 'update_order_status':
        require_once __DIR__ . '/controllers/AdminController.php';
        $admin = new AdminController();
        $admin->updateOrderStatus();
        break;


    // =========================
    // KERANJANG
    // =========================

    case 'cart':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->index();
        break;

    case 'cart_add':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $controller->add($id);
        break;

    case 'cart_remove':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $controller->remove($id);
        break;


    // =========================
    // CHECKOUT
    // =========================

    case 'checkout':
        require_once __DIR__ . '/controllers/CheckoutController.php';
        $controller = new CheckoutController();
        $controller->index();
        break;

    case 'process_checkout':
        require_once __DIR__ . '/controllers/CheckoutController.php';
        $controller = new CheckoutController();
        $controller->process();
        break;


    // =========================
    // STATUS PESANAN PEMBELI
    // =========================

    case 'my_orders':
        require_once __DIR__ . '/controllers/OrderController.php';
        $controller = new OrderController();
        $controller->index();
        break;

    case 'order_detail':
        require_once __DIR__ . '/controllers/OrderController.php';

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $controller = new OrderController();
        $controller->detail($id);
        break;


    // =========================
    // PROFILE
    // =========================

    case 'profile':
        require_once __DIR__ . '/views/auth/profile.php';
        break;


    // =========================
    // DEFAULT
    // =========================

    default:
        http_response_code(404);
        echo "404 Halaman Tidak Ditemukan";
        break;
}

?>
