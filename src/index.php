<?php
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

    default:
        echo "404 Halaman Tidak Ditemukan";
        break;
}
?>