<?php

class ProductModel
{
    private $conn;
    private $table_name = "products";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // AMBIL SEMUA PRODUK
    // =========================
    public function getAllProducts()
    {
        $query = "SELECT *
                  FROM " . $this->table_name . "
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // FILTER & SEARCH PRODUK
    // =========================
    public function getFilteredProducts($search = '', $category = '', $limit = null)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (nama_produk LIKE :search OR deskripsi LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($category)) {
            switch (strtolower($category)) {
                case 'baju':
                case 'kaos':
                    $query .= " AND (nama_produk LIKE '%kaos%' OR nama_produk LIKE '%t-shirt%' OR nama_produk LIKE '%polo%' OR nama_produk LIKE '%baju%' OR nama_produk LIKE '%tunik%' OR nama_produk LIKE '%blouse%')";
                    break;
                case 'kemeja':
                    $query .= " AND (nama_produk LIKE '%kemeja%' OR nama_produk LIKE '%oxford%' OR nama_produk LIKE '%flanel%')";
                    break;
                case 'celana':
                    $query .= " AND (nama_produk LIKE '%celana%' OR nama_produk LIKE '%chino%' OR nama_produk LIKE '%jeans%' OR nama_produk LIKE '%cargo%' OR nama_produk LIKE '%sweatpants%' OR nama_produk LIKE '%jogger%' OR nama_produk LIKE '%ankle%')";
                    break;
                case 'jaket':
                case 'outer':
                    $query .= " AND (nama_produk LIKE '%jaket%' OR nama_produk LIKE '%hoodie%' OR nama_produk LIKE '%sweater%' OR nama_produk LIKE '%cardigan%' OR nama_produk LIKE '%bomber%' OR nama_produk LIKE '%parka%' OR nama_produk LIKE '%blazer%' OR nama_produk LIKE '%rompi%' OR nama_produk LIKE '%vest%')";
                    break;
                case 'batik':
                    $query .= " AND (nama_produk LIKE '%batik%')";
                    break;
                case 'sepatu':
                    $query .= " AND (nama_produk LIKE '%sepatu%' OR nama_produk LIKE '%sneakers%')";
                    break;
                default:
                    $query .= " AND (nama_produk LIKE :cat OR deskripsi LIKE :cat)";
                    $params[':cat'] = '%' . $category . '%';
                    break;
            }
        }

        $query .= " ORDER BY id DESC";

        if ($limit !== null && (int)$limit > 0) {
            $query .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // HITUNG TOTAL PRODUK (UNTUK FILTER/SEARCH)
    // =========================
    public function getTotalProductsCount($search = '', $category = '')
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (nama_produk LIKE :search OR deskripsi LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($category)) {
            switch (strtolower($category)) {
                case 'baju':
                case 'kaos':
                    $query .= " AND (nama_produk LIKE '%kaos%' OR nama_produk LIKE '%t-shirt%' OR nama_produk LIKE '%polo%' OR nama_produk LIKE '%baju%' OR nama_produk LIKE '%tunik%' OR nama_produk LIKE '%blouse%')";
                    break;
                case 'kemeja':
                    $query .= " AND (nama_produk LIKE '%kemeja%' OR nama_produk LIKE '%oxford%' OR nama_produk LIKE '%flanel%')";
                    break;
                case 'celana':
                    $query .= " AND (nama_produk LIKE '%celana%' OR nama_produk LIKE '%chino%' OR nama_produk LIKE '%jeans%' OR nama_produk LIKE '%cargo%' OR nama_produk LIKE '%sweatpants%' OR nama_produk LIKE '%jogger%' OR nama_produk LIKE '%ankle%')";
                    break;
                case 'jaket':
                case 'outer':
                    $query .= " AND (nama_produk LIKE '%jaket%' OR nama_produk LIKE '%hoodie%' OR nama_produk LIKE '%sweater%' OR nama_produk LIKE '%cardigan%' OR nama_produk LIKE '%bomber%' OR nama_produk LIKE '%parka%' OR nama_produk LIKE '%blazer%' OR nama_produk LIKE '%rompi%' OR nama_produk LIKE '%vest%')";
                    break;
                case 'batik':
                    $query .= " AND (nama_produk LIKE '%batik%')";
                    break;
                case 'sepatu':
                    $query .= " AND (nama_produk LIKE '%sepatu%' OR nama_produk LIKE '%sneakers%')";
                    break;
                default:
                    $query .= " AND (nama_produk LIKE :cat OR deskripsi LIKE :cat)";
                    $params[':cat'] = '%' . $category . '%';
                    break;
            }
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    // =========================
    // AMBIL PRODUK BERDASARKAN ID
    // =========================
    public function getProductById($id)
    {
        $query = "SELECT *
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================
    // TAMBAH PRODUK
    // =========================
    public function createProduct(
        $nama,
        $deskripsi,
        $harga,
        $stok,
        $berat,
        $gambar
    ) {
        $query = "INSERT INTO " . $this->table_name . "
                  (
                      nama_produk,
                      deskripsi,
                      harga,
                      stok,
                      berat_gram,
                      gambar
                  )
                  VALUES
                  (
                      :nama,
                      :deskripsi,
                      :harga,
                      :stok,
                      :berat,
                      :gambar
                  )";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':nama', $nama);
        $stmt->bindValue(':deskripsi', $deskripsi);
        $stmt->bindValue(':harga', $harga);
        $stmt->bindValue(':stok', $stok, PDO::PARAM_INT);
        $stmt->bindValue(':berat', $berat, PDO::PARAM_INT);
        $stmt->bindValue(':gambar', $gambar);

        return $stmt->execute();
    }

    // =========================
    // UPDATE PRODUK
    // =========================
    public function updateProduct(
        $id,
        $nama,
        $deskripsi,
        $harga,
        $stok,
        $berat,
        $gambar = null
    ) {
        if ($gambar) {

            $query = "UPDATE " . $this->table_name . "
                      SET
                          nama_produk = :nama,
                          deskripsi = :deskripsi,
                          harga = :harga,
                          stok = :stok,
                          berat_gram = :berat,
                          gambar = :gambar
                      WHERE id = :id";

        } else {

            $query = "UPDATE " . $this->table_name . "
                      SET
                          nama_produk = :nama,
                          deskripsi = :deskripsi,
                          harga = :harga,
                          stok = :stok,
                          berat_gram = :berat
                      WHERE id = :id";
        }

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':nama', $nama);
        $stmt->bindValue(':deskripsi', $deskripsi);
        $stmt->bindValue(':harga', $harga);
        $stmt->bindValue(':stok', $stok, PDO::PARAM_INT);
        $stmt->bindValue(':berat', $berat, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if ($gambar) {
            $stmt->bindValue(':gambar', $gambar);
        }

        return $stmt->execute();
    }

    // =========================
    // HAPUS PRODUK
    // =========================
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // =========================
    // UPDATE STOK SAJA
    // =========================
    public function updateStock($id, $stok)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET stok = :stok
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':stok', $stok, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>