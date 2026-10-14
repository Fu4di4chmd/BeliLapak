<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$action = isset($_GET['action']) ? $_GET['action'] : 'katalog';

switch ($action) {
    case 'katalog':
        require_once 'controllers/CatalogController.php';
        $controller = new CatalogController();
        $controller->index();
        break;
        
    case 'detail':
        require_once 'controllers/CatalogController.php';
        $controller = new CatalogController();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->detail($id);
        break;

    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;
        
    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'admin_dashboard':
        require_once 'views/admin/dashboard.php';
        break;

    case 'manage_products':
        require_once 'controllers/AdminController.php';
        $admin = new AdminController();
        $admin->index();
        break;

    case 'store_product':
        require_once 'controllers/AdminController.php';
        $admin = new AdminController();
        $admin->store();
        break;

    case 'delete_product':
        require_once 'controllers/AdminController.php';
        $admin = new AdminController();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $admin->delete($id);
        break;
    
    case 'cart':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->index();
        break;

    case 'cart_add':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->add($_GET['id']);
        break;

    case 'cart_remove':
        require_once 'controllers/CartController.php';
        $controller = new CartController();
        $controller->remove($_GET['id']);
        break;

    default:
        echo "404 Halaman Tidak Ditemukan";
        break;
}
?>