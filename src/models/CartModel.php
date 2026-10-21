<?php

class CartModel
{
    private $conn;
    private $table_name = "carts";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Ambil isi keranjang berdasarkan user
    public function getCartByUser($userId)
    {
        $query = "SELECT
                    carts.id,
                    carts.user_id,
                    carts.product_id,
                    carts.jumlah,
                    products.nama_produk,
                    products.harga,
                    products.gambar
                  FROM " . $this->table_name . "
                  INNER JOIN products
                    ON carts.product_id = products.id
                  WHERE carts.user_id = :user_id
                  ORDER BY carts.id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':user_id',
            $userId,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tambah produk ke keranjang
    public function addToCart($userId, $productId, $jumlah = 1)
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (user_id, product_id, jumlah)
                  VALUES
                  (:user_id, :product_id, :jumlah)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':jumlah', $jumlah, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Hapus satu item keranjang
    public function removeFromCart($id)
    {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Kosongkan keranjang user
    public function clearCart($userId)
    {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

?>