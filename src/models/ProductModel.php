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