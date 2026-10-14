<?php
class ProductModel {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduct($nama, $deskripsi, $harga, $stok, $berat, $gambar) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nama_produk, deskripsi, harga, stok, berat_gram, gambar) 
                  VALUES (:nama, :deskripsi, :harga, :stok, :berat, :gambar)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':harga', $harga);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':berat', $berat);
        $stmt->bindParam(':gambar', $gambar);
        
        return $stmt->execute();
    }

    public function updateProduct($id, $nama, $deskripsi, $harga, $stok, $berat, $gambar = null) {
        if ($gambar) {
            $query = "UPDATE " . $this->table_name . " SET nama_produk=:nama, deskripsi=:deskripsi, harga=:harga, stok=:stok, berat_gram=:berat, gambar=:gambar WHERE id=:id";
        } else {
            $query = "UPDATE " . $this->table_name . " SET nama_produk=:nama, deskripsi=:deskripsi, harga=:harga, stok=:stok, berat_gram=:berat WHERE id=:id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':harga', $harga);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':berat', $berat);
        $stmt->bindParam(':id', $id);
        if ($gambar) { $stmt->bindParam(':gambar', $gambar); }
        
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>