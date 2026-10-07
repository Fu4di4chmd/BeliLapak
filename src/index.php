<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Tangkap parameter action dari URL (misal: index.php?action=katalog)
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

    default:
        echo "404 Halaman Tidak Ditemukan";
        break;
    
    
}
?>