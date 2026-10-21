<?php

class OrderModel
{
    private $conn;
    private $table_name = "orders";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // AMBIL SEMUA PESANAN
    // UNTUK ADMIN
    // =========================
    public function getAllOrders()
    {
        $query = "SELECT 
                    orders.*,
                    users.nama,
                    users.email
                  FROM " . $this->table_name . "
                  INNER JOIN users
                    ON orders.user_id = users.id
                  ORDER BY orders.id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // AMBIL PESANAN BERDASARKAN ID
    // =========================
    public function getOrderById($id)
    {
        $query = "SELECT 
                    orders.*,
                    users.nama,
                    users.email
                  FROM " . $this->table_name . "
                  INNER JOIN users
                    ON orders.user_id = users.id
                  WHERE orders.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================
    // UPDATE STATUS PESANAN
    // =========================
    public function updateStatusPesanan($id, $status)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET status_pesanan = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':status',
            $status
        );

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }

    // =========================
    // UPDATE STATUS PEMBAYARAN
    // =========================
    public function updateStatusPembayaran($id, $status)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET status_pembayaran = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(
            ':status',
            $status
        );

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }
}
?>